<?php
echo "<h1>🔧 Quick Enrollment Status Fix</h1>";
echo "<p>Fixing enrollment status issues...</p>";

try {
    // Include config from dp folder
    require_once 'dp/config.php';
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
    
    echo "<h2>Step 1: Check Current Status</h2>";
    
    // Check current table structure
    $stmt = $pdo->query("SHOW COLUMNS FROM enrollments LIKE 'status'");
    $column = $stmt->fetch();
    echo "<p><strong>Current status column:</strong> " . $column['Type'] . "</p>";
    
    // Check if we have the wrong ENUM values
    if (strpos($column['Type'], 'active') !== false || strpos($column['Type'], 'completed') !== false) {
        echo "<p style='color: orange;'>⚠️ Found incorrect ENUM values, fixing...</p>";
        
        echo "<h2>Step 2: Fix Database Structure</h2>";
        
        // Update ENUM to only allow 'pending' and 'approved'
        $sql = "ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending', 'approved') DEFAULT 'pending'";
        $pdo->exec($sql);
        echo "<p style='color: green;'>✅ Updated ENUM to use 'pending' and 'approved'</p>";
        
        // Convert old status values
        $stmt = $pdo->prepare("UPDATE enrollments SET status = 'approved' WHERE status IN ('active', 'completed')");
        $stmt->execute();
        $affected = $stmt->rowCount();
        if ($affected > 0) {
            echo "<p style='color: green;'>✅ Updated $affected records to use 'approved' status</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Database structure is already correct</p>";
    }
    
    echo "<h2>Step 3: Check Data</h2>";
    
    // Check current data
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM enrollments");
    $total = $stmt->fetch()['total'];
    echo "<p><strong>Total enrollments:</strong> $total</p>";
    
    if ($total == 0) {
        echo "<p style='color: orange;'>⚠️ No enrollments found, creating sample data...</p>";
        
        // Check if we have users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $userCount = $stmt->fetch()['count'];
        
        if ($userCount == 0) {
            // Create sample user
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->execute(['Nguyễn Văn A', 'test@example.com', password_hash('123456', PASSWORD_DEFAULT)]);
            echo "<p style='color: green;'>✅ Created sample user</p>";
        }
        
        // Create sample enrollments
        $stmt = $pdo->prepare("
            INSERT INTO enrollments (user_id, course_title, course_price, course_duration, course_level, course_schedule, course_description, course_features, status) 
            VALUES (1, ?, ?, 6, 'Cơ bản', 'Thứ 2,4,6 - 19:00-21:00', 'Khóa học chất lượng cao', '[]', ?)
        ");
        
        $sampleData = [
            ['Tiếng Anh Giao Tiếp', 1500000, 'pending'],
            ['Tin học Văn phòng', 1200000, 'approved'],
            ['Marketing Online', 2000000, 'pending']
        ];
        
        foreach ($sampleData as $data) {
            $stmt->execute($data);
        }
        
        echo "<p style='color: green;'>✅ Created 3 sample enrollments</p>";
    }
    
    echo "<h2>Step 4: Final Verification</h2>";
    
    // Show final status
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM enrollments GROUP BY status");
    $statusCounts = $stmt->fetchAll();
    echo "<p><strong>Current status distribution:</strong></p><ul>";
    foreach ($statusCounts as $count) {
        echo "<li><strong>{$count['status']}</strong>: {$count['count']} records</li>";
    }
    echo "</ul>";
    
    echo "<h2 style='color: green;'>✅ Fix Completed Successfully!</h2>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='dp/set_admin_session_quick.php' target='_blank'>Setup Admin Session</a></li>";
    echo "<li><a href='admin.html' target='_blank'>Open Admin Panel</a></li>";
    echo "<li>Go to 'Đăng ký khóa học' section and try updating status</li>";
    echo "</ol>";
    
    echo "<p style='background: #e3f2fd; padding: 10px; border-radius: 5px;'>";
    echo "<strong>📝 Note:</strong> The system now uses only 2 statuses:<br>";
    echo "• <strong>pending</strong> = Chờ xử lý<br>";
    echo "• <strong>approved</strong> = Đã duyệt";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Possible solutions:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure your web server (XAMPP/WAMP) is running</li>";
    echo "<li>Check if the database connection is configured correctly in dp/config.php</li>";
    echo "<li>Ensure the database 'user_auth_db' exists</li>";
    echo "</ul>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h1, h2, h3 { color: #333; }
p { margin: 10px 0; }
ul, ol { margin: 10px 0 10px 20px; }
a { color: #1976d2; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>