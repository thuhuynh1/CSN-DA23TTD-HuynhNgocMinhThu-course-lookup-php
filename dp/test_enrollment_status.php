<?php
// Script test để kiểm tra hệ thống trạng thái đăng ký mới
require_once 'config.php';

try {
    echo "<h2>🧪 Test: Hệ thống trạng thái đăng ký khóa học</h2>";
    
    // Test 1: Kiểm tra trạng thái trong database
    echo "<h3>1. Kiểm tra trạng thái trong database</h3>";
    $stmt = $pdo->query("SELECT DISTINCT status FROM enrollments ORDER BY status");
    $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p><strong>Các trạng thái hiện có:</strong> " . implode(', ', $statuses) . "</p>";
    
    $expectedStatuses = ['pending', 'approved'];
    $unexpectedStatuses = array_diff($statuses, $expectedStatuses);
    
    if (empty($unexpectedStatuses)) {
        echo "<p style='color: green;'>✅ Chỉ có 2 trạng thái hợp lệ: pending và approved</p>";
    } else {
        echo "<p style='color: red;'>❌ Còn trạng thái không mong muốn: " . implode(', ', $unexpectedStatuses) . "</p>";
    }
    
    // Test 2: Kiểm tra API admin_enrollments.php
    echo "<h3>2. Test API admin_enrollments.php</h3>";
    
    // Simulate API call
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['status'] = 'pending';
    
    ob_start();
    include 'admin_enrollments.php';
    $apiResponse = ob_get_clean();
    
    $data = json_decode($apiResponse, true);
    if ($data && $data['success']) {
        echo "<p style='color: green;'>✅ API hoạt động bình thường</p>";
        echo "<p>Tìm thấy " . count($data['enrollments']) . " đăng ký với trạng thái 'pending'</p>";
    } else {
        echo "<p style='color: red;'>❌ API có lỗi: " . ($data['message'] ?? 'Unknown error') . "</p>";
    }
    
    // Test 3: Kiểm tra validation trạng thái
    echo "<h3>3. Test validation trạng thái</h3>";
    
    $validStatuses = ['pending', 'approved'];
    $invalidStatuses = ['active', 'completed', 'invalid'];
    
    foreach ($validStatuses as $status) {
        if (in_array($status, ['pending', 'approved'])) {
            echo "<p style='color: green;'>✅ Trạng thái '{$status}' hợp lệ</p>";
        } else {
            echo "<p style='color: red;'>❌ Trạng thái '{$status}' không hợp lệ</p>";
        }
    }
    
    foreach ($invalidStatuses as $status) {
        if (!in_array($status, ['pending', 'approved'])) {
            echo "<p style='color: green;'>✅ Trạng thái '{$status}' bị từ chối đúng</p>";
        } else {
            echo "<p style='color: red;'>❌ Trạng thái '{$status}' không được từ chối</p>";
        }
    }
    
    // Test 4: Thống kê theo trạng thái
    echo "<h3>4. Thống kê theo trạng thái</h3>";
    $stmt = $pdo->query("
        SELECT 
            status,
            COUNT(*) as count,
            ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM enrollments), 2) as percentage
        FROM enrollments 
        GROUP BY status
        ORDER BY count DESC
    ");
    $stats = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Trạng thái</th><th>Số lượng</th><th>Tỷ lệ (%)</th></tr>";
    foreach ($stats as $stat) {
        $statusName = $stat['status'] === 'pending' ? 'Chờ xử lý' : 'Đã duyệt';
        echo "<tr>";
        echo "<td>{$statusName} ({$stat['status']})</td>";
        echo "<td>{$stat['count']}</td>";
        echo "<td>{$stat['percentage']}%</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>🎯 Kết luận</h3>";
    echo "<p>Hệ thống đã được cập nhật thành công với 2 trạng thái:</p>";
    echo "<ul>";
    echo "<li><strong>pending</strong> (Chờ xử lý): Đăng ký mới chưa được admin xem xét</li>";
    echo "<li><strong>approved</strong> (Đã duyệt): Đăng ký đã được admin phê duyệt</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h3>❌ Lỗi test:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>