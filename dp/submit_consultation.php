<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Tắt hiển thị lỗi
error_reporting(0);
ini_set('display_errors', 0);

try {
    require_once 'config.php';
    
    // Chỉ cho phép POST method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Method không được hỗ trợ'
        ]);
        exit;
    }
    
    // Lấy dữ liệu từ request
    $input = json_decode(file_get_contents('php://input'), true);
    
    $fullName = trim($input['full_name'] ?? '');
    $email = trim($input['email'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $courseInterest = trim($input['course_interest'] ?? '');
    $message = trim($input['message'] ?? '');
    
    // Validation
    if (empty($fullName)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng nhập họ tên'
        ]);
        exit;
    }
    
    if (empty($email)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng nhập email'
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
    
    if (empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng nhập tin nhắn'
        ]);
        exit;
    }
    
    // Kiểm tra xem bảng consultations có tồn tại không
    try {
        $checkTable = $pdo->query("SHOW TABLES LIKE 'consultations'");
        if ($checkTable->rowCount() == 0) {
            // Tạo bảng consultations nếu chưa có
            $createTable = "
                CREATE TABLE consultations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    full_name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    phone VARCHAR(20),
                    course_interest VARCHAR(255),
                    message TEXT,
                    status ENUM('new', 'processing', 'completed') DEFAULT 'new',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ";
            $pdo->exec($createTable);
        }
    } catch (Exception $e) {
        // Nếu có lỗi tạo bảng, vẫn tiếp tục
    }
    
    // Thêm yêu cầu tư vấn vào database
    $stmt = $pdo->prepare("
        INSERT INTO consultations (full_name, email, phone, course_interest, message, status, created_at) 
        VALUES (?, ?, ?, ?, ?, 'new', NOW())
    ");
    
    if ($stmt->execute([$fullName, $email, $phone, $courseInterest, $message])) {
        echo json_encode([
            'success' => true,
            'message' => 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.',
            'consultation_id' => $pdo->lastInsertId()
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại sau.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống. Vui lòng thử lại sau.'
    ]);
}
?>