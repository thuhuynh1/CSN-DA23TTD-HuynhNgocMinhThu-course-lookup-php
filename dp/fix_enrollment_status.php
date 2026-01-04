<?php
echo "<h1>🔧 Fix Enrollment Status Issue</h1>";

try {
    require_once 'config.php';
    echo "<p style='color: green;'>✅ Database connected</p>";
    
    // Check current status values
    echo "<h3>Current Status Values:</h3>";
    $stmt = $pdo->query("SELECT DISTINCT status FROM enrollments");
    $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Found statuses: " . implode(', ', $statuses) . "</p>";
    
    // Check table structure
    echo "<h3>Current Table Structure:</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM enrollments LIKE 'status'");
    $column = $stmt->fetch();
    echo "<p>Status column type: " . $column['Type'] . "</p>";
    
    // Fix 1: Update ENUM to include 'approved' and remove 'active', 'completed'
    echo "<h3>Fixing ENUM values...</h3>";
    $sql = "ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending', 'approved') DEFAULT 'pending'";
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Updated ENUM to use 'pending' and 'approved'</p>";
    
    // Fix 2: Update existing data
    echo "<h3>Updating existing data...</h3>";
    $stmt = $pdo->prepare("UPDATE enrollments SET status = 'approved' WHERE status IN ('active', 'completed')");
    $result = $stmt->execute();
    $affected = $stmt->rowCount();
    echo "<p style='color: green;'>✅ Updated $affected records from 'active'/'completed' to 'approved'</p>";
    
    // Verify changes
    echo "<h3>Verification:</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM enrollments LIKE 'status'");
    $column = $stmt->fetch();
    echo "<p>New status column type: " . $column['Type'] . "</p>";
    
    $stmt = $pdo->query("SELECT DISTINCT status FROM enrollments");
    $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Current statuses: " . implode(', ', $statuses) . "</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total, status FROM enrollments GROUP BY status");
    $counts = $stmt->fetchAll();
    echo "<p>Status counts:</p><ul>";
    foreach ($counts as $count) {
        echo "<li>{$count['status']}: {$count['total']}</li>";
    }
    echo "</ul>";
    
    echo "<h3 style='color: green;'>✅ Fix completed successfully!</h3>";
    echo "<p><a href='../admin.html'>Go to Admin Panel</a> | <a href='../test_enrollment_fix.html'>Test Enrollment Fix</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>