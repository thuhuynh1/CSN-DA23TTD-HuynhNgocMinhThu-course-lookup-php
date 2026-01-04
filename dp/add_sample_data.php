<?php
require_once 'config.php';

try {
    echo "<h2>🔧 Thêm dữ liệu mẫu cho Dashboard</h2>";
    
    // Thêm một số user mẫu nếu chưa có
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    
    if ($userCount < 3) {
        echo "<h3>👥 Thêm người dùng mẫu:</h3>";
        
        $sampleUsers = [
            ['Nguyễn Văn An', 'an@example.com', password_hash('123456', PASSWORD_DEFAULT)],
            ['Trần Thị Bình', 'binh@example.com', password_hash('123456', PASSWORD_DEFAULT)],
            ['Lê Văn Cường', 'cuong@example.com', password_hash('123456', PASSWORD_DEFAULT)],
            ['Phạm Thị Dung', 'dung@example.com', password_hash('123456', PASSWORD_DEFAULT)],
            ['Hoàng Văn Em', 'em@example.com', password_hash('123456', PASSWORD_DEFAULT)]
        ];
        
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (full_name, email, password) VALUES (?, ?, ?)");
        
        foreach ($sampleUsers as $user) {
            $stmt->execute($user);
            echo "✅ Thêm user: {$user[0]} ({$user[1]})<br>";
        }
    } else {
        echo "✅ Đã có $userCount người dùng trong hệ thống<br>";
    }
    
    // Thêm một số enrollment mẫu
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM enrollments");
    $enrollmentCount = $stmt->fetch()['count'];
    
    if ($enrollmentCount < 3) {
        echo "<h3>📝 Thêm đăng ký khóa học mẫu:</h3>";
        
        // Lấy user IDs
        $stmt = $pdo->query("SELECT id FROM users LIMIT 5");
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($userIds)) {
            $sampleEnrollments = [
                ['Tiếng Anh giao tiếp', 1500000, 6, 'Cơ bản', 'Thứ 2, 4, 6 - 19:00-21:00', 'Khóa học tiếng Anh cơ bản cho người mới bắt đầu', '["Lớp học nhỏ", "Giảng viên bản ngữ"]', 'pending'],
                ['IELTS Preparation', 2500000, 8, 'Trung cấp', 'Thứ 3, 5, 7 - 18:30-20:30', 'Luyện thi IELTS đạt 6.5+', '["Mock test hàng tuần", "Chấm bài writing chi tiết"]', 'active'],
                ['Lập trình Web Frontend', 2500000, 8, 'Cơ bản', 'Thứ 3, 5, 7 - 19:00-21:30', 'Học HTML, CSS, JavaScript và React', '["Dự án thực tế", "Code review từ mentor"]', 'pending'],
                ['Python cho Data Science', 3000000, 12, 'Trung cấp', 'Thứ 3, 5, 7 - 18:30-21:00', 'Học Python để phân tích dữ liệu', '["Jupyter Notebook", "Real datasets"]', 'active']
            ];
            
            $stmt = $pdo->prepare("
                INSERT INTO enrollments 
                (user_id, course_title, course_price, course_duration, course_level, course_schedule, course_description, course_features, status, enrolled_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            foreach ($sampleEnrollments as $i => $enrollment) {
                $userId = $userIds[$i % count($userIds)];
                $stmt->execute(array_merge([$userId], $enrollment));
                echo "✅ Thêm đăng ký: {$enrollment[0]} cho user ID $userId<br>";
            }
        }
    } else {
        echo "✅ Đã có $enrollmentCount đăng ký khóa học<br>";
    }
    
    // Thêm một số consultation mẫu
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM consultations");
        $consultationCount = $stmt->fetch()['count'];
        
        if ($consultationCount < 2) {
            echo "<h3>💬 Thêm yêu cầu tư vấn mẫu:</h3>";
            
            $sampleConsultations = [
                ['Nguyễn Thị Hoa', 'hoa@example.com', '0901234567', 'Tiếng Anh giao tiếp', 'Tôi muốn học tiếng Anh để giao tiếp trong công việc. Xin tư vấn khóa học phù hợp.', 'new'],
                ['Trần Văn Nam', 'nam@example.com', '0912345678', 'Lập trình Web', 'Tôi là người mới bắt đầu, muốn học lập trình web. Khóa học nào phù hợp?', 'processing'],
                ['Lê Thị Mai', 'mai@example.com', '0923456789', 'IELTS', 'Tôi cần đạt IELTS 7.0 trong 6 tháng. Có khóa học nào phù hợp không?', 'new']
            ];
            
            $stmt = $pdo->prepare("
                INSERT INTO consultations 
                (full_name, email, phone, course_interest, message, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            foreach ($sampleConsultations as $consultation) {
                $stmt->execute($consultation);
                echo "✅ Thêm tư vấn: {$consultation[0]} - {$consultation[3]}<br>";
            }
        } else {
            echo "✅ Đã có $consultationCount yêu cầu tư vấn<br>";
        }
    } catch (Exception $e) {
        echo "⚠️ Bảng consultations chưa tồn tại hoặc có lỗi: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>🎉 Hoàn thành! Bây giờ dashboard sẽ có dữ liệu để hiển thị.</h3>";
    echo "<p><a href='../admin.html'>👉 Mở Admin Dashboard</a></p>";
    echo "<p><a href='test_dashboard_real.php'>🔍 Kiểm tra Dashboard API</a></p>";
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}
?>