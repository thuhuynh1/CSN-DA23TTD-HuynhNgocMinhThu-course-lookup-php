<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'session_id' => session_id(),
    'user_id' => $_SESSION['user_id'] ?? null,
    'is_logged_in' => isset($_SESSION['user_id']),
    'all_session_data' => $_SESSION
]);
?>