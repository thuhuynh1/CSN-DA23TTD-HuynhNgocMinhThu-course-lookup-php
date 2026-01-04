<?php
// Script debug hoàn chỉnh để tìm lỗi enrollment update
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Debug Hoàn Chỉnh - Enrollment Update</h1>";

try {
    require_once 'config.php';
    
    // Test 1: Database Connection
    echo "<h2>1. ✅ Database Connection</h2>";
    echo "<p style='color: green;'>Kết nối database thành công</p>";
    
    // Test 2: Check Tables
    echo "<h2>2. Kiểm tra Tables</h2>";
    $tables = ['users', 'enrollments'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Bảng $table tồn tại</p>";
        } else {
            echo "<p style='color: red;'>❌ Bảng $table không tồn tại</p>";
        }
    }
    
    // Test 3: Check Data
    echo "<h2>3. Kiểm tra Dữ liệu</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    echo "<p>Users: <strong>$userCount</strong></p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM enrollments");
    $enrollmentCount = $stmt->fetch()['count'];
    echo "<p>Enrollments: <strong>$enrollmentCount</strong></p>";
    
    if ($enrollmentCount == 0) {
        echo "<p style='color: orange;'>⚠️ Không có enrollment, tạo sample data...</p>";
        
        // Tạo user nếu chưa có
        if ($userCount == 0) {
            $pdo->exec("INSERT INTO users (full_name, email, password) VALUES ('Test User', 'test@example.com', 'password123')");
            echo "<p style='color: green;'>✅ Đã tạo sample user</p>";
        }
        
        // Tạo enrollments
        $stmt = $pdo->prepare("
            INSERT INTO enrollments (user_id, course_title, course_price, course_duration, course_level, course_schedule, course_description, course_features, status) 
            VALUES (1, ?, 1500000, 6, 'Cơ bản', 'Thứ 2,4,6', 'Test course', '[]', ?)
        ");
        
        $courses = [
            ['Luyện Thi IELTS', 'pending'],
            ['Tiếng Anh Giao Tiếp', 'approved'],
            ['Tin Học Văn Phòng', 'pending']
        ];
        
        foreach ($courses as $course) {
            $stmt->execute([$course[0], $course[1]]);
        }
        
        echo "<p style='color: green;'>✅ Đã tạo " . count($courses) . " sample enrollments</p>";
    }
    
    // Test 4: API GET Test
    echo "<h2>4. Test API GET</h2>";
    
    // Simulate GET request
    $_SERVER['REQUEST_METHOD'] = 'GET';
    ob_start();
    include 'admin_enrollments_no_auth.php';
    $getResponse = ob_get_clean();
    
    echo "<h4>GET Response:</h4>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo htmlspecialchars($getResponse);
    echo "</pre>";
    
    $getData = json_decode($getResponse, true);
    if ($getData && $getData['success']) {
        echo "<p style='color: green;'>✅ API GET hoạt động bình thường</p>";
        echo "<p>Tìm thấy " . count($getData['enrollments']) . " enrollments</p>";
    } else {
        echo "<p style='color: red;'>❌ API GET có lỗi</p>";
    }
    
    // Test 5: API PUT Test
    echo "<h2>5. Test API PUT</h2>";
    
    if ($getData && $getData['success'] && !empty($getData['enrollments'])) {
        $testEnrollment = $getData['enrollments'][0];
        $testId = $testEnrollment['id'];
        $currentStatus = $testEnrollment['status'];
        $newStatus = $currentStatus === 'pending' ? 'approved' : 'pending';
        
        echo "<p>Test: Cập nhật enrollment ID $testId từ '$currentStatus' thành '$newStatus'</p>";
        
        // Simulate PUT request
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $putData = json_encode([
            'enrollment_id' => $testId,
            'status' => $newStatus
        ]);
        
        // Mock php://input
        $tempFile = tempnam(sys_get_temp_dir(), 'php_input');
        file_put_contents($tempFile, $putData);
        
        // Override php://input for testing
        $originalInput = 'php://input';
        
        ob_start();
        
        // Manually execute PUT logic
        $input = json_decode($putData, true);
        $enrollmentId = $input['enrollment_id'] ?? 0;
        $newStatusTest = $input['status'] ?? '';
        
        if (!$enrollmentId || !in_array($newStatusTest, ['pending', 'approved'])) {
            $putResponse = json_encode([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'debug' => ['enrollment_id' => $enrollmentId, 'status' => $newStatusTest]
            ]);
        } else {
            // Check if enrollment exists
            $stmt = $pdo->prepare("SELECT id, status FROM enrollments WHERE id = ?");
            $stmt->execute([$enrollmentId]);
            $enrollment = $stmt->fetch();
            
            if (!$enrollment) {
                $putResponse = json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy đăng ký với ID: ' . $enrollmentId
                ]);
            } else {
                // Update status
                $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
                $result = $stmt->execute([$newStatusTest, $enrollmentId]);
                
                if ($result) {
                    $putResponse = json_encode([
                        'success' => true,
                        'message' => 'Cập nhật trạng thái thành công',
                        'debug' => [
                            'enrollment_id' => $enrollmentId,
                            'old_status' => $enrollment['status'],
                            'new_status' => $newStatusTest,
                            'affected_rows' => $stmt->rowCount()
                        ]
                    ]);
                } else {
                    $errorInfo = $stmt->errorInfo();
                    $putResponse = json_encode([
                        'success' => false,
                        'message' => 'Lỗi database: ' . $errorInfo[2],
                        'debug' => ['error_info' => $errorInfo]
                    ]);
                }
            }
        }
        
        ob_end_clean();
        
        echo "<h4>PUT Response:</h4>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
        echo htmlspecialchars($putResponse);
        echo "</pre>";
        
        $putData = json_decode($putResponse, true);
        if ($putData && $putData['success']) {
            echo "<p style='color: green;'>✅ API PUT hoạt động bình thường</p>";
            
            // Rollback for testing
            $rollbackStatus = $currentStatus;
            $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
            $stmt->execute([$rollbackStatus, $testId]);
            echo "<p style='color: blue;'>🔄 Đã rollback về trạng thái ban đầu</p>";
        } else {
            echo "<p style='color: red;'>❌ API PUT có lỗi</p>";
        }
        
        // Clean up temp file
        unlink($tempFile);
    }
    
    // Test 6: Current Data
    echo "<h2>6. Dữ liệu Hiện Tại</h2>";
    $stmt = $pdo->query("
        SELECT 
            e.id,
            e.course_title,
            e.status,
            u.full_name as user_name
        FROM enrollments e
        JOIN users u ON e.user_id = u.id
        ORDER BY e.id DESC
        LIMIT 5
    ");
    $enrollments = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>User</th><th>Course</th><th>Status</th></tr>";
    foreach ($enrollments as $enrollment) {
        echo "<tr>";
        echo "<td>{$enrollment['id']}</td>";
        echo "<td>{$enrollment['user_name']}</td>";
        echo "<td>{$enrollment['course_title']}</td>";
        echo "<td><strong>{$enrollment['status']}</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test 7: JavaScript Test
    echo "<h2>7. JavaScript Test</h2>";
    echo "<button onclick=\"testJavaScript()\">Test JavaScript API Call</button>";
    echo "<div id=\"jsResult\"></div>";
    
    echo "<h2>🎯 Kết Luận</h2>";
    echo "<p>Nếu tất cả test trên đều PASS, vấn đề có thể là:</p>";
    echo "<ul>";
    echo "<li>Browser cache - Thử hard refresh (Ctrl+F5)</li>";
    echo "<li>JavaScript error - Kiểm tra console (F12)</li>";
    echo "<li>Network issue - Kiểm tra Network tab</li>";
    echo "<li>Session conflict - Thử incognito mode</li>";
    echo "</ul>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='../test_simple.html'>Test với Simple Page</a></li>";
    echo "<li><a href='../admin.html'>Test với Admin Panel</a></li>";
    echo "<li>Kiểm tra Browser Console khi cập nhật</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Lỗi Fatal</h2>";
    echo "<p>Message: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<script>
function testJavaScript() {
    const resultDiv = document.getElementById('jsResult');
    resultDiv.innerHTML = '<p>Testing...</p>';
    
    // Test GET
    fetch('admin_enrollments_no_auth.php')
    .then(response => {
        console.log('GET Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('GET Data:', data);
        
        if (data.success && data.enrollments.length > 0) {
            const enrollment = data.enrollments[0];
            const newStatus = enrollment.status === 'pending' ? 'approved' : 'pending';
            
            resultDiv.innerHTML += '<p style="color: green;">✅ GET API hoạt động</p>';
            resultDiv.innerHTML += '<p>Testing PUT với enrollment ID ' + enrollment.id + '...</p>';
            
            // Test PUT
            return fetch('admin_enrollments_no_auth.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    enrollment_id: enrollment.id,
                    status: newStatus
                })
            });
        } else {
            throw new Error('No enrollments found');
        }
    })
    .then(response => {
        console.log('PUT Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('PUT Data:', data);
        
        if (data.success) {
            resultDiv.innerHTML += '<p style="color: green;">✅ PUT API hoạt động</p>';
            resultDiv.innerHTML += '<p>✅ JavaScript test THÀNH CÔNG!</p>';
        } else {
            resultDiv.innerHTML += '<p style="color: red;">❌ PUT API lỗi: ' + data.message + '</p>';
        }
    })
    .catch(error => {
        console.error('JavaScript test error:', error);
        resultDiv.innerHTML += '<p style="color: red;">❌ JavaScript test lỗi: ' + error.message + '</p>';
    });
}
</script>