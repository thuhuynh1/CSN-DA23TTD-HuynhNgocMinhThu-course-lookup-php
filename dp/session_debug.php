<?php
// File debug để kiểm tra cấu hình session
session_start();

header('Content-Type: application/json');

echo json_encode([
    'session_info' => [
        'session_id' => session_id(),
        'session_status' => session_status(),
        'session_name' => session_name(),
        'session_save_path' => session_save_path(),
        'session_cookie_params' => session_get_cookie_params(),
    ],
    'php_session_config' => [
        'session.cookie_lifetime' => ini_get('session.cookie_lifetime'),
        'session.gc_maxlifetime' => ini_get('session.gc_maxlifetime'),
        'session.cookie_httponly' => ini_get('session.cookie_httponly'),
        'session.use_strict_mode' => ini_get('session.use_strict_mode'),
        'session.cookie_secure' => ini_get('session.cookie_secure'),
        'session.cookie_samesite' => ini_get('session.cookie_samesite'),
    ],
    'current_session_data' => $_SESSION,
    'cookies' => $_COOKIE,
    'server_info' => [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'https' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'host' => $_SERVER['HTTP_HOST'] ?? 'Unknown'
    ]
], JSON_PRETTY_PRINT);
?>