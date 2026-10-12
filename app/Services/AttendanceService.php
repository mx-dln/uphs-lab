<?php

namespace App\Services;

use App\Helpers\Database;

class AttendanceService
{
    public function recordAttendance(int $studentId, int $laboratoryId): array
    {
        $db = Database::connect();

        $settings = $this->getSettings();
        $duplicateMinutes = (int)($settings['duplicate_scan_minutes'] ?? 30);

        $stmt = $db->prepare("SELECT * FROM attendance WHERE student_id = ? AND laboratory_id = ? AND DATE(attendance_date) = CURDATE() ORDER BY time_in DESC LIMIT 1");
        $stmt->execute([$studentId, $laboratoryId]);
        $existing = $stmt->fetch();

        if ($existing && ($existing['time_out'] === null || $existing['time_out'] === '')) {
            $timeIn = strtotime($existing['time_in']);
            $diff = (time() - $timeIn) / 60;
            if ($diff < $duplicateMinutes) {
                return ['success' => false, 'message' => "Duplicate scan detected. Please wait {$duplicateMinutes} minutes between scans.", 'attendance' => $existing];
            }
        }

        $stmt = $db->prepare("INSERT INTO attendance (student_id, laboratory_id, attendance_date, time_in, ip_address, browser, session_id) VALUES (?, ?, CURDATE(), NOW(), ?, ?, ?)");
        $stmt->execute([
            $studentId,
            $laboratoryId,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            session_id(),
        ]);
        $attendanceId = (int)$db->lastInsertId();

        $this->logActivity($studentId, 'attendance_scan', "Attendance recorded for laboratory #{$laboratoryId}");

        $stmt = $db->prepare("SELECT a.*, l.name as laboratory_name, l.room_number FROM attendance a JOIN laboratories l ON a.laboratory_id = l.id WHERE a.id = ?");
        $stmt->execute([$attendanceId]);
        return ['success' => true, 'message' => 'Attendance recorded successfully.', 'attendance' => $stmt->fetch()];
    }

    public function getTodayAttendance(): array
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT a.*, s.full_name as student_name, s.student_number, l.name as laboratory_name FROM attendance a JOIN students s ON a.student_id = s.id JOIN laboratories l ON a.laboratory_id = l.id WHERE DATE(a.attendance_date) = CURDATE() ORDER BY a.time_in DESC");
        return $stmt->fetchAll();
    }

    public function getAttendanceByDate(string $date): array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT a.*, s.full_name as student_name, s.student_number, l.name as laboratory_name FROM attendance a JOIN students s ON a.student_id = s.id JOIN laboratories l ON a.laboratory_id = l.id WHERE DATE(a.attendance_date) = ? ORDER BY a.time_in DESC");
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }

    public function getStudentAttendance(int $studentId): array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT a.*, l.name as laboratory_name FROM attendance a JOIN laboratories l ON a.laboratory_id = l.id WHERE a.student_id = ? ORDER BY a.time_in DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function getLaboratoryAttendance(int $laboratoryId): array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT a.*, s.full_name as student_name, s.student_number FROM attendance a JOIN students s ON a.student_id = s.id WHERE a.laboratory_id = ? ORDER BY a.time_in DESC");
        $stmt->execute([$laboratoryId]);
        return $stmt->fetchAll();
    }

    public function getWeeklyAttendance(): int
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT COUNT(*) as total FROM attendance WHERE YEARWEEK(attendance_date) = YEARWEEK(CURDATE())");
        return (int)$stmt->fetch()['total'];
    }

    public function getMonthlyAttendance(): int
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT COUNT(*) as total FROM attendance WHERE MONTH(attendance_date) = MONTH(CURDATE()) AND YEAR(attendance_date) = YEAR(CURDATE())");
        return (int)$stmt->fetch()['total'];
    }

    public function getAttendanceTrend(int $days = 30): array
    {
        $db = Database::connect();
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        $stmt = $db->prepare("SELECT DATE(attendance_date) as date, COUNT(*) as count FROM attendance WHERE attendance_date >= ? GROUP BY DATE(attendance_date) ORDER BY date ASC");
        $stmt->execute([$startDate]);
        return $stmt->fetchAll();
    }

    private function getSettings(): array
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM settings");
        $result = [];
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }
        return $result;
    }

    private function logActivity(int $userId, string $action, string $description): void
    {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $description, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
    }
}
