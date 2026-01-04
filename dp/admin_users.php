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
            // Lấy danh sách học viên
            $search = $_GET['search'] ?? '';
            
            $sql = "SELECT id, full_name, email, created_at FROM users WHERE 1=1";
            $params = [];
            
            if ($search) {
                $sql .= " AND (full_name LIKE ? OR email LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'users' => $users
            ]);
            break;
            
        case 'POST':
            // Thêm học viên mới
            $input = json_decode(file_get_contents('php://input'), true);
            
            $fullName = trim($input['full_name'] ?? '');
            $email = trim($input['email'] ?? '');
            $password = $input['password'] ?? '';
            
            // Validation
            if (empty($fullName) || empty($email) || empty($password)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vui lòng nhập đầy đủ thông tin'
                ]);
                exit;
            }
            
            // Kiểm tra email đã tồn tại
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email đã tồn tại'
                ]);
                exit;
            }
            
            // Thêm học viên
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (full_name, email, password) 
                VALUES (?, ?, ?)
            ");
            
            if ($stmt->execute([$fullName, $email, $hashedPassword])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Thêm học viên thành công',
                    'user_id' => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi thêm học viên'
                ]);
            }
            break;
            
        case 'DELETE':
            // Xóa học viên
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['user_id'] ?? 0;
            
            if (!$userId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID học viên không hợp lệ'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt->execute([$userId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Xóa học viên thành công'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xóa học viên'
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