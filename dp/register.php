<?php
require_once 'config.php';

// Bật error reporting để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    
    // Validation
    $errors = [];
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
    }
    
    if (empty($full_name) || strlen($full_name) < 2) {
        $errors[] = "Họ tên phải có ít nhất 2 ký tự";
    }
    
    // Kiểm tra email đã tồn tại
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Email này đã được sử dụng. Vui lòng chọn email khác.";
            }
        } catch (PDOException $e) {
            $errors[] = "Lỗi kiểm tra email: " . $e->getMessage();
        }
    }
    
    // Đăng ký người dùng
    if (empty($errors)) {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$full_name, $email, $password_hash])) {
                // Đăng ký thành công - chuyển về trang đăng nhập với thông báo
                header("Location: ../simple_login.html?success=" . urlencode("🎉 Đăng ký thành công! Vui lòng đăng nhập với tài khoản mới."));
                exit;
            } else {
                $errorInfo = $stmt->errorInfo();
                $errors[] = "Có lỗi xảy ra khi đăng ký: " . $errorInfo[2];
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $errors[] = "Email này đã được sử dụng. Vui lòng chọn email khác.";
            } else {
                $errors[] = "Lỗi database: " . $e->getMessage();
            }
        } catch (Exception $e) {
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
    
    // Có lỗi - trả về trang đăng ký với thông báo lỗi
    if (!empty($errors)) {
        $error_message = implode(' | ', $errors);
        header("Location: ../simple_register.html?error=" . urlencode($error_message));
        exit;
    }
}

// Nếu không phải POST request, chuyển về trang đăng ký
header("Location: ../simple_register.html");
exit;
?>