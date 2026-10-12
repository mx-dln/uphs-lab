<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

require __DIR__ . '/../app/Helpers/Functions.php';

use App\Models\Student;

echo "=== Testing findByStudentNumber ===\n";
$student = Student::findByStudentNumber('2024-0001');
if ($student) {
    echo "Found: {$student['full_name']} ({$student['student_number']})\n";
    echo "Password hash: " . substr($student['password'], 0, 30) . "...\n";
    echo "Password verify 'student123': " . (password_verify('student123', $student['password']) ? 'OK' : 'FAIL') . "\n";
} else {
    echo "Student NOT FOUND by number '2024-0001'\n";
    // Try all students
    $all = Student::all();
    echo "All students in DB:\n";
    foreach ($all as $s) {
        echo "  [{$s['id']}] {$s['student_number']} - {$s['full_name']}\n";
    }
}

echo "\n=== Testing whereFirst ===\n";
$result = Student::whereFirst('student_number', '2024-0001');
echo "whereFirst result: " . ($result ? $result['full_name'] : 'NULL') . "\n";
