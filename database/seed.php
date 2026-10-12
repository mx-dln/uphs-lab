<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/database.php';
$db = $config['connections']['mysql'];

try {
    $pdo = new PDO(
        "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset={$db['charset']}",
        $db['username'],
        $db['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Seeding database...\n";

    // Check if users already exist
    $stmt = $pdo->query("SELECT COUNT(*) as c FROM users");
    if ($stmt->fetch()['c'] > 0) {
        echo "Users already exist, skipping user seeds.\n";
    } else {
        $users = [
            ['System Administrator', 'admin@labmanagement.com', password_hash('admin123', PASSWORD_BCRYPT), 1, 'active'],
            ['Laboratory Administrator', 'labadmin@labmanagement.com', password_hash('labadmin123', PASSWORD_BCRYPT), 2, 'active'],
            ['Laboratory Custodian', 'custodian@labmanagement.com', password_hash('custodian123', PASSWORD_BCRYPT), 3, 'active'],
            ['Faculty Member', 'faculty@labmanagement.com', password_hash('faculty123', PASSWORD_BCRYPT), 4, 'active'],
        ];
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role_id, status) VALUES (?, ?, ?, ?, ?)");
        foreach ($users as $u) {
            $stmt->execute($u);
            echo "  Created user: {$u[1]}\n";
        }
    }

    // Check if equipment categories exist
    $stmt = $pdo->query("SELECT COUNT(*) as c FROM equipment_categories");
    if ($stmt->fetch()['c'] > 0) {
        echo "Equipment categories already exist, skipping.\n";
    } else {
        $categories = ['System Unit', 'Monitor', 'Keyboard', 'Mouse', 'RAM', 'GPU', 'Power Supply', 'Motherboard', 'SSD', 'HDD', 'Network Device', 'Printer', 'Projector', 'Others'];
        $stmt = $pdo->prepare("INSERT INTO equipment_categories (name) VALUES (?)");
        foreach ($categories as $c) {
            $stmt->execute([$c]);
        }
        echo "  Created " . count($categories) . " equipment categories.\n";
    }

    echo "\nSeeding complete!\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}
