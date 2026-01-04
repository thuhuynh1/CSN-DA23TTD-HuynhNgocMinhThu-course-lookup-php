<?php
// Comprehensive debug file for cancel enrollment
session_start();
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='utf-8'><title>Debug Cancel Enrollment</title></head><body>";
echo "<h1>🔍 Debug Cancel Enrollment</h1>";

// Step 1: Check session
echo "<h2>1. Session Status</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . "<br>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
echo "Full Name: " . ($_SESSION['full_name'] ?? 'NOT SET') . "<br>";
echo "Email: " . ($_SESSION['email'] ?? 'NOT SET') . "<br>";
echo "All Session Data: <pre>" . print_r($_SESSION, true) . "</pre>";

// Step 2: Check database connection
echo "<h2>2. Database Connection</h2>";
try {
    require_once 'config.php';
    echo "✅ Database connection successful<br>";
    
    // Check enrollments table
    $checkTable = $pdo->query("SHOW TABLES LIKE 'enrollments'");
    if ($checkTable->rowCount() > 0) {
        echo "✅ Enrollments table exists<br>";
        
        // Count total enrollments
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM enrollments");
        $total = $stmt->fetch()['total'];
        echo "📊 Total enrollments: " . $total . "<br>";
        
        // Show user's enrollments if logged in
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $userEnrollments = $stmt->fetchAll();
            echo "👤 User's enrollments: " . count($userEnrollments) . "<br>";
            
            if (count($userEnrollments) > 0) {
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
                echo "<tr><th>ID</th><th>Course Title</th><th>Status</th><th>Action</th></tr>";
                foreach ($userEnrollments as $enrollment) {
                    echo "<tr>";
                    echo "<td>" . $enrollment['id'] . "</td>";
                    echo "<td>" . $enrollment['course_title'] . "</td>";
                    echo "<td>" . $enrollment['status'] . "</td>";
                    echo "<td><button onclick='testCancel(" . $enrollment['id'] . ", \"" . addslashes($enrollment['course_title']) . "\")'>Test Cancel</button></td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "❌ Not logged in - cannot show user enrollments<br>";
        }
    } else {
        echo "❌ Enrollments table does not exist<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Step 3: Test API endpoint
echo "<h2>3. API Test</h2>";
if (isset($_SESSION['user_id'])) {
    echo "✅ Ready to test API<br>";
    echo "<div id='apiResult'></div>";
} else {
    echo "❌ Please login first: <a href='../simple_login.html'>Login</a><br>";
}

echo "<script>
function testCancel(enrollmentId, courseTitle) {
    console.log('Testing cancel for enrollment:', enrollmentId, courseTitle);
    
    if (!confirm('Test cancel enrollment for: ' + courseTitle + '?')) {
        return;
    }
    
    fetch('cancel_enrollment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            enrollment_id: enrollmentId
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text(); // Get as text first to see raw response
    })
    .then(text => {
        console.log('Raw response:', text);
        try {
            const data = JSON.parse(text);
            document.getElementById('apiResult').innerHTML = 
                '<h4>✅ API Response:</h4><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        } catch (e) {
            document.getElementById('apiResult').innerHTML = 
                '<h4>❌ Invalid JSON Response:</h4><pre>' + text + '</pre>';
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        document.getElementById('apiResult').innerHTML = 
            '<h4>❌ Network Error:</h4><pre>' + error.toString() + '</pre>';
    });
}
</script>";

echo "</body></html>";
?>