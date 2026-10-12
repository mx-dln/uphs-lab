<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Helpers\Database;
use App\Models\Equipment;
use App\Models\Investigation;
use App\Models\Student;
use App\Models\Laboratory;

class SearchController extends BaseController
{
    /**
     * Search & Investigation Overview
     * "Investigation -> change into search"
     */
    public function index(): void
    {
        $db = Database::connect();
        $query = trim($_GET['q'] ?? ($_GET['search'] ?? ''));
        $tab = $_GET['tab'] ?? 'all';

        // 1. Investigation / Incident records
        $investigationsSql = "
            SELECT i.*, e.name as equipment_name, e.asset_tag, e.property_code, u.name as initiated_by_name, l.name as laboratory_name
            FROM investigations i
            JOIN equipment e ON i.equipment_id = e.id
            JOIN users u ON i.initiated_by = u.id
            JOIN laboratories l ON e.laboratory_id = l.id
            WHERE 1=1
        ";
        $invParams = [];
        if ($query) {
            $investigationsSql .= " AND (e.name LIKE ? OR e.asset_tag LIKE ? OR e.property_code LIKE ? OR i.status LIKE ?)";
            $invParams[] = "%{$query}%";
            $invParams[] = "%{$query}%";
            $invParams[] = "%{$query}%";
            $invParams[] = "%{$query}%";
        }
        $investigationsSql .= " ORDER BY i.created_at DESC";
        $stmtInv = $db->prepare($investigationsSql);
        $stmtInv->execute($invParams);
        $investigations = $stmtInv->fetchAll();

        // 2. Equipment Search Results
        $equipmentResults = [];
        if ($query) {
            $eqSql = "
                SELECT e.*, ec.name as category_name, l.name as laboratory_name 
                FROM equipment e 
                JOIN equipment_categories ec ON e.category_id = ec.id 
                JOIN laboratories l ON e.laboratory_id = l.id 
                WHERE e.name LIKE ? OR e.asset_tag LIKE ? OR e.property_code LIKE ? OR e.serial_number LIKE ?
                ORDER BY e.name ASC LIMIT 20
            ";
            $stmtEq = $db->prepare($eqSql);
            $stmtEq->execute(["%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"]);
            $equipmentResults = $stmtEq->fetchAll();
        }

        // 3. Student Search Results (by name, student number, or 1st Sem OR)
        $studentResults = [];
        if ($query) {
            $stSql = "
                SELECT s.* FROM students s 
                WHERE s.full_name LIKE ? OR s.student_number LIKE ? OR s.or_number LIKE ?
                ORDER BY s.full_name ASC LIMIT 20
            ";
            $stmtSt = $db->prepare($stSql);
            $stmtSt->execute(["%{$query}%", "%{$query}%", "%{$query}%"]);
            $studentResults = $stmtSt->fetchAll();
        }

        view('search.index', compact('investigations', 'equipmentResults', 'studentResults', 'query', 'tab'));
    }

    public function show(int $id): void
    {
        $investigation = Investigation::rawOne(
            "SELECT i.*, e.name as equipment_name, e.asset_tag, e.property_code, e.serial_number, e.status as equipment_status,
                    e.laboratory_id, u.name as initiated_by_name, l.name as laboratory_name, l.room_number
             FROM investigations i
             JOIN equipment e ON i.equipment_id = e.id
             JOIN users u ON i.initiated_by = u.id
             JOIN laboratories l ON e.laboratory_id = l.id
             WHERE i.id = ?", [$id]
        );

        if (!$investigation) {
            view('errors.404');
            return;
        }

        $db = Database::connect();

        // Get last completed audit date for this equipment
        $stmt = $db->prepare("SELECT MAX(a.audit_date) as last_audit_date FROM audits a JOIN audit_items ai ON a.id = ai.audit_id WHERE ai.equipment_id = ? AND a.status = 'completed'");
        $stmt->execute([$investigation['equipment_id']]);
        $lastAudit = $stmt->fetch();

        // Get last seen date (most recent attendance in the same lab)
        $stmt = $db->prepare("SELECT MAX(a.attendance_date) as last_seen, MAX(a.time_in) as last_time FROM attendance a WHERE a.laboratory_id = ?");
        $stmt->execute([$investigation['laboratory_id']]);
        $lastSeen = $stmt->fetch();

        // Get students who attended this lab since last audit
        $sinceDate = $lastAudit['last_audit_date'] ?? date('Y-m-d', strtotime('-90 days'));
        $stmt = $db->prepare("SELECT s.*, a.attendance_date, a.time_in FROM attendance a JOIN students s ON a.student_id = s.id WHERE a.laboratory_id = ? AND a.attendance_date >= ? ORDER BY a.attendance_date DESC, a.time_in DESC");
        $stmt->execute([$investigation['laboratory_id'], $sinceDate]);
        $studentsAfterAudit = $stmt->fetchAll();

        view('search.show', compact('investigation', 'lastAudit', 'lastSeen', 'studentsAfterAudit'));
    }

    public function store(): void
    {
        $data = [
            'equipment_id' => (int)($_POST['equipment_id'] ?? 0),
            'audit_id' => !empty($_POST['audit_id']) ? (int)$_POST['audit_id'] : null,
            'initiated_by' => $_SESSION['user']['id'] ?? 1,
            'status' => 'open',
        ];
        Investigation::create($data);
        session_flash('success', 'Search & incident tracking record initiated.');
        redirect('/search');
    }

    public function resolve(int $id): void
    {
        Investigation::update($id, [
            'status' => 'resolved',
            'findings' => $_POST['findings'] ?? '',
            'recommendations' => $_POST['recommendations'] ?? '',
            'resolved_at' => date('Y-m-d H:i:s'),
        ]);
        session_flash('success', 'Search investigation resolved.');
        redirect('/search/' . $id);
    }

    public function bulk(): void
    {
        $auditId = (int)($_POST['audit_id'] ?? 0);
        if (!$auditId) {
            session_flash('error', 'No audit specified.');
            redirect('/audits');
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare("SELECT equipment_id FROM audit_items WHERE audit_id = ? AND status = 'missing'");
        $stmt->execute([$auditId]);
        $missingItems = $stmt->fetchAll();

        $created = 0;
        foreach ($missingItems as $item) {
            $check = $db->prepare("SELECT COUNT(*) FROM investigations WHERE equipment_id = ? AND status IN ('open', 'under_review')");
            $check->execute([$item['equipment_id']]);
            if ($check->fetchColumn() == 0) {
                Investigation::create([
                    'equipment_id' => $item['equipment_id'],
                    'audit_id' => $auditId,
                    'initiated_by' => $_SESSION['user']['id'] ?? 1,
                    'status' => 'open',
                ]);
                $created++;
            }
        }

        session_flash('success', "{$created} search tracking item(s) created for missing assets.");
        redirect('/search');
    }
}
