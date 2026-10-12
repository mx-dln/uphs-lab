<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/database.php';
$db = $config['connections']['mysql'];
$pdo = new PDO("mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4", $db['username'], $db['password']);

$stmt = $pdo->query("SELECT id, student_number, full_name, password FROM students");
echo "Students in DB:\n";
foreach ($stmt as $row) {
    $check = password_verify('student123', $row['password']);
    echo "  {$row['id']} | {$row['student_number']} | {$row['full_name']} | hash: " . substr($row['password'], 0, 20) . "... | student123: " . ($check ? 'OK' : 'FAIL') . "\n";
}
