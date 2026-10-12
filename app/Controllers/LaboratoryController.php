<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Models\Laboratory;
use App\Services\QRService;
use App\Helpers\Database;

class LaboratoryController extends BaseController
{
    private QRService $qrService;

    public function __construct()
    {
        $this->qrService = new QRService();
    }

    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        if ($search) {
            $laboratories = Laboratory::search($search);
        } else {
            $laboratories = Laboratory::all();
        }
        view('laboratories.index', ['laboratories' => $laboratories, 'search' => $search]);
    }

    public function create(): void
    {
        view('laboratories.form', ['laboratory' => null]);
    }

    public function store(): void
    {
        $data = $_POST;
        $errors = $this->validate($data, [
            'name' => 'required',
            'room_number' => 'required',
        ]);

        if (!empty($errors)) {
            session_flash('error', 'Please check your input.');
            view('laboratories.form', ['laboratory' => null, 'errors' => $errors]);
            return;
        }

        $id = Laboratory::create($data);
        $qrPath = $this->qrService->generateLaboratoryQR($id, $data['name']);
        Laboratory::update($id, ['qr_code' => $qrPath]);

        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, 'laboratory_create', ?, ?, ?)");
        $stmt->execute([$_SESSION['user']['id'], "Laboratory '{$data['name']}' created", $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);

        session_flash('success', 'Laboratory created successfully.');
        redirect('/laboratories');
    }

    public function show(int $id): void
    {
        $laboratory = Laboratory::find($id);
        if (!$laboratory) {
            view('errors.404');
            return;
        }
        $db = Database::connect();
        $stmt = $db->prepare("SELECT COUNT(*) as c FROM equipment WHERE laboratory_id = ?");
        $stmt->execute([$id]);
        $equipment_count = $stmt->fetch()['c'];
        $stmt = $db->prepare("SELECT COUNT(*) as c FROM attendance WHERE laboratory_id = ?");
        $stmt->execute([$id]);
        $attendance_count = $stmt->fetch()['c'];
        view('laboratories.show', compact('laboratory', 'equipment_count', 'attendance_count'));
    }

    public function edit(int $id): void
    {
        $laboratory = Laboratory::find($id);
        if (!$laboratory) {
            view('errors.404');
            return;
        }
        view('laboratories.form', ['laboratory' => $laboratory]);
    }

    public function update(int $id): void
    {
        $data = $_POST;
        Laboratory::update($id, $data);
        session_flash('success', 'Laboratory updated successfully.');
        redirect('/laboratories');
    }

    public function destroy(int $id): void
    {
        if (!isAdmin()) {
            session_flash('error', 'Unauthorized. Only Administrators can archive or delete laboratories.');
            redirect('/laboratories');
            return;
        }
        Laboratory::update($id, ['status' => 'archived']);
        session_flash('success', 'Laboratory archived successfully.');
        redirect('/laboratories');
    }

    public function regenerateQR(int $id): void
    {
        $lab = Laboratory::find($id);
        if ($lab) {
            $qrPath = $this->qrService->generateLaboratoryQR($id, $lab['name']);
            Laboratory::update($id, ['qr_code' => $qrPath]);
            session_flash('success', 'QR code regenerated.');
        }
        redirect("/laboratories/{$id}");
    }

    public function viewQR(int $id): void
    {
        $lab = Laboratory::find($id);
        if (!$lab) {
            view('errors.404');
            return;
        }
        view('laboratories.qr', ['laboratory' => $lab]);
    }

    public function printAll(): void
    {
        $laboratories = Laboratory::where('status', 'active');
        view('laboratories.print-all', ['laboratories' => $laboratories]);
    }
}
