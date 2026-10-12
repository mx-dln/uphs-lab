<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/database.php';
$db = $config['connections']['mysql'];
$pdo = new PDO("mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4", $db['username'], $db['password']);

echo "Running migration...\n";

try {
    $pdo->exec("ALTER TABLE attendance ADD COLUMN `time_out` TIME NULL AFTER `time_in`");
    echo "  [OK] Added time_out column\n";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate column')) {
        echo "  [SKIP] time_out column already exists\n";
    } else {
        echo "  [ERROR] " . $e->getMessage() . "\n";
    }
}

try {
    $pdo->exec("ALTER TABLE attendance DROP INDEX `unique_attendance`");
    echo "  [OK] Dropped unique_attendance constraint\n";
} catch (PDOException $e) {
    echo "  [SKIP] " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE attendance ADD INDEX `idx_attendance_student_lab_date` (`student_id`, `laboratory_id`, `attendance_date`)");
    echo "  [OK] Added index\n";
} catch (PDOException $e) {
    echo "  [SKIP] " . $e->getMessage() . "\n";
}

echo "\nMigration complete!\n";
