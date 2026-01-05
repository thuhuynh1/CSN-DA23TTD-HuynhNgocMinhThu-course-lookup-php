<?php
// Cấu hình session (chỉ set nếu session chưa start)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.cookie_samesite', 'Lax');
}

// Cấu hình kết nối cơ sở dữ liệu
// Thay đổi thông tin này theo MySQL Server của bạn
define('DB_HOST', 'localhost');        // Hoặc IP của MySQL server
define('DB_NAME', 'user_auth_db');     // Tên database
define('DB_USER', 'root');             // Username MySQL
define('DB_PASS', '');                 // XAMPP mặc định không có password

// Cấu hình session
define('SESSION_LIFETIME', 3600); // 1 giờ

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}
?>