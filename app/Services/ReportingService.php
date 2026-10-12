<?php

namespace App\Services;

use App\Helpers\Database;

class ReportingService
{
    public function attendanceReport(string $startDate, string $endDate, int $laboratoryId = null): array
    {
        $db = Database::connect();
        $sql = "SELECT a.*, s.full_name as student_name, s.student_number, s.course, l.name as laboratory_name FROM attendance a JOIN students s ON a.student_id = s.id JOIN laboratories l ON a.laboratory_id = l.id WHERE DATE(a.attendance_date) BETWEEN ? AND ?";
        $params = [$startDate, $endDate];
        if ($laboratoryId) {
            $sql .= " AND a.laboratory_id = ?";
            $params[] = $laboratoryId;
        }
        $sql .= " ORDER BY a.attendance_date DESC, a.time_in DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function studentAttendanceSummary(int $studentId = null): array
    {
        $db = Database::connect();
        $sql = "SELECT s.id, s.full_name, s.student_number, s.course, COUNT(a.id) as total_attendance FROM students s LEFT JOIN attendance a ON s.id = a.student_id";
        $params = [];
        if ($studentId) {
            $sql .= " WHERE s.id = ?";
            $params[] = $studentId;
        }
        $sql .= " GROUP BY s.id ORDER BY total_attendance DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function laboratoryUsageReport(string $startDate, string $endDate): array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT l.id, l.name, l.room_number, l.building, COUNT(a.id) as total_visits, COUNT(DISTINCT a.student_id) as unique_students FROM laboratories l LEFT JOIN attendance a ON l.id = a.laboratory_id AND DATE(a.attendance_date) BETWEEN ? AND ? GROUP BY l.id ORDER BY total_visits DESC");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }

    public function equipmentInventoryReport(int $laboratoryId = null): array
    {
        $db = Database::connect();
        $sql = "SELECT e.*, ec.name as category_name, l.name as laboratory_name FROM equipment e JOIN equipment_categories ec ON e.category_id = ec.id JOIN laboratories l ON e.laboratory_id = l.id";
        $params = [];
        if ($laboratoryId) {
            $sql .= " WHERE e.laboratory_id = ?";
            $params[] = $laboratoryId;
        }
        $sql .= " ORDER BY e.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function missingEquipmentReport(): array
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT e.*, ec.name as category_name, l.name as laboratory_name FROM equipment e JOIN equipment_categories ec ON e.category_id = ec.id JOIN laboratories l ON e.laboratory_id = l.id WHERE e.status = 'missing' ORDER BY e.updated_at DESC");
        return $stmt->fetchAll();
    }

    public function defectiveEquipmentReport(): array
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT e.*, ec.name as category_name, l.name as laboratory_name FROM equipment e JOIN equipment_categories ec ON e.category_id = ec.id JOIN laboratories l ON e.laboratory_id = l.id WHERE e.status = 'defective' ORDER BY e.updated_at DESC");
        return $stmt->fetchAll();
    }

    public function auditReport(int $auditId = null): array
    {
        $db = Database::connect();
        if ($auditId) {
            $stmt = $db->prepare("SELECT a.*, u.name as conducted_by_name, l.name as laboratory_name FROM audits a JOIN users u ON a.conducted_by = u.id JOIN laboratories l ON a.laboratory_id = l.id WHERE a.id = ?");
            $stmt->execute([$auditId]);
            return $stmt->fetchAll();
        }
        $stmt = $db->query("SELECT a.*, u.name as conducted_by_name, l.name as laboratory_name FROM audits a JOIN users u ON a.conducted_by = u.id JOIN laboratories l ON a.laboratory_id = l.id ORDER BY a.created_at DESC");
        return $stmt->fetchAll();
    }

    public function borrowingReport(string $startDate = null, string $endDate = null): array
    {
        $db = Database::connect();
        $sql = "SELECT b.*, e.name as equipment_name, e.asset_tag, u.name as borrower_name FROM borrowings b JOIN equipment e ON b.equipment_id = e.id JOIN users u ON b.borrower_id = u.id";
        $params = [];
        if ($startDate && $endDate) {
            $sql .= " WHERE DATE(b.date_borrowed) BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }
        $sql .= " ORDER BY b.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
