<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Helpers\Database;
use App\Models\Borrowing;
use App\Models\Equipment;
use App\Helpers\Constants;

class BorrowingController extends BaseController
{
    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        $sql = "SELECT b.*, e.name as equipment_name, e.asset_tag, u.name as borrower_name, ap.name as approved_by_name
                FROM borrowings b
                JOIN equipment e ON b.equipment_id = e.id
                JOIN users u ON b.borrower_id = u.id
                LEFT JOIN users ap ON b.approved_by = ap.id";
        $params = [];
        if ($status) {
            $sql .= " WHERE b.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY b.created_at DESC";
        $borrowings = Borrowing::raw($sql, $params);
        view('borrowing.index', ['borrowings' => $borrowings, 'status' => $status]);
    }

    public function create(): void
    {
        $equipment = Equipment::where('status', 'active');
        $db = Database::connect();
        $users = $db->query("SELECT id, name FROM users WHERE status = 'active'")->fetchAll();
        view('borrowing.form', ['borrowing' => null, 'equipment' => $equipment, 'users' => $users]);
    }

    public function store(): void
    {
        $data = $_POST;
        Borrowing::create([
            'borrower_id' => $data['borrower_id'],
            'equipment_id' => $data['equipment_id'],
            'date_borrowed' => $data['date_borrowed'],
            'expected_return' => $data['expected_return'],
            'status' => 'pending',
            'notes' => $data['notes'] ?? '',
        ]);
        session_flash('success', 'Borrowing request submitted.');
        redirect('/borrowing');
    }

    public function approve(int $id): void
    {
        Borrowing::update($id, [
            'status' => 'approved',
            'approved_by' => $_SESSION['user']['id'],
        ]);
        Equipment::update($_POST['equipment_id'] ?? 0, ['status' => 'borrowed']);
        session_flash('success', 'Borrowing approved.');
        redirect('/borrowing');
    }

    public function return(int $id): void
    {
        Borrowing::update($id, [
            'status' => 'returned',
            'actual_return' => date('Y-m-d'),
        ]);
        Equipment::update($_POST['equipment_id'] ?? 0, ['status' => 'active']);
        session_flash('success', 'Equipment returned.');
        redirect('/borrowing');
    }

    public function reject(int $id): void
    {
        Borrowing::update($id, ['status' => 'rejected']);
        session_flash('success', 'Borrowing rejected.');
        redirect('/borrowing');
    }
}
