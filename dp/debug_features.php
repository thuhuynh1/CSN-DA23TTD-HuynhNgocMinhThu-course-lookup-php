<?php
require_once 'config.php';

echo "<h2>🔍 Debug Features Data</h2>";

try {
    // Kiểm tra dữ liệu courses
    echo "<h3>📚 Courses Features:</h3>";
    $stmt = $pdo->query("SELECT id, title, features FROM courses LIMIT 5");
    $courses = $stmt->fetchAll();
    
    foreach ($courses as $course) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<strong>ID:</strong> " . $course['id'] . "<br>";
        echo "<strong>Title:</strong> " . $course['title'] . "<br>";
        echo "<strong>Features (raw):</strong> " . $course['features'] . "<br>";
        
        $features = json_decode($course['features'], true);
        echo "<strong>Features (parsed):</strong> ";
        if ($features && is_array($features)) {
            echo "<ul>";
            foreach ($features as $feature) {
                echo "<li>" . htmlspecialchars($feature) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "No features or invalid JSON<br>";
        }
        echo "</div>";
    }
    
    // Kiểm tra dữ liệu enrollments
    echo "<h3>📝 Enrollments Features:</h3>";
    $stmt = $pdo->query("SELECT id, course_title, course_features FROM enrollments LIMIT 5");
    $enrollments = $stmt->fetchAll();
    
    if (empty($enrollments)) {
        echo "<p>No enrollments found.</p>";
    } else {
        foreach ($enrollments as $enrollment) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<strong>ID:</strong> " . $enrollment['id'] . "<br>";
            echo "<strong>Course:</strong> " . $enrollment['course_title'] . "<br>";
            echo "<strong>Features (raw):</strong> " . $enrollment['course_features'] . "<br>";
            
            $features = json_decode($enrollment['course_features'], true);
            echo "<strong>Features (parsed):</strong> ";
            if ($features && is_array($features)) {
                echo "<ul>";
                foreach ($features as $feature) {
                    echo "<li>" . htmlspecialchars($feature) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "No features or invalid JSON<br>";
            }
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>