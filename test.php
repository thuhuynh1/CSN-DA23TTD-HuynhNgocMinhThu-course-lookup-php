<?php
echo "<h1>🎉 Web server hoạt động!</h1>";
echo "<p>Thời gian hiện tại: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP version: " . phpversion() . "</p>";

// Test database connection
try {
    require_once 'dp/config.php';
    echo "<p style='color: green;'>✅ Database kết nối thành công!</p>";
    
    // Quick fix enrollment status
    echo "<h2>Sửa lỗi nhanh:</h2>";
    
    // Check and fix ENUM
    $stmt = $pdo->query("SHOW COLUMNS FROM enrollments LIKE 'status'");
    $column = $stmt->fetch();
    
    if (strpos($column['Type'], 'active') !== false) {
        $pdo->exec("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending', 'approved') DEFAULT 'pending'");
        $pdo->exec("UPDATE enrollments SET status = 'approved' WHERE status IN ('active', 'completed')");
        echo "<p style='color: green;'>✅ Đã sửa cấu trúc database!</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Database đã đúng cấu trúc!</p>";
    }
    
    // Create sample data if needed
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM enrollments");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        // Create user if needed
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $userCount = $stmt->fetch()['count'];
        
        if ($userCount == 0) {
            $pdo->exec("INSERT INTO users (full_name, email, password) VALUES ('Test User', 'test@example.com', 'password123')");
        }
        
        // Create sample enrollments
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_title, course_price, course_duration, course_level, course_schedule, course_description, course_features, status) VALUES (1, ?, 1500000, 6, 'Cơ bản', 'Thứ 2,4,6', 'Test course', '[]', ?)");
        $stmt->execute(['Tiếng Anh Giao Tiếp', 'pending']);
        $stmt->execute(['Tin học Văn phòng', 'approved']);
        
        echo "<p style='color: green;'>✅ Đã tạo dữ liệu mẫu!</p>";
    }
    
    echo "<h3>Các bước tiếp theo:</h3>";
    echo "<ol>";
    echo "<li><a href='dp/set_admin_session_quick.php' target='_blank'>Thiết lập Admin Session</a></li>";
    echo "<li><a href='admin.html' target='_blank'>Mở Admin Panel</a></li>";
    echo "<li>Vào phần 'Đăng ký khóa học' và thử cập nhật trạng thái</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi database: " . $e->getMessage() . "</p>";
    echo "<p><strong>Kiểm tra:</strong></p>";
    echo "<ul>";
    echo "<li>MySQL đã chạy chưa?</li>";
    echo "<li>Database 'user_auth_db' đã tồn tại chưa?</li>";
    echo "<li>File dp/config.php có đúng thông tin kết nối không?</li>";
    echo "</ul>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h1, h2, h3 { color: #333; }
a { color: #1976d2; }
</style>