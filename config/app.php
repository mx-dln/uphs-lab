<?php

date_default_timezone_set('Asia/Manila');

return [
    'name' => $_ENV['APP_NAME'] ?? 'Computer Laboratory Management System',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost/computer-lab-system',
    'session_lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 120),
    'qr_size' => (int)($_ENV['QR_SIZE'] ?? 300),
    'qr_margin' => (int)($_ENV['QR_MARGIN'] ?? 10),
    'duplicate_scan_minutes' => 30,

    'database' => [
        'driver' => 'mysql',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'name' => $_ENV['DB_NAME'] ?? 'lab_management',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
];
