<?php

header('Content-Type: application/json');

require_once __DIR__ . '/config/redis.php';

$headers = function_exists('getallheaders') ? getallheaders() : [];
$token = null;

if (isset($headers['X-Session-Token'])) {
    $token = trim($headers['X-Session-Token']);
} elseif (isset($_POST['token'])) {
    $token = trim($_POST['token']);
}

if (!$token) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No token provided'
    ]);
    exit;
}

$sessionKey = "session_" . $token;

$redis->del([$sessionKey]);

echo json_encode([
    'status' => 'success',
    'message' => 'Logged out successfully'
]);
exit;
