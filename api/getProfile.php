<?php

header('Content-Type: application/json');

require_once __DIR__ . '/auth.php'; 
require_once __DIR__ . '/config/mongo.php'; 

$user = getAuthenticatedUser();

if (!$user) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized'
    ]);
    exit;
}

$userId = (int) $user['user_id'];

try {
    $doc = $profilesCollection->findOne(['user_id' => $userId]);

    if ($doc) {
        $profile = [
            'user_id' => $doc['user_id'],
            'fullName' => $doc['fullName'] ?? '',
            'age' => $doc['age'] ?? '',
            'gender' => $doc['gender'] ?? '',
            'phone' => $doc['phone'] ?? '',
            'bio' => $doc['bio'] ?? '',
            'skills' => $doc['skills'] ?? [],
        ];

        echo json_encode([
            'status' => 'success',
            'profile' => $profile
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'profile' => null
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'MongoDB error: ' . $e->getMessage()
    ]);
}