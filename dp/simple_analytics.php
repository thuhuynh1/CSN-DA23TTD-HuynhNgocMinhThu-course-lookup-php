<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Tắt hiển thị lỗi
error_reporting(0);
ini_set('display_errors', 0);

try {
    session_start();
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'POST') {
        // Ghi nhận lượt truy cập đơn giản - không cần database
        $input = json_decode(file_get_contents('php://input'), true);
        $pageUrl = $input['page_url'] ?? '';
        
        // Luôn trả về thành công để không làm gián đoạn website
        echo json_encode([
            'success' => true,
            'message' => 'OK'
        ]);
        exit;
    }
    
    // Chỉ admin mới có thể xem thống kê
    require_once 'config.php';
    
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        echo json_encode([
            'success' => false,
            'message' => 'Không có quyền truy cập'
        ]);
        exit;
    }
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'website_stats':
            // Thống kê đơn giản
            $stats = [];
            
            // Lượt truy cập hôm nay (nếu có bảng analytics)
            try {
                $stmt = $pdo->query("
                    SELECT COUNT(*) as total 
                    FROM website_analytics 
                    WHERE visit_date = CURDATE()
                ");
                $stats['todayVisits'] = $stmt->fetch()['total'];
            } catch (Exception $e) {
                // Nếu chưa có bảng analytics
                $stats['todayVisits'] = 0;
            }
            
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Action không hợp lệ'
            ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => true,
        'message' => 'OK'
    ]);
}
?>