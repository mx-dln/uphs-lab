<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Helpers\Database;
use App\Models\Laboratory;
use App\Models\Student;
use App\Services\AttendanceService;

class AttendanceController extends BaseController
{
    private AttendanceService $attendanceService;

    public function __construct()
    {
        $this->attendanceService = new AttendanceService();
    }

    public function index(): void
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        $laboratoryId = $_GET['laboratory_id'] ?? '';

        if ($laboratoryId) {
            $attendance = $this->attendanceService->getLaboratoryAttendance((int)$laboratoryId);
        } else {
            $attendance = $this->attendanceService->getAttendanceByDate($date);
        }

        $laboratories = Laboratory::where('status', 'active');
        view('attendance.index', compact('attendance', 'date', 'laboratoryId', 'laboratories'));
    }

    public function today(): void
    {
        $attendance = $this->attendanceService->getTodayAttendance();
        view('attendance.today', ['attendance' => $attendance]);
    }

    public function byStudent(int $id): void
    {
        $attendance = $this->attendanceService->getStudentAttendance($id);
        $student = Student::find($id);
        view('attendance.student', compact('attendance', 'student'));
    }

    public function byLaboratory(int $id): void
    {
        $attendance = $this->attendanceService->getLaboratoryAttendance($id);
        $laboratory = Laboratory::find($id);
        view('attendance.laboratory', compact('attendance', 'laboratory'));
    }

    public function scanLaboratoryQR(int $id): void
    {
        $laboratory = Laboratory::find($id);
        if (!$laboratory || $laboratory['status'] !== 'active') {
            view('errors.404');
            return;
        }

        $student = $_SESSION['student'] ?? null;
        view('attendance.scan', ['laboratory' => $laboratory, 'student' => $student]);
    }

    public function scanAndRedirect(int $laboratoryId): void
    {
        $studentNumber = $_POST['student_number'] ?? ($_SESSION['student']['student_number'] ?? '');
        $actionMode = $_POST['action_mode'] ?? 'auto';

        $student = Student::findByStudentNumber($studentNumber);
        if (!$student) {
            session_flash('error', 'Student not found.');
            redirect(isset($_SESSION['student']) ? '/student/dashboard' : '/scan/laboratory/' . $laboratoryId);
            return;
        }

        $db = \App\Helpers\Database::connect();
        
        // Find if student currently has an OPEN session today in this lab
        $stmt = $db->prepare("SELECT * FROM attendance WHERE student_id = ? AND laboratory_id = ? AND DATE(attendance_date) = CURDATE() AND (time_out IS NULL OR time_out = '') ORDER BY id DESC LIMIT 1");
        $stmt->execute([$student['id'], $laboratoryId]);
        $openSession = $stmt->fetch();

        if ($actionMode === 'out') {
            if ($openSession) {
                $stmt = $db->prepare("UPDATE attendance SET time_out = NOW() WHERE id = ?");
                $stmt->execute([$openSession['id']]);
                session_flash('success', 'Timed out at ' . now('h:i A') . '! See you next time, ' . $student['full_name'] . '.');
            } else {
                session_flash('error', 'No active Time-In session found today to time out.');
            }
        } elseif ($actionMode === 'in') {
            if ($openSession) {
                session_flash('info', 'Already Timed In at ' . date('h:i A', strtotime($openSession['time_in'])) . '. Current session is active.');
            } else {
                $result = $this->attendanceService->recordAttendance($student['id'], $laboratoryId);
                if ($result['success']) {
                    session_flash('success', 'Timed in at ' . now('h:i A') . '! Welcome, ' . $student['full_name'] . '.');
                } else {
                    session_flash('error', $result['message']);
                }
            }
        } else {
            // Auto shuffle: If open session exists -> Time Out; If none -> Time In
            if ($openSession) {
                $stmt = $db->prepare("UPDATE attendance SET time_out = NOW() WHERE id = ?");
                $stmt->execute([$openSession['id']]);
                session_flash('success', 'Auto-Shuffle: Timed out at ' . now('h:i A') . '! See you next time, ' . $student['full_name'] . '.');
            } else {
                $result = $this->attendanceService->recordAttendance($student['id'], $laboratoryId);
                if ($result['success']) {
                    session_flash('success', 'Auto-Shuffle: Timed in at ' . now('h:i A') . '! Welcome, ' . $student['full_name'] . '.');
                } else {
                    session_flash('error', $result['message']);
                }
            }
        }

        redirect(isset($_SESSION['student']) ? '/student/dashboard' : '/scan/laboratory/' . $laboratoryId);
    }

    public function studentLoginForm(): void
    {
        $labId = $_GET['lab'] ?? null;
        view('attendance.student-login', ['labId' => $labId]);
    }

    public function studentLogin(): void
    {
        $studentNumber = $_POST['student_number'] ?? '';
        $password = $_POST['password'] ?? '';
        $labId = $_POST['lab_id'] ?? null;

        $student = Student::findByStudentNumber($studentNumber);
        if (!$student || !password_verify($password, $student['password'])) {
            session_flash('error', 'Invalid student number or password.');
            $redirect = $labId ? '/student/login?lab=' . $labId : '/student/login';
            view('attendance.student-login', ['labId' => $labId]);
            return;
        }

        $_SESSION['student'] = $student;
        session_flash('success', 'Welcome, ' . $student['full_name'] . '!');

        if ($labId) {
            redirect('/scan/laboratory/' . $labId);
        }
        redirect('/student/dashboard');
    }

    public function studentLogout(): void
    {
        unset($_SESSION['student']);
        session_flash('success', 'Logged out.');
        redirect('/student/login');
    }

    public function studentDashboard(): void
    {
        $student = $_SESSION['student'] ?? null;
        if (!$student) {
            redirect('/student/login');
            return;
        }
        $attendance = $this->attendanceService->getStudentAttendance($student['id']);
        view('attendance.student-dashboard', ['student' => $student, 'attendance' => $attendance]);
    }

    public function studentScan(): void
    {
        $student = $_SESSION['student'] ?? null;
        if (!$student) { redirect('/student/login'); return; }
        view('attendance.student-scan', ['student' => $student]);
    }

    public function studentHistory(): void
    {
        $student = $_SESSION['student'] ?? null;
        if (!$student) { redirect('/student/login'); return; }
        $attendance = $this->attendanceService->getStudentAttendance($student['id']);
        view('attendance.student-history', ['student' => $student, 'attendance' => $attendance]);
    }

    public function studentProfile(): void
    {
        $student = $_SESSION['student'] ?? null;
        if (!$student) { redirect('/student/login'); return; }
        $attendance = $this->attendanceService->getStudentAttendance($student['id']);
        view('attendance.student-profile', ['student' => $student, 'attendance' => $attendance]);
    }

    public function scanAttendance(int $laboratoryId): void
    {
        $db = \App\Helpers\Database::connect();
        $actionMode = $_POST['action_mode'] ?? 'auto';

        // Get student (from session or by number)
        if (isset($_SESSION['student'])) {
            $student = $_SESSION['student'];
        } else {
            $studentNumber = $_POST['student_number'] ?? '';
            $student = Student::findByStudentNumber($studentNumber);
            if (!$student) {
                $this->json(['success' => false, 'message' => 'Student not found.'], 404);
                return;
            }
        }

        // Find active open session
        $stmt = $db->prepare("SELECT * FROM attendance WHERE student_id = ? AND laboratory_id = ? AND DATE(attendance_date) = CURDATE() AND (time_out IS NULL OR time_out = '') ORDER BY id DESC LIMIT 1");
        $stmt->execute([$student['id'], $laboratoryId]);
        $openSession = $stmt->fetch();

        if ($actionMode === 'out') {
            if ($openSession) {
                $stmt = $db->prepare("UPDATE attendance SET time_out = NOW() WHERE id = ?");
                $stmt->execute([$openSession['id']]);
                session_flash('success', 'Timed out at ' . now('h:i A') . '! See you next time, ' . $student['full_name'] . '.');
                $this->json(['success' => true, 'action' => 'logout', 'message' => 'Timed out at ' . now('h:i A') . '!']);
            } else {
                $this->json(['success' => false, 'message' => 'No active Time-In session found today.']);
            }
            return;
        }

        if ($actionMode === 'in') {
            if ($openSession) {
                $this->json(['success' => true, 'action' => 'active', 'message' => 'Already timed in at ' . date('h:i A', strtotime($openSession['time_in']))]);
                return;
            }
            $result = $this->attendanceService->recordAttendance($student['id'], $laboratoryId);
            if ($result['success']) {
                session_flash('success', 'Timed in at ' . now('h:i A') . '! Welcome, ' . $student['full_name'] . '.');
                $this->json(['success' => true, 'action' => 'login', 'message' => 'Timed in at ' . now('h:i A') . '!', 'attendance' => $result['attendance']]);
            } else {
                $this->json(['success' => false, 'message' => $result['message']]);
            }
            return;
        }

        // Auto shuffle
        if ($openSession) {
            $stmt = $db->prepare("UPDATE attendance SET time_out = NOW() WHERE id = ?");
            $stmt->execute([$openSession['id']]);
            session_flash('success', 'Timed out at ' . now('h:i A') . '! See you next time, ' . $student['full_name'] . '.');
            $this->json(['success' => true, 'action' => 'logout', 'message' => 'Timed out at ' . now('h:i A') . '!']);
            return;
        }

        $result = $this->attendanceService->recordAttendance($student['id'], $laboratoryId);
        if ($result['success']) {
            session_flash('success', 'Timed in at ' . now('h:i A') . '! Welcome, ' . $student['full_name'] . '.');
            $this->json(['success' => true, 'action' => 'login', 'message' => 'Timed in at ' . now('h:i A') . '!', 'attendance' => $result['attendance']]);
        } else {
            $this->json(['success' => false, 'message' => $result['message']]);
        }
    }

    public function trend(): void
    {
        $days = (int)($_GET['days'] ?? 30);
        $this->json($this->attendanceService->getAttendanceTrend($days));
    }
}
