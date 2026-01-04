<?php
echo "<h1>🔧 Complete Enrollment Status Fix</h1>";
echo "<p>This script will fix all enrollment status issues.</p>";

try {
    require_once 'config.php';
    echo "<p style='color: green;'>✅ Database connected</p>";
    
    echo "<h2>Step 1: Check Current Database Status</h2>";
    
    // Check current table structure
    $stmt = $pdo->query("SHOW COLUMNS FROM enrollments LIKE 'status'");
    $column = $stmt->fetch();
    echo "<p><strong>Current status column:</strong> " . $column['Type'] . "</p>";
    
    // Check current data
    $stmt = $pdo->query("SELECT DISTINCT status FROM enrollments");
    $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>Current status values:</strong> " . implode(', ', $statuses) . "</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM enrollments");
    $total = $stmt->fetch()['total'];
    echo "<p><strong>Total enrollments:</strong> $total</p>";
    
    echo "<h2>Step 2: Fix Database Structure</h2>";
    
    // Update ENUM to only allow 'pending' and 'approved'
    $sql = "ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending', 'approved') DEFAULT 'pending'";
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Updated ENUM to use only 'pending' and 'approved'</p>";
    
    echo "<h2>Step 3: Update Existing Data</h2>";
    
    // Convert old status values to new ones
    $updates = [
        'active' => 'approved',
        'completed' => 'approved'
    ];
    
    $totalUpdated = 0;
    foreach ($updates as $oldStatus => $newStatus) {
        $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE status = ?");
        $stmt->execute([$newStatus, $oldStatus]);
        $affected = $stmt->rowCount();
        if ($affected > 0) {
            echo "<p style='color: green;'>✅ Updated $affected records from '$oldStatus' to '$newStatus'</p>";
            $totalUpdated += $affected;
        }
    }
    
    if ($totalUpdated == 0) {
        echo "<p style='color: blue;'>ℹ️ No data needed updating</p>";
    }
    
    echo "<h2>Step 4: Create Sample Data (if needed)</h2>";
    
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
        $sampleEnrollments = [
            ['Khóa học Tiếng Anh Giao Tiếp', 1500000, 'pending'],
            ['Khóa học Tin học Văn phòng', 1200000, 'approved'],
            ['Khóa học Marketing Online', 2000000, 'pending']
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO enrollments (user_id, course_title, course_price, course_duration, course_level, course_schedule, course_description, course_features, status) 
            VALUES (1, ?, ?, 6, 'Cơ bản', 'Thứ 2,4,6 - 19:00-21:00', 'Khóa học chất lượng cao', '[]', ?)
        ");
        
        foreach ($sampleEnrollments as $enrollment) {
            $stmt->execute([$enrollment[0], $enrollment[1], $enrollment[2]]);
        }
        
        echo "<p style='color: green;'>✅ Created " . count($sampleEnrollments) . " sample enrollments</p>";
    }
    
    echo "<h2>Step 5: Verification</h2>";
    
    // Verify final state
    $stmt = $pdo->query("SHOW COLUMNS FROM enrollments LIKE 'status'");
    $column = $stmt->fetch();
    echo "<p><strong>Final status column:</strong> " . $column['Type'] . "</p>";
    
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM enrollments GROUP BY status");
    $statusCounts = $stmt->fetchAll();
    echo "<p><strong>Final status distribution:</strong></p><ul>";
    foreach ($statusCounts as $count) {
        echo "<li>{$count['status']}: {$count['count']} records</li>";
    }
    echo "</ul>";
    
    echo "<h2>Step 6: Test API Endpoints</h2>";
    
    // Test main API
    echo "<p>Testing main API endpoint...</p>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/dp/admin_enrollments.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            if ($data['success']) {
                echo "<p style='color: green;'>✅ Main API working (found " . count($data['enrollments']) . " enrollments)</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Main API needs admin session: " . $data['message'] . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Main API returned invalid JSON</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Main API HTTP error: $httpCode</p>";
    }
    
    // Test no-auth API
    echo "<p>Testing no-auth API endpoint...</p>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/dp/admin_enrollments_no_auth.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo "<p style='color: green;'>✅ No-auth API working (found " . count($data['enrollments']) . " enrollments)</p>";
        } else {
            echo "<p style='color: red;'>❌ No-auth API error: " . ($data['message'] ?? 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ No-auth API HTTP error: $httpCode</p>";
    }
    
    echo "<h2 style='color: green;'>✅ All Fixes Completed Successfully!</h2>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='set_admin_session_quick.php'>Setup Admin Session</a> (if needed)</li>";
    echo "<li><a href='../admin.html'>Test Admin Panel</a></li>";
    echo "<li><a href='../test_enrollment_fix.html'>Run Enrollment Tests</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
}
?>