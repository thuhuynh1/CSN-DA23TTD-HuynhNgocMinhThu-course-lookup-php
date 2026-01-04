<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Bật error reporting để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    session_start();
    require_once 'config.php';
    
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng đăng nhập để đăng ký khóa học'
        ]);
        exit;
    }
    
    // Kiểm tra method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Method không hợp lệ'
        ]);
        exit;
    }
    
    // Lấy dữ liệu từ request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        // Fallback cho form data
        $input = $_POST;
    }
    
    $user_id = $_SESSION['user_id'];
    $course_id = $input['course_id'] ?? null;
    $course_title = $input['course_title'] ?? '';
    $course_price = $input['course_price'] ?? 0;
    $course_duration = $input['course_duration'] ?? 0;
    $course_level = $input['course_level'] ?? '';
    $course_schedule = $input['course_schedule'] ?? '';
    $course_description = $input['course_description'] ?? '';
    $course_features = $input['course_features'] ?? [];
    
    // Validation
    if (!$course_id || !$course_title) {
        echo json_encode([
            'success' => false,
            'message' => 'Thông tin khóa học không đầy đủ'
        ]);
        exit;
    }
    
    // Kiểm tra đã đăng ký khóa học này chưa (chỉ chặn trùng lặp cùng một khóa học)
    $stmt = $pdo->prepare("SELECT id, status FROM enrollments WHERE user_id = ? AND course_title = ?");
    $stmt->execute([$user_id, $course_title]);
    $existing_enrollment = $stmt->fetch();
    
    if ($existing_enrollment) {
        $status_text = '';
        switch($existing_enrollment['status']) {
            case 'pending':
                $status_text = 'đang chờ xử lý';
                break;
            case 'active':
                $status_text = 'đang học';
                break;
            case 'completed':
                $status_text = 'đã hoàn thành';
                break;
            default:
                $status_text = 'đã đăng ký';
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Bạn đã đăng ký khóa học "' . $course_title . '" rồi (trạng thái: ' . $status_text . '). Vui lòng kiểm tra trong mục "Khóa học của tôi".'
        ]);
        exit;
    }
    
    // Đếm số khóa học đã đăng ký của user (để thống kê)
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_courses FROM enrollments WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $course_count = $stmt->fetch()['total_courses'];
    
    // Đăng ký khóa học
    $stmt = $pdo->prepare("
        INSERT INTO enrollments (
            user_id, course_title, course_price, course_duration, 
            course_level, course_schedule, course_description, course_features, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $features_json = json_encode($course_features);
    
    $result = $stmt->execute([
        $user_id, $course_title, $course_price, $course_duration,
        $course_level, $course_schedule, $course_description, $features_json
    ]);
    
    if ($result) {
        $new_total = $course_count + 1;
        echo json_encode([
            'success' => true,
            'message' => '🎉 Đăng ký khóa học "' . $course_title . '" thành công! Đây là khóa học thứ ' . $new_total . ' của bạn. Chúng tôi sẽ liên hệ với bạn sớm nhất.',
            'enrollment_id' => $pdo->lastInsertId(),
            'total_courses' => $new_total
        ]);
    } else {
        // Lấy thông tin lỗi chi tiết
        $errorInfo = $stmt->errorInfo();
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi đăng ký khóa học: ' . $errorInfo[2]
        ]);
    }
    
} catch (PDOException $e) {
    // Xử lý lỗi database cụ thể
    if ($e->getCode() == 23000) { // Integrity constraint violation
        echo json_encode([
            'success' => false,
            'message' => 'Bạn đã đăng ký khóa học này rồi. Vui lòng kiểm tra trong mục "Khóa học của tôi".'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi database: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}
?>