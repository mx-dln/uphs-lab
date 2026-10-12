<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/database.php';
$db = $config['connections']['mysql'];
$pdo = new PDO("mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4", $db['username'], $db['password']);

// Check labs
echo "=== LABORATORIES ===\n";
$stmt = $pdo->query("SELECT id, name, qr_code FROM laboratories");
foreach ($stmt as $row) {
    echo "ID: {$row['id']}, Name: {$row['name']}, QR: " . ($row['qr_code'] ?? 'NULL') . "\n";
    if ($row['qr_code']) {
        $fullPath = __DIR__ . '/../' . $row['qr_code'];
        echo "  Full path: $fullPath\n";
        echo "  Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    }
}

// Check equipment
echo "\n=== EQUIPMENT ===\n";
$stmt = $pdo->query("SELECT id, name, asset_tag, qr_code FROM equipment LIMIT 5");
foreach ($stmt as $row) {
    echo "ID: {$row['id']}, Name: {$row['name']}, QR: " . ($row['qr_code'] ?? 'NULL') . "\n";
    if ($row['qr_code']) {
        $fullPath = __DIR__ . '/../' . $row['qr_code'];
        echo "  Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    }
}

// Check directory
echo "\n=== QR DIRECTORY ===\n";
$dir = __DIR__ . '/../public/qr/';
echo "Dir: $dir\n";
$files = glob($dir . '*');
echo "Files: " . count($files) . "\n";
foreach ($files as $f) {
    echo "  " . basename($f) . " (" . filesize($f) . " bytes)\n";
}
