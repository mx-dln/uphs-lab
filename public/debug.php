<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
} catch (Exception $e) {
    die("Autoload Error: " . $e->getMessage());
}

try {
    $config = require __DIR__ . '/../config/database.php';
    $db = $config['connections']['mysql'];
    $pdo = new PDO("mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4", $db['username'], $db['password']);
} catch (Exception $e) {
    die("DB Error: " . $e->getMessage());
}

echo "<h2>Scan Debug</h2>";

echo "<h3>Session Student:</h3><pre>";
echo isset($_SESSION['student']) ? json_encode($_SESSION['student'], JSON_PRETTY_PRINT) : "NONE";
echo "</pre>";

echo "<h3>Today's Attendance:</h3><pre>";
try {
    $stmt = $pdo->query("SELECT a.*, s.full_name, s.student_number, l.name as lab_name FROM attendance a JOIN students s ON a.student_id = s.id JOIN laboratories l ON a.laboratory_id = l.id WHERE DATE(a.attendance_date) = CURDATE() ORDER BY a.time_in DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($rows) {
        foreach ($rows as $r) {
            echo "ID:{$r['id']} | {$r['student_number']} {$r['full_name']} | Lab:{$r['lab_name']} | In:{$r['time_in']} | Out:{$r['time_out'] ?? '---'}\n";
        }
    } else {
        echo "No attendance today\n";
    }
} catch (Exception $e) {
    echo "Query Error: " . $e->getMessage() . "\n";
}
echo "</pre>";

echo "<a href='?clear=1'>Clear today</a>";
if (isset($_GET['clear'])) {
    $pdo->exec("DELETE FROM attendance WHERE DATE(attendance_date) = CURDATE()");
    echo "<meta http-equiv='refresh' content='0'>";
}
