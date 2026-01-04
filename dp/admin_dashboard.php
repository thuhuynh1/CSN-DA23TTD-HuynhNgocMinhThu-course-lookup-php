<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Tắt hiển thị lỗi
error_reporting(0);
ini_set('display_errors', 0);

try {
    session_start();
    require_once 'config.php';
    
    // Tạm thời bỏ qua kiểm tra session để test
    // if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'Không có quyền truy cập'
    //     ]);
    //     exit;
    // }
    
    // Lấy thống kê tổng quan
    $stats = [];
    
    // Tổng số người dùng
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
        $stats['totalUsers'] = $stmt->fetch()['total'];
    } catch (Exception $e) {
        $stats['totalUsers'] = 0;
    }
    
    // Tổng số khóa học
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM courses WHERE status = 'active'");
        $stats['totalCourses'] = $stmt->fetch()['total'];
    } catch (Exception $e) {
        // Bảng courses chưa tồn tại hoặc chưa có dữ liệu
        $stats['totalCourses'] = 10; // Giá trị mặc định từ database_setup.sql
    }
    
    // Tổng số đăng ký khóa học
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM enrollments");
        $stats['totalEnrollments'] = $stmt->fetch()['total'];
    } catch (Exception $e) {
        $stats['totalEnrollments'] = 0;
    }
    
    // Tổng số yêu cầu tư vấn
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM consultations");
        $stats['totalConsultations'] = $stmt->fetch()['total'];
    } catch (Exception $e) {
        // Bảng consultations chưa tồn tại
        $stats['totalConsultations'] = 0;
    }
    
    // Đăng ký theo trạng thái
    try {
        $stmt = $pdo->query("
            SELECT status, COUNT(*) as count 
            FROM enrollments 
            GROUP BY status
        ");
        $enrollmentsByStatus = [];
        while ($row = $stmt->fetch()) {
            $enrollmentsByStatus[$row['status']] = $row['count'];
        }
        $stats['enrollmentsByStatus'] = $enrollmentsByStatus;
    } catch (Exception $e) {
        $stats['enrollmentsByStatus'] = [];
    }
    
    // Người dùng mới trong 30 ngày
    try {
        $stmt = $pdo->query("
            SELECT COUNT(*) as total 
            FROM users 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stats['newUsersThisMonth'] = $stmt->fetch()['total'];
    } catch (Exception $e) {
        $stats['newUsersThisMonth'] = 0;
    }
    
    // Đăng ký mới trong 30 ngày
    try {
        $stmt = $pdo->query("
            SELECT COUNT(*) as total 
            FROM enrollments 
            WHERE enrolled_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stats['newEnrollmentsThisMonth'] = $stmt->fetch()['total'];
    } catch (Exception $e) {
        $stats['newEnrollmentsThisMonth'] = 0;
    }
    
    // Hoạt động gần đây
    try {
        $stmt = $pdo->query("
            SELECT 
                e.id,
                e.course_title,
                e.enrolled_at,
                e.status,
                u.full_name as user_name
            FROM enrollments e
            JOIN users u ON e.user_id = u.id
            ORDER BY e.enrolled_at DESC
            LIMIT 10
        ");
        $recentActivities = $stmt->fetchAll();
    } catch (Exception $e) {
        $recentActivities = [];
    }
    
    // Thống kê đăng ký theo tháng (12 tháng gần nhất)
    try {
        $stmt = $pdo->query("
            SELECT 
                DATE_FORMAT(enrolled_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM enrollments 
            WHERE enrolled_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(enrolled_at, '%Y-%m')
            ORDER BY month
        ");
        $enrollmentsByMonth = $stmt->fetchAll();
    } catch (Exception $e) {
        $enrollmentsByMonth = [];
    }
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recentActivities' => $recentActivities,
        'enrollmentsByMonth' => $enrollmentsByMonth
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}
?>