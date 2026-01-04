<?php
// Test file để kiểm tra đăng nhập admin
echo "Testing admin login...\n";

// Test 1: Kiểm tra API admin_login.php
echo "1. Testing admin_login.php API:\n";
$postData = json_encode([
    'email' => 'adthu@gmail.com',
    'password' => 'ad123456'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $postData
    ]
]);

$result = file_get_contents('http://localhost/CSN/dp/admin_login.php', false, $context);
echo "Login API Response: " . $result . "\n\n";

// Test 2: Kiểm tra API admin_check.php
echo "2. Testing admin_check.php API:\n";
$checkResult = file_get_contents('http://localhost/CSN/dp/admin_check.php');
echo "Check API Response: " . $checkResult . "\n\n";

// Test 3: Kiểm tra database connection
echo "3. Testing database connection:\n";
try {
    require_once 'dp/config.php';
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins WHERE email = 'adthu@gmail.com'");
    $count = $stmt->fetch()['count'];
    echo "Admin exists in database: " . ($count > 0 ? 'YES' : 'NO') . "\n";
    
    if ($count > 0) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = 'adthu@gmail.com'");
        $stmt->execute();
        $admin = $stmt->fetch();
        echo "Admin data: " . json_encode($admin) . "\n";
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>