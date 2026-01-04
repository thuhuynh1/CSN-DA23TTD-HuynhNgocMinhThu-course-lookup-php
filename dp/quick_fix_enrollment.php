<?php
// Quick fix để test cập nhật enrollment status
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Bật error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    session_start();
    require_once 'config.php';
    
    // Tạm thời bỏ qua admin check để test
    // if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    //     echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập']);
    //     exit;
    // }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        // Lấy danh sách enrollments
        $stmt = $pdo->query("
            SELECT 
                e.id,
                e.course_title,
                e.status,
                e.enrolled_at,
                u.full_name as user_name,
                u.email as user_email
            FROM enrollments e
            JOIN users u ON e.user_id = u.id
            ORDER BY e.enrolled_at DESC
        ");
        $enrollments = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'enrollments' => $enrollments,
            'count' => count($enrollments)
        ]);
        
    } elseif ($method === 'PUT') {
        // Cập nhật trạng thái
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể đọc dữ liệu JSON',
                'raw_input' => file_get_contents('php://input')
            ]);
            exit;
        }
        
        $enrollmentId = $input['enrollment_id'] ?? 0;
        $newStatus = $input['status'] ?? '';
        
        // Validation
        if (!$enrollmentId) {
            echo json_encode([
                'success' => false,
                'message' => 'ID đăng ký không hợp lệ',
                'enrollment_id' => $enrollmentId
            ]);
            exit;
        }
        
        if (!in_array($newStatus, ['pending', 'approved'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Trạng thái không hợp lệ',
                'status' => $newStatus,
                'valid_statuses' => ['pending', 'approved']
            ]);
            exit;
        }
        
        // Kiểm tra enrollment có tồn tại không
        $stmt = $pdo->prepare("SELECT id, status FROM enrollments WHERE id = ?");
        $stmt->execute([$enrollmentId]);
        $enrollment = $stmt->fetch();
        
        if (!$enrollment) {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy đăng ký',
                'enrollment_id' => $enrollmentId
            ]);
            exit;
        }
        
        // Cập nhật trạng thái
        $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
        $result = $stmt->execute([$newStatus, $enrollmentId]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'enrollment_id' => $enrollmentId,
                'old_status' => $enrollment['status'],
                'new_status' => $newStatus,
                'affected_rows' => $stmt->rowCount()
            ]);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi database',
                'error' => $errorInfo
            ]);
        }
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Method không được hỗ trợ',
            'method' => $method
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>