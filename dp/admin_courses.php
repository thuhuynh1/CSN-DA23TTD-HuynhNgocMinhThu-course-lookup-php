<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Tắt hiển thị lỗi
error_reporting(0);
ini_set('display_errors', 0);

try {
    session_start();
    require_once 'config.php';
    
    // Kiểm tra quyền admin
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        echo json_encode([
            'success' => false,
            'message' => 'Không có quyền truy cập'
        ]);
        exit;
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Kiểm tra nếu có ID cụ thể để lấy chi tiết
            $courseId = $_GET['id'] ?? null;
            
            if ($courseId) {
                // Lấy chi tiết một khóa học
                $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
                $stmt->execute([$courseId]);
                $course = $stmt->fetch();
                
                if ($course) {
                    // Parse features JSON
                    $course['features'] = json_decode($course['features'] ?? '[]', true);
                    
                    echo json_encode([
                        'success' => true,
                        'course' => $course
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Không tìm thấy khóa học'
                    ]);
                }
            } else {
                // Lấy danh sách khóa học
                $search = $_GET['search'] ?? '';
                $category = $_GET['category'] ?? '';
                $status = $_GET['status'] ?? '';
                
                $sql = "SELECT * FROM courses WHERE 1=1";
                $params = [];
                
                if ($search) {
                    $sql .= " AND (title LIKE ? OR description LIKE ?)";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                }
                
                if ($category) {
                    $sql .= " AND category = ?";
                    $params[] = $category;
                }
                
                if ($status) {
                    $sql .= " AND status = ?";
                    $params[] = $status;
                }
                
                $sql .= " ORDER BY created_at DESC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $courses = $stmt->fetchAll();
                
                // Parse features JSON cho mỗi khóa học
                foreach ($courses as &$course) {
                    $course['features'] = json_decode($course['features'] ?? '[]', true);
                }
                
                echo json_encode([
                    'success' => true,
                    'courses' => $courses
                ]);
            }
            break;
            
        case 'POST':
            // Thêm khóa học mới
            $input = json_decode(file_get_contents('php://input'), true);
            
            $title = trim($input['title'] ?? '');
            $description = trim($input['description'] ?? '');
            $category = trim($input['category'] ?? '');
            $price = floatval($input['price'] ?? 0);
            $duration = intval($input['duration'] ?? 0);
            $level = trim($input['level'] ?? 'Cơ bản');
            $schedule = trim($input['schedule'] ?? '');
            $features = $input['features'] ?? [];
            
            // Validation
            if (empty($title) || empty($category) || $price <= 0 || $duration <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vui lòng nhập đầy đủ thông tin hợp lệ'
                ]);
                exit;
            }
            
            // Kiểm tra tên khóa học đã tồn tại
            $stmt = $pdo->prepare("SELECT id FROM courses WHERE title = ?");
            $stmt->execute([$title]);
            if ($stmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Tên khóa học đã tồn tại'
                ]);
                exit;
            }
            
            // Thêm khóa học
            $stmt = $pdo->prepare("
                INSERT INTO courses (title, description, category, price, duration, level, schedule, features) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$title, $description, $category, $price, $duration, $level, $schedule, json_encode($features)])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Thêm khóa học thành công',
                    'course_id' => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi thêm khóa học'
                ]);
            }
            break;
            
        case 'PUT':
            // Cập nhật khóa học
            $input = json_decode(file_get_contents('php://input'), true);
            $courseId = $input['course_id'] ?? 0;
            
            if (!$courseId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID khóa học không hợp lệ'
                ]);
                exit;
            }
            
            // Cập nhật trạng thái hoặc thông tin khóa học
            if (isset($input['status'])) {
                // Chỉ cập nhật trạng thái
                $status = $input['status'];
                if (!in_array($status, ['active', 'inactive'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Trạng thái không hợp lệ'
                    ]);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE courses SET status = ? WHERE id = ?");
                if ($stmt->execute([$status, $courseId])) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Cập nhật trạng thái thành công'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Có lỗi xảy ra khi cập nhật'
                    ]);
                }
            } else {
                // Cập nhật toàn bộ thông tin khóa học
                $title = trim($input['title'] ?? '');
                $description = trim($input['description'] ?? '');
                $category = trim($input['category'] ?? '');
                $price = floatval($input['price'] ?? 0);
                $duration = intval($input['duration'] ?? 0);
                $level = trim($input['level'] ?? 'Cơ bản');
                $schedule = trim($input['schedule'] ?? '');
                $features = $input['features'] ?? [];
                
                if (empty($title) || empty($category) || $price <= 0 || $duration <= 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Vui lòng nhập đầy đủ thông tin hợp lệ'
                    ]);
                    exit;
                }
                
                $stmt = $pdo->prepare("
                    UPDATE courses 
                    SET title = ?, description = ?, category = ?, price = ?, duration = ?, 
                        level = ?, schedule = ?, features = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                if ($stmt->execute([$title, $description, $category, $price, $duration, $level, $schedule, json_encode($features), $courseId])) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Cập nhật khóa học thành công'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Có lỗi xảy ra khi cập nhật'
                    ]);
                }
            }
            break;
            
        case 'DELETE':
            // Xóa khóa học
            $input = json_decode(file_get_contents('php://input'), true);
            $courseId = $input['course_id'] ?? 0;
            
            if (!$courseId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID khóa học không hợp lệ'
                ]);
                exit;
            }
            
            // Kiểm tra xem có học viên nào đã đăng ký khóa học này chưa
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_title = (SELECT title FROM courses WHERE id = ?)");
            $stmt->execute([$courseId]);
            $enrollmentCount = $stmt->fetch()['count'];
            
            if ($enrollmentCount > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không thể xóa khóa học đã có học viên đăng ký. Vui lòng chuyển sang trạng thái "Không hoạt động".'
                ]);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            if ($stmt->execute([$courseId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Xóa khóa học thành công'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xóa khóa học'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Method không được hỗ trợ'
            ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}
?>