<?php
$pdo = new PDO('mysql:host=localhost;dbname=lab-management;charset=utf8mb4', 'root', '');
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");

$updates = [
    ['admin@labmanagement.com', password_hash('admin123', PASSWORD_BCRYPT)],
];

foreach ($updates as [$email, $hash]) {
    $stmt->execute([$hash, $email]);
    echo "Fixed password for $email\n";
}
