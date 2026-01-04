<?php
// Quick script để thiết lập admin session
session_start();

// Thiết lập admin session
$_SESSION['is_admin'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'admin';

// Redirect về admin panel
header('Location: ../admin.html');
exit;
?>