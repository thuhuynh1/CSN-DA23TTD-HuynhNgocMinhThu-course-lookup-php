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
            // Lấy danh sách nội dung
            $id = $_GET['id'] ?? '';
            $type = $_GET['type'] ?? '';
            
            if ($id) {
                // Lấy nội dung theo ID
                $stmt = $pdo->prepare("SELECT * FROM site_content WHERE id = ?");
                $stmt->execute([$id]);
                $content = $stmt->fetchAll();
            } elseif ($type) {
                // Lấy nội dung theo loại
                $stmt = $pdo->prepare("SELECT * FROM site_content WHERE content_type = ? ORDER BY updated_at DESC");
                $stmt->execute([$type]);
                $content = $stmt->fetchAll();
            } else {
                // Lấy tất cả nội dung
                $stmt = $pdo->query("SELECT * FROM site_content ORDER BY content_type, updated_at DESC");
                $content = $stmt->fetchAll();
            }
            
            echo json_encode([
                'success' => true,
                'content' => $content
            ]);
            break;
            
        case 'POST':
            // Thêm nội dung mới
            $input = json_decode(file_get_contents('php://input'), true);
            
            $contentType = trim($input['content_type'] ?? '');
            $title = trim($input['title'] ?? '');
            $content = trim($input['content'] ?? '');
            
            if (empty($contentType) || empty($title)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vui lòng nhập đầy đủ thông tin'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO site_content (content_type, title, content) 
                VALUES (?, ?, ?)
            ");
            
            if ($stmt->execute([$contentType, $title, $content])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Thêm nội dung thành công',
                    'content_id' => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi thêm nội dung'
                ]);
            }
            break;
            
        case 'PUT':
            // Cập nhật nội dung
            $input = json_decode(file_get_contents('php://input'), true);
            $contentId = $input['content_id'] ?? 0;
            $title = trim($input['title'] ?? '');
            $content = trim($input['content'] ?? '');
            $status = $input['status'] ?? 'active';
            
            if (!$contentId || empty($title)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("
                UPDATE site_content 
                SET title = ?, content = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            if ($stmt->execute([$title, $content, $status, $contentId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật nội dung thành công'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi cập nhật'
                ]);
            }
            break;
            
        case 'DELETE':
            // Xóa nội dung
            $input = json_decode(file_get_contents('php://input'), true);
            $contentId = $input['content_id'] ?? 0;
            
            if (!$contentId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID nội dung không hợp lệ'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM site_content WHERE id = ?");
            if ($stmt->execute([$contentId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Xóa nội dung thành công'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xóa nội dung'
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