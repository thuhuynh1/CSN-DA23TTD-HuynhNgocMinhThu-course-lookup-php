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
    
    // Kiểm tra quyền admin
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        echo json_encode([
            'success' => false,
            'message' => 'Không có quyền truy cập'
        ]);
        exit;
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Kiểm tra nếu có ID cụ thể để lấy chi tiết
            $enrollmentId = $_GET['id'] ?? null;
            
            if ($enrollmentId) {
                // Lấy chi tiết một đăng ký
                $sql = "
                    SELECT 
                        e.*,
                        u.full_name as user_name,
                        u.email as user_email,
                        u.created_at as user_created_at
                    FROM enrollments e
                    JOIN users u ON e.user_id = u.id
                    WHERE e.id = ?
                ";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$enrollmentId]);
                $enrollment = $stmt->fetch();
                
                if ($enrollment) {
                    echo json_encode([
                        'success' => true,
                        'enrollment' => $enrollment
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Không tìm thấy đăng ký'
                    ]);
                }
            } else {
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
                    'enrollments' => $enrollments
                ]);
            }
            break;
            
        case 'PUT':
            // Cập nhật trạng thái đăng ký
            $input = json_decode(file_get_contents('php://input'), true);
            $enrollmentId = $input['enrollment_id'] ?? 0;
            $newStatus = $input['status'] ?? '';
            
            if (!$enrollmentId || !in_array($newStatus, ['pending', 'approved'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
            if ($stmt->execute([$newStatus, $enrollmentId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thành công'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi cập nhật'
                ]);
            }
            break;
            
        case 'DELETE':
            // Xóa đăng ký
            $input = json_decode(file_get_contents('php://input'), true);
            $enrollmentId = $input['enrollment_id'] ?? 0;
            
            if (!$enrollmentId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID đăng ký không hợp lệ'
                ]);
                exit;
            }
            
            // Kiểm tra đăng ký có tồn tại không
            $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE id = ?");
            $stmt->execute([$enrollmentId]);
            
            if (!$stmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy đăng ký'
                ]);
                exit;
            }
            
            // Xóa đăng ký
            $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
            if ($stmt->execute([$enrollmentId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Xóa đăng ký thành công'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xóa đăng ký'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Method không được hỗ trợ'
            ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}
?>