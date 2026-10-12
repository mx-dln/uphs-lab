<?php

namespace App\Models;

use App\Helpers\BaseModel;

class Student extends BaseModel
{
    protected static string $table = 'students';
    protected static array $fillable = [
        'student_number', 'full_name', 'course', 'year_level',
        'section', 'email', 'password', 'status',
        'or_number', 'semester', 'or_date',
    ];
    protected static array $searchable = [
        'student_number', 'full_name', 'email', 'or_number',
    ];

    public static function findByStudentNumber(string $number): ?array
    {
        return self::whereFirst('student_number', $number);
    }

    public static function importFromCsv(array $rows): array
    {
        $imported = 0;
        $errors = [];
        foreach ($rows as $index => $row) {
            try {
                $data = [
                    'student_number' => $row[0] ?? '',
                    'full_name' => $row[1] ?? '',
                    'course' => $row[2] ?? '',
                    'year_level' => $row[3] ?? '',
                    'section' => $row[4] ?? '',
                    'email' => $row[5] ?? '',
                    'password' => password_hash($row[6] ?? 'password123', PASSWORD_BCRYPT),
                    'or_number' => $row[7] ?? '',
                    'semester' => !empty($row[8]) ? $row[8] : 'First Semester',
                    'or_date' => !empty($row[9]) ? $row[9] : date('Y-m-d'),
                ];
                if (empty($data['student_number'])) {
                    $errors[] = "Row {$index}: Student number is empty.";
                    continue;
                }
                $existing = self::findByStudentNumber($data['student_number']);
                if ($existing) {
                    $errors[] = "Row {$index}: Duplicate student number.";
                    continue;
                }
                self::create($data);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$index}: {$e->getMessage()}";
            }
        }
        return ['imported' => $imported, 'errors' => $errors];
    }
}
