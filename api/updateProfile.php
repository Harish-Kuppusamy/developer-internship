<?php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

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
$fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
$age = isset($_POST['age']) ? trim($_POST['age']) : '';
$gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
$skillsJson = $_POST['skills'] ?? '[]';

if ($fullName === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Full name is required'
    ]);
    exit;
}

$skills = json_decode($skillsJson, true);
if (!is_array($skills)) {
    $skills = [];
}

$profileData = [
    'user_id' => $userId,
    'fullName' => $fullName,
    'gender' => $gender,
    'phone' => $phone,
    'bio' => $bio,
    'skills' => $skills,
];

if ($age !== '') {
    $profileData['age'] = (int) $age;
}

try {
    // upsert: insert if not exists, update if exists
    $profilesCollection->updateOne(
        ['user_id' => $userId],
        ['$set' => $profileData],
        ['upsert' => true]
    );

    echo json_encode([
        'status' => 'success',
        'message' => 'Profile saved'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'MongoDB error: ' . $e->getMessage()
    ]);
}