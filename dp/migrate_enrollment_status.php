<?php
// Script để migrate trạng thái enrollment từ active/completed sang approved
require_once 'config.php';

try {
    echo "<h2>🔄 Migration: Cập nhật trạng thái đăng ký khóa học</h2>";
    
    // Kiểm tra các trạng thái hiện tại
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM enrollments GROUP BY status");
    $currentStatuses = $stmt->fetchAll();
    
    echo "<h3>📊 Trạng thái hiện tại:</h3>";
    foreach ($currentStatuses as $status) {
        echo "<p>- {$status['status']}: {$status['count']} đăng ký</p>";
    }
    
    // Cập nhật active và completed thành approved
    $stmt = $pdo->prepare("UPDATE enrollments SET status = 'approved' WHERE status IN ('active', 'completed')");
    $result = $stmt->execute();
    $affectedRows = $stmt->rowCount();
    
    if ($result) {
        echo "<h3>✅ Migration thành công!</h3>";
        echo "<p>Đã cập nhật {$affectedRows} đăng ký từ 'active'/'completed' thành 'approved'</p>";
    } else {
        echo "<h3>❌ Migration thất bại!</h3>";
        echo "<p>Có lỗi xảy ra khi cập nhật database</p>";
    }
    
    // Kiểm tra trạng thái sau migration
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM enrollments GROUP BY status");
    $newStatuses = $stmt->fetchAll();
    
    echo "<h3>📊 Trạng thái sau migration:</h3>";
    foreach ($newStatuses as $status) {
        echo "<p>- {$status['status']}: {$status['count']} đăng ký</p>";
    }
    
    echo "<h3>🎉 Hoàn thành!</h3>";
    echo "<p>Hệ thống đã được cập nhật để chỉ sử dụng 2 trạng thái:</p>";
    echo "<ul>";
    echo "<li><strong>pending</strong>: Chờ xử lý</li>";
    echo "<li><strong>approved</strong>: Đã duyệt</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h3>❌ Lỗi:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>