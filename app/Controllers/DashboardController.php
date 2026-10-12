<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Helpers\Database;
use App\Models\Laboratory;
use App\Models\Student;
use App\Models\Equipment;
use App\Models\User;
use App\Models\SegmentRequest;
use App\Models\AccountRequest;
use App\Services\AttendanceService;
use App\Services\InventoryService;
use App\Services\AuditService;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $db = Database::connect();
        $attendanceService = new AttendanceService();
        $inventoryService = new InventoryService();
        $auditService = new AuditService();

        // Dashboard Options (Filters & Views)
        $selectedLabId = !empty($_GET['laboratory_id']) ? (int)$_GET['laboratory_id'] : null;
        $selectedSemester = $_GET['semester'] ?? 'First Semester';
        $selectedPeriod = $_GET['period'] ?? 'today';

        // 1. Total users currently in laboratory (Active users whose time_out IS NULL)
        $activeUsersSql = "SELECT COUNT(*) as c FROM attendance WHERE DATE(attendance_date) = CURDATE() AND (time_out IS NULL OR time_out = '')";
        if ($selectedLabId) {
            $activeUsersSql .= " AND laboratory_id = {$selectedLabId}";
        }
        $totalActiveUsersInLab = (int)$db->query($activeUsersSql)->fetch()['c'];

        // Room-by-room Occupancy Breakdown
        $occupancyQuery = "
            SELECT l.id, l.name, l.room_number, l.capacity,
                   COUNT(CASE WHEN DATE(a.attendance_date) = CURDATE() AND (a.time_out IS NULL OR a.time_out = '') THEN 1 END) as active_users,
                   COUNT(CASE WHEN DATE(a.attendance_date) = CURDATE() THEN 1 END) as total_today_visits
            FROM laboratories l
            LEFT JOIN attendance a ON l.id = a.laboratory_id AND DATE(a.attendance_date) = CURDATE()
            WHERE l.status = 'active'
            GROUP BY l.id, l.name, l.room_number, l.capacity
            ORDER BY l.name ASC
        ";
        $laboratoryOccupancy = $db->query($occupancyQuery)->fetchAll();

        // 2. First Semester for OR Stats
        $orStatsQuery = "
            SELECT 
                COUNT(*) as total_students,
                COUNT(CASE WHEN semester = 'First Semester' THEN 1 END) as first_sem_total,
                COUNT(CASE WHEN semester = 'First Semester' AND or_number IS NOT NULL AND or_number != '' THEN 1 END) as first_sem_with_or,
                COUNT(CASE WHEN semester = 'First Semester' AND (or_number IS NULL OR or_number = '') THEN 1 END) as first_sem_no_or
            FROM students
        ";
        $orStats = $db->query($orStatsQuery)->fetch();

        // 3. Long NR for Dashboard (Requests, Non-Returned Items, Narrative Logs)
        // Segment requests with Long NR
        $longNrSegmentRequests = $db->query("
            SELECT sr.*, l.name as laboratory_name, l.room_number 
            FROM segment_requests sr 
            JOIN laboratories l ON sr.laboratory_id = l.id 
            ORDER BY sr.created_at DESC LIMIT 6
        ")->fetchAll();

        // Account requests with Long NR
        $longNrAccountRequests = $db->query("
            SELECT ar.*, r.name as requested_role_name 
            FROM account_requests ar 
            LEFT JOIN roles r ON ar.requested_role_id = r.id 
            ORDER BY ar.created_at DESC LIMIT 6
        ")->fetchAll();

        // Long NR Non-Returned / Overdue Items
        $longNrNonReturned = $db->query("
            SELECT b.*, e.name as equipment_name, e.asset_tag, e.property_code, e.serial_number, u.name as borrower_name
            FROM borrowings b
            JOIN equipment e ON b.equipment_id = e.id
            JOIN users u ON b.borrower_id = u.id
            WHERE b.status IN ('borrowed', 'overdue')
            ORDER BY b.date_borrowed DESC LIMIT 6
        ")->fetchAll();

        // Equipment and Lab stats (respecting laboratory filter if applied)
        $totalLaboratories = Laboratory::count();
        $totalStudents = Student::count();
        $inventoryStats = $inventoryService->getDashboardStats();

        // Recent Attendance today
        if ($selectedLabId) {
            $recentAttendance = $db->query("
                SELECT a.*, s.full_name as student_name, s.student_number, s.or_number, s.semester, l.name as laboratory_name 
                FROM attendance a 
                JOIN students s ON a.student_id = s.id 
                JOIN laboratories l ON a.laboratory_id = l.id 
                WHERE DATE(a.attendance_date) = CURDATE() AND a.laboratory_id = {$selectedLabId}
                ORDER BY a.time_in DESC LIMIT 15
            ")->fetchAll();
        } else {
            $recentAttendance = $db->query("
                SELECT a.*, s.full_name as student_name, s.student_number, s.or_number, s.semester, l.name as laboratory_name 
                FROM attendance a 
                JOIN students s ON a.student_id = s.id 
                JOIN laboratories l ON a.laboratory_id = l.id 
                WHERE DATE(a.attendance_date) = CURDATE() 
                ORDER BY a.time_in DESC LIMIT 15
            ")->fetchAll();
        }

        // Recent Audits
        $recentAudits = $auditService->getRecentAudits(5);
        $auditStats = $auditService->getDashboardStats();

        // Active Laboratories list for filter options
        $activeLaboratories = Laboratory::where('status', 'active');

        // Recent equipment services
        $recentServices = $db->query("
            SELECT es.*, e.name as equipment_name, e.asset_tag, e.property_code 
            FROM equipment_services es 
            JOIN equipment e ON es.equipment_id = e.id 
            ORDER BY es.service_date DESC LIMIT 5
        ")->fetchAll();

        // Newly Acquired Equipment (For HR Asset Watch and New Equipment Alerts)
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        $stmtNew = $db->prepare("
            SELECT e.*, ec.name as category_name, l.name as laboratory_name, l.room_number 
            FROM equipment e 
            JOIN equipment_categories ec ON e.category_id = ec.id 
            JOIN laboratories l ON e.laboratory_id = l.id 
            WHERE e.acquisition_date >= ? OR e.created_at >= ?
            ORDER BY e.acquisition_date DESC, e.created_at DESC 
            LIMIT 10
        ");
        $stmtNew->execute([$thirtyDaysAgo, $thirtyDaysAgo]);
        $newEquipmentList = $stmtNew->fetchAll();
        $newEquipmentCount = count($newEquipmentList);
        $roles = $db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();

        $data = [
            'total_laboratories' => $totalLaboratories,
            'total_students' => $totalStudents,
            'inventory' => $inventoryStats,
            'attendance_today' => count($recentAttendance),
            'total_active_users_in_lab' => $totalActiveUsersInLab,
            'laboratory_occupancy' => $laboratoryOccupancy,
            'laboratoryOccupancy' => $laboratoryOccupancy,
            'or_stats' => $orStats,
            'long_nr_segments' => $longNrSegmentRequests,
            'long_nr_accounts' => $longNrAccountRequests,
            'long_nr_non_returned' => $longNrNonReturned,
            'recent_services' => $recentServices,
            'roles' => $roles,
            'new_equipment_list' => $newEquipmentList,
            'new_equipment_count' => $newEquipmentCount,
            'attendance_weekly' => $attendanceService->getWeeklyAttendance(),
            'attendance_monthly' => $attendanceService->getMonthlyAttendance(),
            'attendance_trend' => $attendanceService->getAttendanceTrend(14),
            'inventory_status' => $inventoryService->getInventoryStatus(),
            'audit_stats' => $auditStats,
            'recent_audits' => $recentAudits,
            'active_laboratories' => $activeLaboratories,
            'recent_attendance' => $recentAttendance,
            'selected_lab_id' => $selectedLabId,
            'selected_semester' => $selectedSemester,
            'selected_period' => $selectedPeriod,
        ];

        view('dashboard.index', $data);
    }

    /**
     * Add account directly from dashboard (Administrator only)
     */
    public function addAccount(): void
    {
        if (!isAdmin()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Unauthorized: Only Administrators can create accounts.'], 403);
                return;
            }
            session_flash('error', 'Unauthorized: Only Administrators can create user accounts.');
            redirect('/dashboard');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 4); // Default to faculty
        $departmentSegment = trim($_POST['department_segment'] ?? '');
        $status = $_POST['status'] ?? 'active';

        if (empty($name) || empty($email) || empty($password)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Please fill in all required fields.'], 422);
                return;
            }
            session_flash('error', 'Name, email, and password are required.');
            redirect('/dashboard');
            return;
        }

        $db = Database::connect();
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'An account with this email already exists.'], 422);
                return;
            }
            session_flash('error', 'An account with this email already exists.');
            redirect('/dashboard');
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role_id, department_segment, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hash, $roleId, $departmentSegment, $status]);
        $newUserId = (int)$db->lastInsertId();

        // Log activity
        $currentUserId = $_SESSION['user']['id'] ?? null;
        $stmtLog = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, 'account_create', ?, ?, ?)");
        $stmtLog->execute([$currentUserId, "Created user account '{$name}' ({$email}) with role ID {$roleId}", $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);

        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => "Account for {$name} created successfully!"]);
            return;
        }

        session_flash('success', "Account for '{$name}' created successfully!");
        $redirectTo = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : '/dashboard';
        redirect($redirectTo);
    }

    public function stats(): void
    {
        $attendanceService = new AttendanceService();
        $inventoryService = new InventoryService();
        $db = Database::connect();

        $activeUsers = (int)$db->query("SELECT COUNT(*) as c FROM attendance WHERE DATE(attendance_date) = CURDATE() AND (time_out IS NULL OR time_out = '')")->fetch()['c'];

        $this->json([
            'laboratories' => Laboratory::count(),
            'students' => Student::count(),
            'inventory' => $inventoryService->getDashboardStats(),
            'attendance_today' => count($attendanceService->getTodayAttendance()),
            'total_users_in_lab' => $activeUsers,
            'attendance_weekly' => $attendanceService->getWeeklyAttendance(),
            'attendance_monthly' => $attendanceService->getMonthlyAttendance(),
        ]);
    }

    private function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}
