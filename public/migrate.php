<?php
$host = "sql306.infinityfree.com";
$user = "if0_42405032";
$pass = "LGeHdI3ukkd7ia";
$db = "if0_42405032_lab";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
} catch (Exception $e) {
    die("DB Error: " . $e->getMessage());
}

echo "<h2>Migration</h2><pre>";

try {
    $pdo->exec("ALTER TABLE attendance ADD COLUMN `time_out` TIME NULL AFTER `time_in`");
    echo "[OK] Added time_out column\n";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate column')) {
        echo "[SKIP] time_out already exists\n";
    } else {
        echo "[ERROR] " . $e->getMessage() . "\n";
    }
}

try {
    $pdo->exec("ALTER TABLE attendance DROP INDEX `unique_attendance`");
    echo "[OK] Dropped unique constraint\n";
} catch (PDOException $e) {
    echo "[SKIP] " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE attendance ADD INDEX `idx_attendance_student_lab_date` (`student_id`, `laboratory_id`, `attendance_date`)");
    echo "[OK] Added index\n";
} catch (PDOException $e) {
    echo "[SKIP] " . $e->getMessage() . "\n";
}

echo "\nDone!</pre>";
echo '<p><a href="/">Go back</a></p>';
