<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/database.php';
$db = $config['connections']['mysql'];
$pdo = new PDO("mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4", $db['username'], $db['password']);

$students = [
    ['2024-0001', 'Juan Dela Cruz', 'BSIT', '3rd Year', 'A', 'juan@example.com', 'student123'],
    ['2024-0002', 'Maria Santos', 'BSCS', '2nd Year', 'B', 'maria@example.com', 'student123'],
    ['2024-0003', 'Pedro Reyes', 'BSIT', '4th Year', 'A', 'pedro@example.com', 'student123'],
];

$check = $pdo->prepare("SELECT COUNT(*) FROM students WHERE student_number = ?");
$insert = $pdo->prepare("INSERT INTO students (student_number, full_name, course, year_level, section, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");

foreach ($students as $s) {
    $check->execute([$s[0]]);
    if ($check->fetchColumn() == 0) {
        $insert->execute([$s[0], $s[1], $s[2], $s[3], $s[4], $s[5], password_hash($s[6], PASSWORD_BCRYPT)]);
        echo "Added student: {$s[0]} - {$s[1]}\n";
    } else {
        echo "Already exists: {$s[0]}\n";
    }
}
