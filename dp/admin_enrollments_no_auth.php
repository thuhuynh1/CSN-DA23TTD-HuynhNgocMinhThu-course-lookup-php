<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Bật error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'config.php';
    
    // BỎ QUA KIỂM TRA ADMIN để test
    // session_start();
    // if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    //     echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập']);
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
                    'count' => count($enrollments)
                ]
            ]);
            break;
            
        case 'PUT':
            // Cập nhật trạng thái đăng ký
            $input = json_decode(file_get_contents('php://input'), true);
            $enrollmentId = $input['enrollment_id'] ?? 0;
            $newStatus = $input['status'] ?? '';
            
            // Log để debug
            error_log("PUT Request - ID: $enrollmentId, Status: $newStatus");
            
            if (!$enrollmentId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID đăng ký không hợp lệ',
                    'debug' => ['enrollment_id' => $enrollmentId]
                ]);
                exit;
            }
            
            if (!in_array($newStatus, ['pending', 'approved'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Trạng thái không hợp lệ: ' . $newStatus,
                    'debug' => [
                        'status' => $newStatus,
                        'valid_statuses' => ['pending', 'approved']
                    ]
                ]);
                exit;
            }
            
            // Kiểm tra enrollment có tồn tại
            $stmt = $pdo->prepare("SELECT id, status FROM enrollments WHERE id = ?");
            $stmt->execute([$enrollmentId]);
            $enrollment = $stmt->fetch();
            
            if (!$enrollment) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy đăng ký với ID: ' . $enrollmentId
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
                    'debug' => [
                        'enrollment_id' => $enrollmentId,
                        'old_status' => $enrollment['status'],
                        'new_status' => $newStatus,
                        'affected_rows' => $stmt->rowCount()
                    ]
                ]);
            } else {
                $errorInfo = $stmt->errorInfo();
                echo json_encode([
                    'success' => false,
                    'message' => 'Lỗi database: ' . $errorInfo[2],
                    'debug' => ['error_info' => $errorInfo]
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Method không được hỗ trợ: ' . $method
            ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
?>