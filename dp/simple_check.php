<?php
// Kiểm tra trạng thái đăng nhập đơn giản
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

session_start();

try {
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        // Người dùng đã đăng nhập
        echo json_encode([
            'logged_in' => true,
            'user_id' => $_SESSION['user_id'],
            'user_name' => $_SESSION['user_name'] ?? 'Người dùng',
            'user_email' => $_SESSION['user_email'] ?? ''
        ]);
    } else {
        // Người dùng chưa đăng nhập
        echo json_encode([
            'logged_in' => false
        ]);
    }
} catch (Exception $e) {
    // Lỗi - trả về chưa đăng nhập
    echo json_encode([
        'logged_in' => false,
        'error' => $e->getMessage()
    ]);
}
?>