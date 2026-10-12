<?php
$pdo = new PDO('mysql:host=localhost;dbname=lab-management;charset=utf8mb4', 'root', '');
$stmt = $pdo->query('SELECT id, name, email, password FROM users');

$passwords = ['admin123', 'labadmin123', 'custodian123', 'faculty123'];

foreach ($stmt as $row) {
    $results = [];
    foreach ($passwords as $p) {
        if (password_verify($p, $row['password'])) {
            $results[] = "$p OK";
        }
    }
    echo $row['id'] . ' | ' . $row['email'] . ' | ' . (implode(', ', $results) ?: 'NO MATCH') . "\n";
}
