<?php
// File debug đơn giản để kiểm tra JSON output
header('Content-Type: application/json');

// Tắt tất cả error output
error_reporting(0);
ini_set('display_errors', 0);

try {
    session_start();
    
    // Debug session info
    $session_info = [
        'session_id' => session_id(),
        'session_status' => session_status(),
        'session_data' => $_SESSION ?? [],
        'has_user_id' => isset($_SESSION['user_id']),
        'has_full_name' => isset($_SESSION['full_name'])
    ];
    
    // Kiểm tra session đơn giản
    if (isset($_SESSION['user_id']) && isset($_SESSION['full_name'])) {
        echo json_encode([
            'logged_in' => true,
            'user_name' => $_SESSION['full_name'],
            'email' => $_SESSION['email'] ?? 'N/A',
            'user_id' => $_SESSION['user_id'],
            'debug' => $session_info
        ]);
    } else {
        echo json_encode([
            'logged_in' => false,
            'debug' => $session_info
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'logged_in' => false,
        'error' => $e->getMessage()
    ]);
}
?>