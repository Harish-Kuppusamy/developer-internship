<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Dotenv\Dotenv;
use MongoDB\Client as MongoClient;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$mongoUri = $_ENV['MONGO_URI'] ?? '';

if (!$mongoUri) {
    die("MONGO_URI is not set in .env");
}

try {
    $mongoClient = new MongoClient($mongoUri);

    $mongoDb = $mongoClient->selectDatabase('developer-internship');

    $profilesCollection = $mongoDb->selectCollection('profiles');

} catch (Exception $e) {
    die("MongoDB connection failed: " . $e->getMessage());
}