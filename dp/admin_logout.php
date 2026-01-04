<?php
session_start();

// Xóa tất cả session admin
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_full_name']);
unset($_SESSION['admin_role']);
unset($_SESSION['is_admin']);

// Chuyển hướng về trang đăng nhập admin
header('Location: ../admin-login.html?logout=success');
exit;
?>