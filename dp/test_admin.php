<?php
// Test file đơn giản để kiểm tra admin dashboard
session_start();

header('Content-Type: application/json');

try {
    require_once 'config.php';
    
    // Test database connection
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'] ?? 0;
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM enrollments");
    $enrollmentCount = $stmt->fetch()['count'] ?? 0;
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM consultations");
    $consultationCount = $stmt->fetch()['count'] ?? 0;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'totalUsers' => $userCount,
            'totalCourses' => 6,
            'totalEnrollments' => $enrollmentCount,
            'totalConsultations' => $consultationCount
        ],
        'recentActivities' => []
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>