<?php

/**
 * Database Migration Runner
 * Usage: php database/migrate.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

echo "Running migrations...\n";

$sql = file_get_contents(__DIR__ . '/schema.sql');

$statements = explode(';', $sql);

$config = require __DIR__ . '/../config/database.php';
$db = $config['connections']['mysql'];

try {
    $pdo = new PDO(
        "mysql:host={$db['host']};port={$db['port']};charset={$db['charset']}",
        $db['username'],
        $db['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "✓ Executed: " . substr($statement, 0, 60) . "...\n";
            } catch (PDOException $e) {
                if (str_contains($e->getMessage(), 'already exists')) {
                    echo "→ Skipped (already exists): " . substr($statement, 0, 60) . "...\n";
                } else {
                    echo "✗ Error: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    echo "\nMigration completed!\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}
