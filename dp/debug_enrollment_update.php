<?php
// Debug script để kiểm tra vấn đề cập nhật trạng thái enrollment
header('Content-Type: text/html; charset=utf-8');

// Bật error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Debug: Cập nhật trạng thái đăng ký</h2>";

try {
    session_start();
    require_once 'config.php';
    
    // 1. Kiểm tra session admin
    echo "<h3>1. Kiểm tra session admin</h3>";
    echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
    echo "<p><strong>is_admin:</strong> " . (isset($_SESSION['is_admin']) ? ($_SESSION['is_admin'] ? 'true' : 'false') : 'not set') . "</p>";
    echo "<p><strong>admin_id:</strong> " . ($_SESSION['admin_id'] ?? 'not set') . "</p>";
    echo "<p><strong>admin_username:</strong> " . ($_SESSION['admin_username'] ?? 'not set') . "</p>";
    
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        echo "<p style='color: red;'>❌ Không có quyền admin! Cần đăng nhập admin trước.</p>";
        echo "<p><a href='../admin-login.html'>Đăng nhập admin</a></p>";
    } else {
        echo "<p style='color: green;'>✅ Có quyền admin</p>";
    }
    
    // 2. Kiểm tra database connection
    echo "<h3>2. Kiểm tra database connection</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM enrollments");
    $count = $stmt->fetch()['count'];
    echo "<p style='color: green;'>✅ Database kết nối thành công. Có {$count} đăng ký trong database.</p>";
    
    // 3. Kiểm tra dữ liệu enrollments
    echo "<h3>3. Dữ liệu enrollments hiện tại</h3>";
    $stmt = $pdo->query("SELECT id, course_title, status FROM enrollments ORDER BY id DESC LIMIT 5");
    $enrollments = $stmt->fetchAll();
    
    if (empty($enrollments)) {
        echo "<p style='color: orange;'>⚠️ Không có dữ liệu enrollment nào</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Khóa học</th><th>Trạng thái</th></tr>";
        foreach ($enrollments as $enrollment) {
            echo "<tr>";
            echo "<td>{$enrollment['id']}</td>";
            echo "<td>{$enrollment['course_title']}</td>";
            echo "<td>{$enrollment['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 4. Test cập nhật trạng thái (nếu có quyền admin)
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true && !empty($enrollments)) {
        echo "<h3>4. Test cập nhật trạng thái</h3>";
        
        $testEnrollment = $enrollments[0];
        $testId = $testEnrollment['id'];
        $currentStatus = $testEnrollment['status'];
        $newStatus = $currentStatus === 'pending' ? 'approved' : 'pending';
        
        echo "<p>Thử cập nhật enrollment ID {$testId} từ '{$currentStatus}' thành '{$newStatus}'</p>";
        
        // Simulate PUT request
        $input = [
            'enrollment_id' => $testId,
            'status' => $newStatus
        ];
        
        if (!in_array($newStatus, ['pending', 'approved'])) {
            echo "<p style='color: red;'>❌ Trạng thái không hợp lệ: {$newStatus}</p>";
        } else {
            $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
            if ($stmt->execute([$newStatus, $testId])) {
                echo "<p style='color: green;'>✅ Cập nhật thành công!</p>";
                
                // Kiểm tra kết quả
                $stmt = $pdo->prepare("SELECT status FROM enrollments WHERE id = ?");
                $stmt->execute([$testId]);
                $updatedStatus = $stmt->fetch()['status'];
                echo "<p>Trạng thái mới: <strong>{$updatedStatus}</strong></p>";
                
                // Rollback để không ảnh hưởng dữ liệu
                $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
                $stmt->execute([$currentStatus, $testId]);
                echo "<p style='color: blue;'>🔄 Đã rollback về trạng thái ban đầu</p>";
            } else {
                echo "<p style='color: red;'>❌ Cập nhật thất bại!</p>";
                $errorInfo = $stmt->errorInfo();
                echo "<p>Lỗi: " . $errorInfo[2] . "</p>";
            }
        }
    }
    
    // 5. Kiểm tra API endpoint
    echo "<h3>5. Test API endpoint</h3>";
    echo "<p>Để test API, bạn có thể sử dụng:</p>";
    echo "<pre>";
    echo "curl -X PUT http://localhost/dp/admin_enrollments.php \\\n";
    echo "  -H 'Content-Type: application/json' \\\n";
    echo "  -d '{\"enrollment_id\": 1, \"status\": \"approved\"}'";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h3>❌ Lỗi:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
}
?>