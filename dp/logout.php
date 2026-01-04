<?php
session_start();
require_once 'config.php';

// Xóa session khỏi database nếu có session_token
if (isset($_SESSION['session_id'])) {
    $stmt = $pdo->prepare("DELETE FROM sessions WHERE session_token = ?");
    $stmt->execute([$_SESSION['session_id']]);
}

// Xóa tất cả session variables
session_unset();

// Hủy session
session_destroy();

// Chuyển hướng về trang chủ với thông báo đăng xuất
header("Location: ../index.html?logout=success");
exit;
?>