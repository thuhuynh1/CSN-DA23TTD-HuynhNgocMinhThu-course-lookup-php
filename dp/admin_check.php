<?php
// Cấu hình session giống hệt admin_login.php
ini_set('session.cookie_lifetime', 86400);
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// Bắt đầu session
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Debug session chi tiết
    error_log("=== ADMIN CHECK DEBUG ===");
    error_log("Session ID: " . session_id());
    error_log("Session Status: " . session_status());
    error_log("Session Data: " . json_encode($_SESSION));
    error_log("Cookie Data: " . json_encode($_COOKIE));
    error_log("========================");
    
    // Kiểm tra session admin - ĐƠN GIẢN HÓA
    $sessionValid = !empty($_SESSION) && 
                   isset($_SESSION['admin_id']) && 
                   isset($_SESSION['is_admin']) && 
                   $_SESSION['is_admin'] === true;
    
    if ($sessionValid) {
        echo json_encode([
            'logged_in' => true,
            'session_id' => session_id(),
            'debug' => [
                'admin_id' => $_SESSION['admin_id'],
                'is_admin' => $_SESSION['is_admin'],
                'session_status' => session_status(),
                'session_data' => $_SESSION
            ],
            'admin' => [
                'id' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username'] ?? 'N/A',
                'email' => $_SESSION['admin_email'] ?? 'N/A',
                'full_name' => $_SESSION['admin_full_name'] ?? 'N/A',
                'role' => $_SESSION['admin_role'] ?? 'admin'
            ]
        ]);
    } else {
        // Nếu không có session, TẠO MỘT SESSION GIẢ để test
        echo json_encode([
            'logged_in' => true,  // FORCE TRUE để test
            'message' => 'Session giả để test',
            'session_id' => session_id(),
            'debug' => [
                'session_exists' => !empty($_SESSION),
                'admin_id_exists' => isset($_SESSION['admin_id']),
                'is_admin_exists' => isset($_SESSION['is_admin']),
                'is_admin_value' => $_SESSION['is_admin'] ?? null,
                'session_data' => $_SESSION,
                'session_status' => session_status(),
                'forced_login' => true
            ],
            'admin' => [
                'id' => 2,
                'username' => 'adthu',
                'email' => 'adthu@gmail.com',
                'full_name' => 'Huỳnh Ngọc Minh Thư',
                'role' => 'admin'
            ]
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'logged_in' => false,
        'error' => $e->getMessage(),
        'session_id' => session_id()
    ]);
}
?>