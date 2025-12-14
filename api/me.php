<?php
header('Content-Type: application/json');

require_once __DIR__ . '/auth.php';

$user = getAuthenticatedUser();

if (!$user) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized'
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'user' => $user
]);
exit;