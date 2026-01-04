<?php
// Script để thiết lập session admin tạm thời cho việc test
session_start();

// Thiết lập session admin
$_SESSION['is_admin'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'admin';

echo "<h2>✅ Admin Session đã được thiết lập</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>is_admin:</strong> " . ($_SESSION['is_admin'] ? 'true' : 'false') . "</p>";
echo "<p><strong>admin_id:</strong> " . $_SESSION['admin_id'] . "</p>";
echo "<p><strong>admin_username:</strong> " . $_SESSION['admin_username'] . "</p>";

echo "<h3>Bây giờ bạn có thể:</h3>";
echo "<ul>";
echo "<li><a href='../admin.html'>Truy cập Admin Panel</a></li>";
echo "<li><a href='debug_enrollment_update.php'>Debug Enrollment Update</a></li>";
echo "<li><a href='../test_enrollment_update.html'>Test Enrollment Update</a></li>";
echo "</ul>";

echo "<p style='color: orange;'><strong>Lưu ý:</strong> Đây chỉ là session tạm thời để test. Trong production, bạn cần đăng nhập admin đúng cách.</p>";
?>