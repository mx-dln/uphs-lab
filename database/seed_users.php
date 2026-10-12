<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/database.php';
$db = $config['connections']['mysql'];
$pdo = new PDO("mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4", $db['username'], $db['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$users = [
    ['Laboratory Administrator', 'labadmin@labmanagement.com', password_hash('labadmin123', PASSWORD_BCRYPT), 2, 'active'],
    ['Laboratory Custodian', 'custodian@labmanagement.com', password_hash('custodian123', PASSWORD_BCRYPT), 3, 'active'],
    ['Faculty Member', 'faculty@labmanagement.com', password_hash('faculty123', PASSWORD_BCRYPT), 4, 'active'],
];

$check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
$insert = $pdo->prepare('INSERT INTO users (name, email, password, role_id, status) VALUES (?, ?, ?, ?, ?)');

foreach ($users as $u) {
    $check->execute([$u[1]]);
    if ($check->fetchColumn() == 0) {
        $insert->execute($u);
        echo "Added: {$u[1]}\n";
    } else {
        echo "Already exists: {$u[1]}\n";
    }
}
