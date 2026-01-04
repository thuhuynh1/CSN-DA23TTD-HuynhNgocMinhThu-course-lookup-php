<?php
// Script để kiểm tra database và dữ liệu enrollment
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Kiểm tra Database và Enrollment Data</h2>";

try {
    require_once 'config.php';
    
    // 1. Kiểm tra kết nối database
    echo "<h3>1. Database Connection</h3>";
    echo "<p style='color: green;'>✅ Kết nối database thành công</p>";
    
    // 2. Kiểm tra bảng enrollments
    echo "<h3>2. Bảng Enrollments</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'enrollments'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Bảng enrollments tồn tại</p>";
    } else {
        echo "<p style='color: red;'>❌ Bảng enrollments không tồn tại</p>";
        exit;
    }
    
    // 3. Kiểm tra cấu trúc bảng
    echo "<h3>3. Cấu trúc bảng</h3>";
    $stmt = $pdo->query("DESCRIBE enrollments");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 4. Kiểm tra dữ liệu
    echo "<h3>4. Dữ liệu Enrollments</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM enrollments");
    $total = $stmt->fetch()['total'];
    echo "<p>Tổng số enrollments: <strong>{$total}</strong></p>";
    
    if ($total == 0) {
        echo "<p style='color: orange;'>⚠️ Không có dữ liệu enrollment</p>";
        
        // Tạo sample data
        echo "<h4>Tạo sample data...</h4>";
        
        // Kiểm tra có user không
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $userCount = $stmt->fetch()['count'];
        
        if ($userCount == 0) {
            echo "<p>Tạo sample user...</p>";
            $pdo->exec("INSERT INTO users (full_name, email, password) VALUES ('Test User', 'test@example.com', 'password123')");
            echo "<p style='color: green;'>✅ Đã tạo sample user</p>";
        }
        
        // Tạo sample enrollments
        $sampleEnrollments = [
            ['Lê Văn Thuận', 'Luyện Thi IELTS', 1500000, 6, 'Cơ bản', 'Thứ 2,4,6 - 19:00-21:00', 'pending'],
            ['Lê Văn Thuận', 'Tiếng Anh giao tiếp', 1200000, 4, 'Trung bình', 'Thứ 3,5,7 - 18:00-20:00', 'approved'],
            ['Võ Phạm Quang Trường', 'Tiếng Anh giao tiếp', 1200000, 4, 'Cơ bản', 'Thứ 2,4,6 - 18:00-20:00', 'pending']
        ];
        
        foreach ($sampleEnrollments as $enrollment) {
            $stmt = $pdo->prepare("
                INSERT INTO enrollments (user_id, course_title, course_price, course_duration, course_level, course_schedule, course_description, course_features, status) 
                VALUES (1, ?, ?, ?, ?, ?, 'Khóa học chất lượng cao', '[]', ?)
            ");
            $stmt->execute([$enrollment[1], $enrollment[2], $enrollment[3], $enrollment[4], $enrollment[5], $enrollment[6]]);
        }
        
        echo "<p style='color: green;'>✅ Đã tạo " . count($sampleEnrollments) . " sample enrollments</p>";
        
        // Refresh count
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM enrollments");
        $total = $stmt->fetch()['total'];
    }
    
    // 5. Hiển thị dữ liệu hiện tại
    echo "<h3>5. Dữ liệu hiện tại</h3>";
    $stmt = $pdo->query("
        SELECT 
            e.id,
            e.course_title,
            e.status,
            e.enrolled_at,
            u.full_name as user_name
        FROM enrollments e
        JOIN users u ON e.user_id = u.id
        ORDER BY e.id DESC
        LIMIT 10
    ");
    $enrollments = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>User</th><th>Course</th><th>Status</th><th>Date</th><th>Test Update</th></tr>";
    foreach ($enrollments as $enrollment) {
        $newStatus = $enrollment['status'] === 'pending' ? 'approved' : 'pending';
        echo "<tr>";
        echo "<td>{$enrollment['id']}</td>";
        echo "<td>{$enrollment['user_name']}</td>";
        echo "<td>{$enrollment['course_title']}</td>";
        echo "<td><strong>{$enrollment['status']}</strong></td>";
        echo "<td>" . date('d/m/Y', strtotime($enrollment['enrolled_at'])) . "</td>";
        echo "<td><button onclick=\"testUpdate({$enrollment['id']}, '{$newStatus}')\">Update to {$newStatus}</button></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 6. Test API
    echo "<h3>6. Test API</h3>";
    echo "<button onclick=\"testAPI()\">Test Load API</button>";
    echo "<div id=\"apiResult\"></div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
?>

<script>
function testUpdate(enrollmentId, newStatus) {
    console.log('Testing update:', enrollmentId, newStatus);
    
    fetch('admin_enrollments_no_auth.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            enrollment_id: enrollmentId,
            status: newStatus
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('✅ Cập nhật thành công!');
            location.reload();
        } else {
            alert('❌ Lỗi: ' + data.message);
            console.error('Error details:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Có lỗi xảy ra: ' + error.message);
    });
}

function testAPI() {
    fetch('admin_enrollments_no_auth.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('apiResult').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        console.log('API Result:', data);
    })
    .catch(error => {
        document.getElementById('apiResult').innerHTML = '<p style="color: red;">Error: ' + error.message + '</p>';
        console.error('API Error:', error);
    });
}
</script>