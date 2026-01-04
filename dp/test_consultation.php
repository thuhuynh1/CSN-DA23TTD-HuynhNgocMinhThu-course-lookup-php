<?php
// Test file để kiểm tra consultation form
echo "<h2>🔍 Test Consultation Form</h2>";

// Test 1: Kiểm tra bảng consultations
try {
    require_once 'config.php';
    
    echo "<h3>1. Kiểm tra bảng consultations:</h3>";
    
    $checkTable = $pdo->query("SHOW TABLES LIKE 'consultations'");
    if ($checkTable->rowCount() > 0) {
        echo "✅ Bảng consultations đã tồn tại<br>";
        
        // Kiểm tra cấu trúc bảng
        $columns = $pdo->query("DESCRIBE consultations")->fetchAll();
        echo "<strong>Cấu trúc bảng:</strong><br>";
        foreach ($columns as $col) {
            echo "- {$col['Field']}: {$col['Type']}<br>";
        }
        
        // Đếm số record
        $count = $pdo->query("SELECT COUNT(*) as count FROM consultations")->fetch()['count'];
        echo "<strong>Số lượng yêu cầu tư vấn:</strong> $count<br>";
        
    } else {
        echo "❌ Bảng consultations chưa tồn tại<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 2: Test API submit_consultation.php
echo "<h3>2. Test API submit_consultation.php:</h3>";

$testData = [
    'full_name' => 'Nguyễn Test',
    'email' => 'test@example.com',
    'phone' => '0123456789',
    'course_interest' => 'Tiếng Anh',
    'message' => 'Đây là tin nhắn test'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/submit_consultation.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre>";

$data = json_decode($response, true);
if ($data && $data['success']) {
    echo "✅ API hoạt động tốt!<br>";
    echo "Message: " . $data['message'] . "<br>";
    if (isset($data['consultation_id'])) {
        echo "Consultation ID: " . $data['consultation_id'] . "<br>";
    }
} else {
    echo "❌ API có lỗi: " . ($data['message'] ?? 'Unknown error') . "<br>";
}

echo "<hr>";

// Test 3: Hiển thị form test
echo "<h3>3. Form Test:</h3>";
?>

<form id="testForm" style="max-width: 400px; margin: 20px 0;">
    <div style="margin-bottom: 15px;">
        <label>Họ tên:</label><br>
        <input type="text" name="full_name" value="Test User" style="width: 100%; padding: 8px;">
    </div>
    
    <div style="margin-bottom: 15px;">
        <label>Email:</label><br>
        <input type="email" name="email" value="test@example.com" style="width: 100%; padding: 8px;">
    </div>
    
    <div style="margin-bottom: 15px;">
        <label>Số điện thoại:</label><br>
        <input type="tel" name="phone" value="0123456789" style="width: 100%; padding: 8px;">
    </div>
    
    <div style="margin-bottom: 15px;">
        <label>Khóa học quan tâm:</label><br>
        <select name="course_interest" style="width: 100%; padding: 8px;">
            <option value="Tiếng Anh">Tiếng Anh</option>
            <option value="IELTS">IELTS</option>
            <option value="Lập trình">Lập trình</option>
        </select>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label>Tin nhắn:</label><br>
        <textarea name="message" style="width: 100%; padding: 8px; height: 80px;">Tôi muốn tư vấn về khóa học</textarea>
    </div>
    
    <button type="submit" style="background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
        Gửi Test
    </button>
</form>

<div id="testResult" style="margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 5px; display: none;"></div>

<script>
document.getElementById('testForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        full_name: formData.get('full_name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        course_interest: formData.get('course_interest'),
        message: formData.get('message')
    };
    
    const resultDiv = document.getElementById('testResult');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '⏳ Đang gửi...';
    
    fetch('submit_consultation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            resultDiv.innerHTML = '✅ <strong>Thành công!</strong><br>' + result.message;
            resultDiv.style.background = '#d4edda';
            resultDiv.style.color = '#155724';
        } else {
            resultDiv.innerHTML = '❌ <strong>Lỗi:</strong><br>' + result.message;
            resultDiv.style.background = '#f8d7da';
            resultDiv.style.color = '#721c24';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '❌ <strong>Lỗi kết nối:</strong><br>' + error.message;
        resultDiv.style.background = '#f8d7da';
        resultDiv.style.color = '#721c24';
    });
});
</script>

<?php
echo "<hr>";
echo "<h3>4. Liên kết:</h3>";
echo "<p><a href='../contact.html'>👉 Mở trang Contact</a></p>";
echo "<p><a href='../admin.html'>👉 Mở Admin Panel</a></p>";
?>