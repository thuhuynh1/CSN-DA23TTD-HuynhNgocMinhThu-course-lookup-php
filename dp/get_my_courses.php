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
            'courses' => [],
            'message' => 'Chưa đăng nhập'
        ]);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Lấy danh sách khóa học đã đăng ký
    $stmt = $pdo->prepare("
        SELECT 
            id,
            course_title,
            course_price,
            course_duration,
            course_level,
            course_schedule,
            course_description,
            course_features,
            status,
            enrolled_at
        FROM enrollments 
        WHERE user_id = ? 
        ORDER BY enrolled_at DESC
    ");
    
    $stmt->execute([$user_id]);
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Xử lý dữ liệu
    $courses = [];
    foreach ($enrollments as $enrollment) {
        $courses[] = [
            'id' => $enrollment['id'],
            'title' => $enrollment['course_title'],
            'price' => (int)$enrollment['course_price'],
            'duration' => (int)$enrollment['course_duration'],
            'level' => $enrollment['course_level'],
            'schedule' => $enrollment['course_schedule'],
            'description' => $enrollment['course_description'],
            'features' => json_decode($enrollment['course_features'] ?: '[]', true),
            'status' => $enrollment['status'],
            'enrollDate' => date('d/m/Y', strtotime($enrollment['enrolled_at']))
        ];
    }
    
    echo json_encode([
        'success' => true,
        'logged_in' => true,
        'courses' => $courses,
        'total' => count($courses),
        'user_id' => $user_id
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'logged_in' => false,
        'courses' => [],
        'error' => $e->getMessage()
    ]);
}
?>