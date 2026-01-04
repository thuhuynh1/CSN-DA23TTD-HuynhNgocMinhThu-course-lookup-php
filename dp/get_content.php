<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once 'config.php';
    
    $type = $_GET['type'] ?? '';
    
    if (empty($type)) {
        echo json_encode([
            'success' => false,
            'message' => 'Loại nội dung không được chỉ định'
        ]);
        exit;
    }
    
    // Lấy nội dung theo loại và trạng thái active
    $stmt = $pdo->prepare("
        SELECT id, title, content, updated_at 
        FROM site_content 
        WHERE content_type = ? AND status = 'active' 
        ORDER BY updated_at DESC
    ");
    
    $stmt->execute([$type]);
    $content = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'content' => $content,
        'type' => $type
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}
?>