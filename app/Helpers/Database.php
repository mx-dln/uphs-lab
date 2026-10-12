<?php

namespace App\Helpers;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    private string $driver = 'sqlite';

    private function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $default = $config['default'] ?? 'sqlite';

        if ($default === 'mysql' && !empty($config['connections']['mysql']['host'])) {
            $db = $config['connections']['mysql'];
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset={$db['charset']}";
            try {
                $this->pdo = new PDO($dsn, $db['username'], $db['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                $this->driver = 'mysql';
                return;
            } catch (PDOException $e) {
                // MySQL unavailable, fall back to offline SQLite database
            }
        }

        // Connect to SQLite (Offline mode)
        $sqlitePath = $config['connections']['sqlite']['database'] ?? (dirname(__DIR__, 2) . '/database/lab-management.sqlite');
        
        // Ensure database directory exists
        if (!file_exists(dirname($sqlitePath))) {
            mkdir(dirname($sqlitePath), 0777, true);
        }

        $this->pdo = new PDO("sqlite:{$sqlitePath}", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $this->pdo->exec("PRAGMA foreign_keys = ON;");
        $this->driver = 'sqlite';

        // Register custom SQL functions for MySQL compatibility in SQLite
        $this->pdo->sqliteCreateFunction('CURDATE', fn() => date('Y-m-d'));
        $this->pdo->sqliteCreateFunction('NOW', fn() => date('Y-m-d H:i:s'));
        $this->pdo->sqliteCreateFunction('DATE', fn($val = null) => $val ? date('Y-m-d', strtotime((string)$val)) : date('Y-m-d'));
        $this->pdo->sqliteCreateFunction('MONTH', fn($val = null) => $val ? (int)date('m', strtotime((string)$val)) : (int)date('m'));
        $this->pdo->sqliteCreateFunction('YEAR', fn($val = null) => $val ? (int)date('Y', strtotime((string)$val)) : (int)date('Y'));
        $this->pdo->sqliteCreateFunction('YEARWEEK', fn($val = null) => $val ? date('oW', strtotime((string)$val)) : date('oW'));
        $this->pdo->sqliteCreateFunction('CONCAT', function(...$args) {
            return implode('', $args);
        });
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public static function connect(): PDO
    {
        return self::getInstance()->getConnection();
    }

    public static function driver(): string
    {
        return self::getInstance()->getDriver();
    }
}
