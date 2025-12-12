<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Predis\Client as PredisClient;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$host = $_ENV['REDIS_HOST'] ?? '127.0.0.1';
$port = $_ENV['REDIS_PORT'] ?? 6379;
$password = $_ENV['REDIS_PASSWORD'] ?? null;
$db = (int)($_ENV['REDIS_DB'] ?? 0);

$config = [
    'scheme'   => 'tcp', 
    'host'     => $host,
    'port'     => $port,
    'database' => $db,
];

if (!empty($password)) {
    $config['password'] = $password;
}

try {
    $redis = new PredisClient($config);
} catch (Exception $e) {
    die("Redis connection failed: " . $e->getMessage());
}