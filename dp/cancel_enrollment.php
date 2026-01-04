<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    session_start();
    require_once 'config.php';
    
    // Log debug info
    $debug_info = [
        'method' => $_SERVER['REQUEST_METHOD'],
        'session_id' => session_id(),
        'user_id' => $_SESSION['user_id'] ?? 'not_set',
        'post_data' => file_get_contents('php://input')
    ];
    
    // Chỉ cho phép POST method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Method không được hỗ trợ',
            'debug' => $debug_info
        ]);
        exit;
    }
    
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng đăng nhập để thực hiện thao tác này',
            'debug' => $debug_info
        ]);
        exit;
    }
    
    // Lấy dữ liệu từ request
    $input = json_decode(file_get_contents('php://input'), true);
    $enrollmentId = intval($input['enrollment_id'] ?? 0);
    $userId = $_SESSION['user_id'];
    
    $debug_info['parsed_enrollment_id'] = $enrollmentId;
    $debug_info['user_id_from_session'] = $userId;
    
    if ($enrollmentId <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID đăng ký không hợp lệ',
            'debug' => $debug_info
        ]);
        exit;
    }
    
    // Kiểm tra xem bảng enrollments có tồn tại không
    try {
        $checkTable = $pdo->query("SHOW TABLES LIKE 'enrollments'");
        if ($checkTable->rowCount() == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Bảng đăng ký không tồn tại',
                'debug' => $debug_info
            ]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi kiểm tra cơ sở dữ liệu: ' . $e->getMessage(),
            'debug' => $debug_info
        ]);
        exit;
    }
    
    // Kiểm tra xem người dùng có đăng ký này không
    $checkStmt = $pdo->prepare("
        SELECT id, status, course_title 
        FROM enrollments 
        WHERE user_id = ? AND id = ?
    ");
    $checkStmt->execute([$userId, $enrollmentId]);
    $enrollment = $checkStmt->fetch();
    
    $debug_info['enrollment_found'] = $enrollment ? 'yes' : 'no';
    
    if (!$enrollment) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy đăng ký này hoặc bạn không có quyền hủy',
            'debug' => $debug_info
        ]);
        exit;
    }
    
    // Kiểm tra trạng thái đăng ký
    if ($enrollment['status'] === 'completed') {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể hủy khóa học đã hoàn thành',
            'debug' => $debug_info
        ]);
        exit;
    }
    
    // Xóa đăng ký khóa học
    $deleteStmt = $pdo->prepare("
        DELETE FROM enrollments 
        WHERE user_id = ? AND id = ?
    ");
    
    if ($deleteStmt->execute([$userId, $enrollmentId])) {
        echo json_encode([
            'success' => true,
            'message' => 'Hủy đăng ký khóa học "' . $enrollment['course_title'] . '" thành công',
            'course_title' => $enrollment['course_title'],
            'debug' => $debug_info
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi hủy đăng ký. Vui lòng thử lại sau.',
            'debug' => $debug_info
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
        'debug' => $debug_info ?? []
    ]);
}
?>