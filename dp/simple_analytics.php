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
    $action = $_GET['action'] ?? '';
    
    switch ($method) {
        case 'GET':
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
            break;
            
        case 'POST':
            // Ghi nhận lượt truy cập đơn giản
            $input = json_decode(file_get_contents('php://input'), true);
            $pageUrl = $input['page_url'] ?? '';
            
            if ($pageUrl) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO website_analytics (page_url, user_ip, visit_date, visit_time) 
                        VALUES (?, ?, CURDATE(), CURTIME())
                    ");
                    
                    $stmt->execute([$pageUrl, $_SERVER['REMOTE_ADDR'] ?? '']);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Ghi nhận truy cập thành công'
                    ]);
                } catch (Exception $e) {
                    // Nếu chưa có bảng, bỏ qua lỗi
                    echo json_encode([
                        'success' => true,
                        'message' => 'OK'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'URL không hợp lệ'
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
        'message' => 'Lỗi hệ thống'
    ]);
}
?>