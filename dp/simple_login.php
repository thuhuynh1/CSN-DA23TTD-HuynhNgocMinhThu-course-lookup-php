<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../simple_login.html?error=" . urlencode("Email không hợp lệ"));
        exit;
    }
    
    if (empty($password)) {
        header("Location: ../simple_login.html?error=" . urlencode("Mật khẩu không được để trống"));
        exit;
    }
    
    // Xác thực người dùng
    try {
        $stmt = $pdo->prepare("SELECT id, password, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Tạo session
            $session_id = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            // Lưu session vào database
            $stmt = $pdo->prepare("INSERT INTO sessions (user_id, session_token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $session_id, $expires_at]);
            
            // Lưu session vào PHP session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['session_id'] = $session_id;
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $email;
            
            // Chuyển hướng đến trang chính với thông báo thành công
            header("Location: ../index.html?login=success");
            exit;
        } else {
            header("Location: ../simple_login.html?error=" . urlencode("Email hoặc mật khẩu không đúng"));
            exit;
        }
    } catch (Exception $e) {
        header("Location: ../simple_login.html?error=" . urlencode("Có lỗi xảy ra: " . $e->getMessage()));
        exit;
    }
}

// Nếu không phải POST request, chuyển về trang đăng nhập
header("Location: ../simple_login.html");
exit;
?>