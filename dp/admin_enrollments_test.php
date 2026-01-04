<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Bật error reporting để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    session_start();
    require_once 'config.php';
    
    // TẠM THỜI BỎ QUA KIỂM TRA ADMIN để test
    // if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'Không có quyền truy cập'
    //     ]);
    //     exit;
    // }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Lấy danh sách đăng ký
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';
            
            $sql = "
                SELECT 
                    e.id,
                    e.course_title,
                    e.course_price,
                    e.status,
                    e.enrolled_at,
                    u.full_name as user_name,
                    u.email as user_email
                FROM enrollments e
                JOIN users u ON e.user_id = u.id
                WHERE 1=1
            ";
            $params = [];
            
            if ($search) {
                $sql .= " AND (u.full_name LIKE ? OR e.course_title LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            if ($status) {
                $sql .= " AND e.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY e.enrolled_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $enrollments = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'enrollments' => $enrollments,
                'debug' => [
                    'method' => $method,
                    'search' => $search,
                    'status' => $status,
                    'sql' => $sql,
                    'params' => $params
                ]
            ]);
            break;
            
        case 'PUT':
            // Cập nhật trạng thái đăng ký
            $input = json_decode(file_get_contents('php://input'), true);
            $enrollmentId = $input['enrollment_id'] ?? 0;
            $newStatus = $input['status'] ?? '';
            
            echo json_encode([
                'success' => false,
                'message' => 'Debug info',
                'debug' => [
                    'method' => $method,
                    'input' => $input,
                    'enrollment_id' => $enrollmentId,
                    'new_status' => $newStatus,
                    'valid_statuses' => ['pending', 'approved'],
                    'is_valid' => in_array($newStatus, ['pending', 'approved']),
                    'raw_input' => file_get_contents('php://input')
                ]
            ]);
            
            if (!$enrollmentId || !in_array($newStatus, ['pending', 'approved'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'debug' => [
                        'enrollment_id' => $enrollmentId,
                        'new_status' => $newStatus,
                        'valid_enrollment_id' => (bool)$enrollmentId,
                        'valid_status' => in_array($newStatus, ['pending', 'approved'])
                    ]
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
            if ($stmt->execute([$newStatus, $enrollmentId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thành công',
                    'debug' => [
                        'enrollment_id' => $enrollmentId,
                        'new_status' => $newStatus,
                        'affected_rows' => $stmt->rowCount()
                    ]
                ]);
            } else {
                $errorInfo = $stmt->errorInfo();
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi cập nhật',
                    'debug' => [
                        'error_info' => $errorInfo,
                        'enrollment_id' => $enrollmentId,
                        'new_status' => $newStatus
                    ]
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Method không được hỗ trợ',
                'debug' => [
                    'method' => $method,
                    'supported_methods' => ['GET', 'PUT']
                ]
            ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
?>