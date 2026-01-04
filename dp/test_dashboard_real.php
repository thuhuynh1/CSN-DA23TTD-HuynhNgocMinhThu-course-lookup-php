<?php
// Test file để kiểm tra dashboard với dữ liệu thực
require_once 'config.php';

try {
    echo "<h2>🔍 Kiểm tra Dashboard Data</h2>";
    
    // Test kết nối database
    echo "<h3>1. Kết nối Database:</h3>";
    echo "✅ Kết nối thành công<br>";
    
    // Kiểm tra các bảng
    echo "<h3>2. Kiểm tra các bảng:</h3>";
    
    $tables = ['users', 'courses', 'enrollments', 'consultations'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            echo "✅ Bảng <strong>$table</strong>: $count bản ghi<br>";
        } catch (Exception $e) {
            echo "❌ Bảng <strong>$table</strong>: Lỗi - " . $e->getMessage() . "<br>";
        }
    }
    
    // Test API dashboard
    echo "<h3>3. Test API Dashboard:</h3>";
    
    // Gọi API dashboard
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/admin_dashboard.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode<br>";
    echo "Response: <pre>" . htmlspecialchars($response) . "</pre>";
    
    // Parse JSON response
    $data = json_decode($response, true);
    if ($data && $data['success']) {
        echo "<h4>📊 Thống kê Dashboard:</h4>";
        echo "👥 Tổng người dùng: " . $data['stats']['totalUsers'] . "<br>";
        echo "📚 Tổng khóa học: " . $data['stats']['totalCourses'] . "<br>";
        echo "📝 Tổng đăng ký: " . $data['stats']['totalEnrollments'] . "<br>";
        echo "💬 Tổng tư vấn: " . $data['stats']['totalConsultations'] . "<br>";
        
        if (!empty($data['recentActivities'])) {
            echo "<h4>🕒 Hoạt động gần đây:</h4>";
            foreach ($data['recentActivities'] as $activity) {
                echo "- {$activity['user_name']} đăng ký {$activity['course_title']} ({$activity['status']})<br>";
            }
        } else {
            echo "<h4>🕒 Hoạt động gần đây:</h4>Chưa có hoạt động nào<br>";
        }
    } else {
        echo "❌ API Dashboard lỗi: " . ($data['message'] ?? 'Unknown error');
    }
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}
?>