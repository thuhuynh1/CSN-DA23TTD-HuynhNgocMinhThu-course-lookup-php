<?php
echo "<h1>🔍 Simple Debug Test</h1>";

try {
    require_once 'config.php';
    echo "<p style='color: green;'>✅ Config loaded successfully</p>";
    
    // Test database
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM enrollments");
    $count = $stmt->fetch()['count'];
    echo "<p>Enrollments count: <strong>$count</strong></p>";
    
    if ($count == 0) {
        echo "<p style='color: orange;'>⚠️ No enrollments, creating sample...</p>";
        
        // Create user if not exists
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $userCount = $stmt->fetch()['count'];
        
        if ($userCount == 0) {
            $pdo->exec("INSERT INTO users (full_name, email, password) VALUES ('Test User', 'test@example.com', 'password123')");
            echo "<p>✅ Created sample user</p>";
        }
        
        // Create sample enrollments
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_title, course_price, course_duration, course_level, course_schedule, course_description, course_features, status) VALUES (1, ?, 1500000, 6, 'Cơ bản', 'Thứ 2,4,6', 'Test course', '[]', ?)");
        
        $stmt->execute(['Test Course 1', 'pending']);
        $stmt->execute(['Test Course 2', 'approved']);
        
        echo "<p style='color: green;'>✅ Created sample enrollments</p>";
    }
    
    // Show current data
    $stmt = $pdo->query("SELECT e.id, e.course_title, e.status, u.full_name FROM enrollments e JOIN users u ON e.user_id = u.id ORDER BY e.id DESC LIMIT 5");
    $enrollments = $stmt->fetchAll();
    
    echo "<h3>Current Data:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>User</th><th>Course</th><th>Status</th><th>Action</th></tr>";
    foreach ($enrollments as $enrollment) {
        $newStatus = $enrollment['status'] === 'pending' ? 'approved' : 'pending';
        echo "<tr>";
        echo "<td>{$enrollment['id']}</td>";
        echo "<td>{$enrollment['full_name']}</td>";
        echo "<td>{$enrollment['course_title']}</td>";
        echo "<td><strong>{$enrollment['status']}</strong></td>";
        echo "<td><button onclick=\"testUpdate({$enrollment['id']}, '{$newStatus}')\">Update to {$newStatus}</button></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>API Test:</h3>";
    echo "<button onclick=\"testAPI()\">Test API</button>";
    echo "<div id=\"result\"></div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<script>
function testUpdate(id, status) {
    console.log('Testing update:', id, status);
    
    fetch('admin_enrollments_no_auth.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            enrollment_id: id,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Result:', data);
        if (data.success) {
            alert('✅ Success!');
            location.reload();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Network error: ' + error.message);
    });
}

function testAPI() {
    document.getElementById('result').innerHTML = 'Testing...';
    
    fetch('admin_enrollments_no_auth.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        console.log('API Result:', data);
    })
    .catch(error => {
        document.getElementById('result').innerHTML = '<p style="color: red;">Error: ' + error.message + '</p>';
        console.error('API Error:', error);
    });
}
</script>