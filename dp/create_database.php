<?php
// Tạo database user_auth_db
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Kết nối MySQL server (không chỉ định database)
    $pdo = new PDO(
        "mysql:host=localhost;charset=utf8mb4",
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Tạo database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS user_auth_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    echo "✅ Đã tạo database 'user_auth_db' thành công!";
    
} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}
?>