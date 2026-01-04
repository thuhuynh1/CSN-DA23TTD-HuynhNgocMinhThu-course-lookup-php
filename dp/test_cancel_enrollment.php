<?php
// Test file để debug cancel enrollment
session_start();
require_once 'config.php';

echo "<h2>Debug Cancel Enrollment</h2>";

// Test 1: Kiểm tra session
echo "<h3>1. Session Info:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";
echo "Is logged in: " . (isset($_SESSION['user_id']) ? 'Yes' : 'No') . "<br>";

// Test 2: Kiểm tra bảng enrollments
echo "<h3>2. Database Check:</h3>";
try {
    $checkTable = $pdo->query("SHOW TABLES LIKE 'enrollments'");
    if ($checkTable->rowCount() > 0) {
        echo "✅ Bảng enrollments tồn tại<br>";
        
        // Kiểm tra dữ liệu trong bảng
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM enrollments");
        $total = $stmt->fetch()['total'];
        echo "📊 Tổng số đăng ký: " . $total . "<br>";
        
        // Hiển thị một số record mẫu
        if ($total > 0) {
            echo "<h4>Dữ liệu mẫu:</h4>";
            $stmt = $pdo->query("SELECT * FROM enrollments LIMIT 5");
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>User ID</th><th>Course ID</th><th>Course Title</th><th>Status</th><th>Enrolled At</th></tr>";
            while ($row = $stmt->fetch()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['user_id'] . "</td>";
                echo "<td>" . $row['course_id'] . "</td>";
                echo "<td>" . $row['course_title'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>" . $row['enrolled_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "❌ Bảng enrollments không tồn tại<br>";
    }
} catch (Exception $e) {
    echo "❌ Lỗi database: " . $e->getMessage() . "<br>";
}

// Test 3: Test API endpoint
echo "<h3>3. Test Cancel API:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "✅ Có thể test API (đã đăng nhập)<br>";
    echo "<button onclick='testCancelAPI()'>Test Cancel API</button>";
    echo "<div id='apiResult'></div>";
} else {
    echo "❌ Không thể test API (chưa đăng nhập)<br>";
    echo "<a href='../simple_login.html'>Đăng nhập để test</a>";
}

?>

<script>
function testCancelAPI() {
    // Test với course_id = 1 (giả sử)
    fetch('cancel_enrollment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            course_id: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('apiResult').innerHTML = 
            '<h4>API Response:</h4><pre>' + JSON.stringify(data, null, 2) + '</pre>';
    })
    .catch(error => {
        document.getElementById('apiResult').innerHTML = 
            '<h4>API Error:</h4><pre>' + error.toString() + '</pre>';
    });
}
</script>