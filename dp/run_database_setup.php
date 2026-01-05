<?php
// Script để chạy lại database setup
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>🔧 Chạy Database Setup</h3>";

try {
    require_once 'config.php';
    
    // Đọc file SQL
    $sqlFile = '../database_setup.sql';
    if (!file_exists($sqlFile)) {
        echo "<p style='color: red;'>❌ Không tìm thấy file database_setup.sql</p>";
        exit;
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Tách các câu lệnh SQL
    $statements = explode(';', $sql);
    
    $successCount = 0;
    $errorCount = 0;
    
    echo "<p>Đang thực thi " . count($statements) . " câu lệnh SQL...</p>";
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        
        // Bỏ qua câu lệnh trống và comment
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $successCount++;
            echo "<p style='color: green;'>✅ Thành công: " . substr($statement, 0, 50) . "...</p>";
        } catch (Exception $e) {
            $errorCount++;
            echo "<p style='color: orange;'>⚠️ Bỏ qua: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h4>📊 Kết quả:</h4>";
    echo "<p><strong>Thành công:</strong> $successCount câu lệnh</p>";
    echo "<p><strong>Bỏ qua:</strong> $errorCount câu lệnh</p>";
    echo "<p style='color: green;'>✅ Hoàn thành setup database!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
}
?>