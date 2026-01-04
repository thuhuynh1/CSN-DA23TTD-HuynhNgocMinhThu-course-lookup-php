<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Bật error reporting để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    session_start();
    require_once 'config.php';
    
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'logged_in' => false,
            'message' => 'Chưa đăng nhập'
        ]);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Lấy thông tin user từ database
    $stmt = $pdo->prepare("
        SELECT 
            id,
            full_name,
            email,
            created_at
        FROM users 
        WHERE id = ?
    ");
    
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode([
            'success' => false,
            'logged_in' => false,
            'message' => 'Không tìm thấy thông tin người dùng'
        ]);
        exit;
    }
    
    // Lấy thống kê khóa học
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_courses,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_courses,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_courses
        FROM enrollments 
        WHERE user_id = ?
    ");
    
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Format ngày đăng ký
    $registeredAt = date('d/m/Y', strtotime($user['created_at']));
    
    echo json_encode([
        'success' => true,
        'logged_in' => true,
        'user' => [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'registered_at' => $registeredAt,
            'created_at' => $user['created_at']
        ],
        'stats' => [
            'total_courses' => (int)$stats['total_courses'],
            'approved_courses' => (int)$stats['approved_courses'],
            'pending_courses' => (int)$stats['pending_courses']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'logged_in' => false,
        'error' => $e->getMessage()
    ]);
}
?>