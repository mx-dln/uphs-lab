<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Models\Student;
use App\Helpers\Constants;

class StudentController extends BaseController
{
    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        $course = $_GET['course'] ?? '';
        $yearLevel = $_GET['year_level'] ?? '';

        $conditions = [];
        $params = [];

        if ($search) {
            $students = Student::search($search);
        } else {
            $sql = "SELECT * FROM students WHERE 1=1";
            if ($course) {
                $sql .= " AND course = ?";
                $params[] = $course;
            }
            if ($yearLevel) {
                $sql .= " AND year_level = ?";
                $params[] = $yearLevel;
            }
            $sql .= " ORDER BY created_at DESC";
            $students = Student::raw($sql, $params);
        }

        $courses = Student::raw("SELECT DISTINCT course FROM students WHERE course IS NOT NULL AND course != '' ORDER BY course");
        $yearLevels = Constants::YEAR_LEVELS;

        view('students.index', compact('students', 'search', 'course', 'yearLevel', 'courses', 'yearLevels'));
    }

    public function create(): void
    {
        view('students.form', ['student' => null]);
    }

    public function store(): void
    {
        $data = $_POST;
        $errors = $this->validate($data, [
            'student_number' => 'required',
            'full_name' => 'required',
            'email' => 'email',
        ]);

        if (!empty($errors)) {
            session_flash('error', 'Please check your input.');
            view('students.form', ['student' => null, 'errors' => $errors]);
            return;
        }

        $data['password'] = password_hash($data['password'] ?? 'password123', PASSWORD_BCRYPT);
        Student::create($data);
        session_flash('success', 'Student created successfully.');
        redirect('/students');
    }

    public function show(int $id): void
    {
        $student = Student::find($id);
        if (!$student) {
            view('errors.404');
            return;
        }
        $attendance = \App\Models\Attendance::raw("SELECT a.*, l.name as laboratory_name FROM attendance a JOIN laboratories l ON a.laboratory_id = l.id WHERE a.student_id = ? ORDER BY a.time_in DESC LIMIT 20", [$id]);
        view('students.show', compact('student', 'attendance'));
    }

    public function edit(int $id): void
    {
        $student = Student::find($id);
        if (!$student) {
            view('errors.404');
            return;
        }
        view('students.form', ['student' => $student]);
    }

    public function update(int $id): void
    {
        $data = $_POST;
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }
        Student::update($id, $data);
        session_flash('success', 'Student updated successfully.');
        redirect('/students');
    }

    public function destroy(int $id): void
    {
        if (!isAdmin()) {
            session_flash('error', 'Unauthorized. Only Administrators can delete student records.');
            redirect('/students');
            return;
        }
        Student::delete($id);
        session_flash('success', 'Student deleted successfully.');
        redirect('/students');
    }

    public function importCsv(): void
    {
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            session_flash('error', 'Please upload a valid CSV file.');
            redirect('/students');
            return;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);

        $result = Student::importFromCsv($rows);
        session_flash('success', "Imported {$result['imported']} students.");
        if (!empty($result['errors'])) {
            $_SESSION['_flash']['import_errors'] = $result['errors'];
        }
        redirect('/students');
    }

    public function exportCsv(): void
    {
        $students = Student::all();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="students.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student Number', 'Full Name', 'Course', 'Year Level', 'Section', 'Email']);
        foreach ($students as $s) {
            fputcsv($output, [$s['student_number'], $s['full_name'], $s['course'], $s['year_level'], $s['section'], $s['email']]);
        }
        fclose($output);
        exit;
    }
}
