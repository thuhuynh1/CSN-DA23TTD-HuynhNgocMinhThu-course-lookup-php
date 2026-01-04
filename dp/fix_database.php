<?php
// Script để kiểm tra và sửa lỗi database cho enrollment status
require_once 'config.php';

echo "<h2>🔧 Fix Database Issues</h2>";

try {
    // 1. Kiểm tra bảng enrollments có tồn tại không
    echo "<h3>1. Kiểm tra bảng enrollments</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'enrollments'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Bảng enrollments tồn tại</p>";
    } else {
        echo "<p style='color: red;'>❌ Bảng enrollments không tồn tại</p>";
        echo "<p>Cần chạy database_setup.sql</p>";
        exit;
    }
    
    // 2. Kiểm tra cấu trúc bảng
    echo "<h3>2. Kiểm tra cấu trúc bảng</h3>";
    $stmt = $pdo->query("DESCRIBE enrollments");
    $columns = $stmt->fetchAll();
    
    $hasStatusColumn = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'status') {
            $hasStatusColumn = true;
            echo "<p>✅ Cột status: {$column['Type']}</p>";
        }
    }
    
    if (!$hasStatusColumn) {
        echo "<p style='color: red;'>❌ Không có cột status</p>";
        echo "<p>Thêm cột status...</p>";
        $pdo->exec("ALTER TABLE enrollments ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
        echo "<p style='color: green;'>✅ Đã thêm cột status</p>";
    }
    
    // 3. Kiểm tra dữ liệu
    echo "<h3>3. Kiểm tra dữ liệu</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM enrollments");
    $total = $stmt->fetch()['total'];
    echo "<p>Tổng số enrollments: <strong>{$total}</strong></p>";
    
    if ($total == 0) {
        echo "<p style='color: orange;'>⚠️ Không có dữ liệu enrollment</p>";
        echo "<p>Tạo sample data...</p>";
        
        // Kiểm tra có user nào không
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
        $userCount = $stmt->fetch()['total'];
        
        if ($userCount == 0) {
            echo "<p>Tạo sample user...</p>";
            $pdo->exec("INSERT INTO users (full_name, email, password) VALUES ('Test User', 'test@example.com', 'password')");
        }
        
        // Tạo sample enrollment
        $pdo->exec("
            INSERT INTO enrollments (user_id, course_title, course_price, course_duration, course_level, course_schedule, course_description, course_features, status) 
            VALUES (1, 'Sample Course', 1500000, 6, 'Cơ bản', 'Thứ 2,4,6 - 19:00-21:00', 'Khóa học mẫu để test', '[]', 'pending')
        ");
        echo "<p style='color: green;'>✅ Đã tạo sample enrollment</p>";
    }
    
    // 4. Kiểm tra trạng thái hiện tại
    echo "<h3>4. Trạng thái hiện tại</h3>";
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM enrollments GROUP BY status");
    $statuses = $stmt->fetchAll();
    
    foreach ($statuses as $status) {
        echo "<p>- {$status['status']}: {$status['count']} đăng ký</p>";
    }
    
    // 5. Migrate trạng thái cũ nếu cần
    echo "<h3>5. Migration trạng thái</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM enrollments WHERE status IN ('active', 'completed')");
    $oldStatusCount = $stmt->fetch()['count'];
    
    if ($oldStatusCount > 0) {
        echo "<p>Tìm thấy {$oldStatusCount} đăng ký với trạng thái cũ</p>";
        $pdo->exec("UPDATE enrollments SET status = 'approved' WHERE status IN ('active', 'completed')");
        echo "<p style='color: green;'>✅ Đã migrate trạng thái cũ thành 'approved'</p>";
    } else {
        echo "<p style='color: green;'>✅ Không có trạng thái cũ cần migrate</p>";
    }
    
    // 6. Test cập nhật
    echo "<h3>6. Test cập nhật trạng thái</h3>";
    $stmt = $pdo->query("SELECT id, status FROM enrollments LIMIT 1");
    $testEnrollment = $stmt->fetch();
    
    if ($testEnrollment) {
        $testId = $testEnrollment['id'];
        $currentStatus = $testEnrollment['status'];
        $newStatus = $currentStatus === 'pending' ? 'approved' : 'pending';
        
        echo "<p>Test: Cập nhật enrollment {$testId} từ '{$currentStatus}' thành '{$newStatus}'</p>";
        
        $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
        if ($stmt->execute([$newStatus, $testId])) {
            echo "<p style='color: green;'>✅ Test cập nhật thành công</p>";
            
            // Rollback
            $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
            $stmt->execute([$currentStatus, $testId]);
            echo "<p style='color: blue;'>🔄 Đã rollback</p>";
        } else {
            echo "<p style='color: red;'>❌ Test cập nhật thất bại</p>";
        }
    }
    
    echo "<h3>🎉 Hoàn thành!</h3>";
    echo "<p><a href='setup_and_test.php'>Tiếp tục với Setup và Test</a></p>";
    echo "<p><a href='../admin.html'>Vào Admin Panel</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
}
?>