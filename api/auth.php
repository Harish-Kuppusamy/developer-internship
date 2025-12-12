<?php
require_once __DIR__ . '/config/redis.php';

function getAuthenticatedUser()
{
    $headers = function_exists('getallheaders') ? getallheaders() : [];

    $token = null;

    if (isset($headers['X-Session-Token'])) {
        $token = trim($headers['X-Session-Token']);
    } elseif (isset($_POST['token'])) {
        $token = trim($_POST['token']);
    } elseif (isset($_GET['token'])) {
        $token = trim($_GET['token']);
    }

    if (!$token) {
        return null;
    }

    global $redis;

    $sessionKey = "session_" . $token;
    $sessionJson = $redis->get($sessionKey);

    if (!$sessionJson) {
        return null;
    }

    $sessionData = json_decode($sessionJson, true);

    if (!isset($sessionData['user_id'])) {
        return null;
    }

    return $sessionData;
}