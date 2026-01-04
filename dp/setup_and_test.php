<?php
// Script để setup admin session và test enrollment update
session_start();

// Thiết lập admin session
$_SESSION['is_admin'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'admin';

echo "<h2>🔧 Setup và Test Enrollment Update</h2>";

try {
    require_once 'config.php';
    
    echo "<h3>✅ 1. Admin Session đã được thiết lập</h3>";
    echo "<p>is_admin: " . ($_SESSION['is_admin'] ? 'true' : 'false') . "</p>";
    
    echo "<h3>📊 2. Kiểm tra dữ liệu Enrollments</h3>";
    $stmt = $pdo->query("SELECT id, course_title, status FROM enrollments ORDER BY id DESC LIMIT 5");
    $enrollments = $stmt->fetchAll();
    
    if (empty($enrollments)) {
        echo "<p style='color: orange;'>⚠️ Không có enrollment nào trong database</p>";
        
        // Tạo sample data
        echo "<h4>Tạo sample enrollment...</h4>";
        $stmt = $pdo->prepare("
            INSERT INTO enrollments (user_id, course_title, course_price, course_duration, course_level, course_schedule, course_description, course_features, status) 
            VALUES (1, 'Test Course', 1000000, 6, 'Cơ bản', 'Thứ 2,4,6', 'Test description', '[]', 'pending')
        ");
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Đã tạo sample enrollment</p>";
            $enrollments = $pdo->query("SELECT id, course_title, status FROM enrollments ORDER BY id DESC LIMIT 5")->fetchAll();
        }
    }
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Khóa học</th><th>Trạng thái</th><th>Action</th></tr>";
    foreach ($enrollments as $enrollment) {
        $newStatus = $enrollment['status'] === 'pending' ? 'approved' : 'pending';
        echo "<tr>";
        echo "<td>{$enrollment['id']}</td>";
        echo "<td>{$enrollment['course_title']}</td>";
        echo "<td>{$enrollment['status']}</td>";
        echo "<td><button onclick=\"testUpdate({$enrollment['id']}, '{$newStatus}')\">Update to {$newStatus}</button></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>🧪 3. Test Functions</h3>";
    echo "<button onclick=\"testAPI()\">Test API</button>";
    echo "<button onclick=\"window.location.href='../admin.html'\">Go to Admin Panel</button>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
}
?>

<script>
function testUpdate(enrollmentId, newStatus) {
    console.log('Testing update:', enrollmentId, newStatus);
    
    fetch('quick_fix_enrollment.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            enrollment_id: enrollmentId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Result:', data);
        if (data.success) {
            alert('✅ Cập nhật thành công!');
            location.reload();
        } else {
            alert('❌ Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Có lỗi xảy ra');
    });
}

function testAPI() {
    fetch('quick_fix_enrollment.php')
    .then(response => response.json())
    .then(data => {
        console.log('API Test Result:', data);
        alert('API hoạt động: ' + (data.success ? 'OK' : 'ERROR'));
    })
    .catch(error => {
        console.error('API Error:', error);
        alert('API Error: ' + error.message);
    });
}
</script>