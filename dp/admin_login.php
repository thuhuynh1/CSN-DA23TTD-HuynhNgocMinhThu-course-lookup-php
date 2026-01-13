<?php
// Cấu hình session trước khi start
ini_set('session.cookie_lifetime', 86400); // 24 giờ
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// Bắt đầu session
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'config.php';
    
    // Debug: Log request
    error_log("=== ADMIN LOGIN DEBUG ===");
    error_log("Session ID before: " . session_id());
    error_log("Session Status: " . session_status());
    
    // Kiểm tra method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Method không hợp lệ'
        ]);
        exit;
    }
    
    // Lấy dữ liệu từ request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        // Fallback cho form data
        $input = $_POST;
    }
    
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng nhập đầy đủ email và mật khẩu'
        ]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email không hợp lệ'
        ]);
        exit;
    }
    
    // Debug: Log email được nhập
    error_log("Email nhập vào: '$email'");
    
    // Tìm admin trong database
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    
    // Debug: Log kết quả tìm kiếm
    error_log("Admin tìm thấy: " . ($admin ? "CÓ" : "KHÔNG"));
    if ($admin) {
        error_log("Admin info: " . json_encode([
            'id' => $admin['id'],
            'username' => $admin['username'], 
            'email' => $admin['email'],
            'status' => $admin['status']
        ]));
    }
    
    if (!$admin) {
        // Thêm debug: Kiểm tra tất cả admin
        $allAdmins = $pdo->query("SELECT email, status FROM admins")->fetchAll();
        error_log("Tất cả admin trong DB: " . json_encode($allAdmins));
        
        echo json_encode([
            'success' => false,
            'message' => 'Email hoặc mật khẩu không đúng',
            'debug' => [
                'email_input' => $email,
                'all_admins' => $allAdmins
            ]
        ]);
        exit;
    }
    
    // Kiểm tra mật khẩu (hỗ trợ cả hash và plain text để test)
    $passwordValid = false;
    
    // Thử kiểm tra với hash trước
    if (password_verify($password, $admin['password'])) {
        $passwordValid = true;
    } 
    // Nếu không phải hash, kiểm tra trực tiếp (chỉ để test)
    else if ($password === $admin['password']) {
        $passwordValid = true;
        
        // Cập nhật mật khẩu thành hash
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updatePasswordStmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $updatePasswordStmt->execute([$hashedPassword, $admin['id']]);
    }
    
    if (!$passwordValid) {
        echo json_encode([
            'success' => false,
            'message' => 'Email hoặc mật khẩu không đúng'
        ]);
        exit;
    }
    
    // Cập nhật thời gian đăng nhập cuối
    $updateStmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
    $updateStmt->execute([$admin['id']]);
    
    // Xóa session cũ và tạo mới
    session_regenerate_id(true);
    
    // Tạo session cho admin
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_full_name'] = $admin['full_name'];
    $_SESSION['admin_role'] = $admin['role'];
    $_SESSION['is_admin'] = true;
    $_SESSION['login_time'] = time();
    
    // Debug session sau khi tạo
    error_log("Session ID after: " . session_id());
    error_log("Session Data: " . json_encode($_SESSION));
    
    // Đảm bảo session được lưu
    session_write_close();
    
    // Khởi động lại session để kiểm tra
    session_start();
    
    error_log("Session verification: " . json_encode($_SESSION));
    error_log("========================");
    
    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'session_id' => session_id(),
        'debug' => [
            'session_created' => !empty($_SESSION),
            'admin_id' => $_SESSION['admin_id'] ?? null,
            'is_admin' => $_SESSION['is_admin'] ?? null
        ],
        'admin' => [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'email' => $admin['email'],
            'full_name' => $admin['full_name'],
            'role' => $admin['role']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}
?>