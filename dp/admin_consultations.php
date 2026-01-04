<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Tắt hiển thị lỗi
error_reporting(0);
ini_set('display_errors', 0);

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
            $consultationId = $_GET['id'] ?? null;
            
            if ($consultationId) {
                // Lấy chi tiết một yêu cầu tư vấn
                $sql = "SELECT * FROM consultations WHERE id = ?";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$consultationId]);
                $consultation = $stmt->fetch();
                
                if ($consultation) {
                    echo json_encode([
                        'success' => true,
                        'consultation' => $consultation
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Không tìm thấy yêu cầu tư vấn'
                    ]);
                }
            } else {
                // Lấy danh sách yêu cầu tư vấn
                $search = $_GET['search'] ?? '';
                $status = $_GET['status'] ?? '';
                
                $sql = "SELECT * FROM consultations WHERE 1=1";
                $params = [];
                
                if ($search) {
                    $sql .= " AND (full_name LIKE ? OR email LIKE ? OR course_interest LIKE ?)";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                }
                
                if ($status) {
                    $sql .= " AND status = ?";
                    $params[] = $status;
                }
                
                $sql .= " ORDER BY created_at DESC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $consultations = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'consultations' => $consultations
                ]);
            }
            break;
            
        case 'POST':
            // Thêm yêu cầu tư vấn mới (từ form liên hệ)
            $input = json_decode(file_get_contents('php://input'), true);
            
            $fullName = trim($input['full_name'] ?? '');
            $email = trim($input['email'] ?? '');
            $phone = trim($input['phone'] ?? '');
            $courseInterest = trim($input['course_interest'] ?? '');
            $message = trim($input['message'] ?? '');
            
            // Validation
            if (empty($fullName) || empty($email) || empty($phone)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vui lòng nhập đầy đủ thông tin'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO consultations (full_name, email, phone, course_interest, message) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$fullName, $email, $phone, $courseInterest, $message])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Gửi yêu cầu tư vấn thành công',
                    'consultation_id' => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi gửi yêu cầu'
                ]);
            }
            break;
            
        case 'PUT':
            // Cập nhật trạng thái tư vấn
            $input = json_decode(file_get_contents('php://input'), true);
            $consultationId = $input['consultation_id'] ?? 0;
            $newStatus = $input['status'] ?? '';
            
            if (!$consultationId || !in_array($newStatus, ['new', 'processing', 'completed'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE consultations SET status = ? WHERE id = ?");
            if ($stmt->execute([$newStatus, $consultationId])) {
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
            // Xóa yêu cầu tư vấn
            $input = json_decode(file_get_contents('php://input'), true);
            $consultationId = $input['consultation_id'] ?? 0;
            
            if (!$consultationId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID yêu cầu tư vấn không hợp lệ'
                ]);
                exit;
            }
            
            // Kiểm tra xem yêu cầu tư vấn có tồn tại không
            $checkStmt = $pdo->prepare("SELECT id FROM consultations WHERE id = ?");
            $checkStmt->execute([$consultationId]);
            
            if (!$checkStmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy yêu cầu tư vấn'
                ]);
                exit;
            }
            
            // Xóa yêu cầu tư vấn
            $stmt = $pdo->prepare("DELETE FROM consultations WHERE id = ?");
            if ($stmt->execute([$consultationId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Xóa yêu cầu tư vấn thành công'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xóa yêu cầu tư vấn'
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