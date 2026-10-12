<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Helpers\Database;
use App\Models\SegmentRequest;
use App\Models\Laboratory;

class SegmentRequestController extends BaseController
{
    public function index(): void
    {
        $db = Database::connect();
        $status = $_GET['status'] ?? '';
        $labId = $_GET['laboratory_id'] ?? '';

        $sql = "
            SELECT sr.*, l.name as laboratory_name, l.room_number, u.name as reviewer_name
            FROM segment_requests sr
            JOIN laboratories l ON sr.laboratory_id = l.id
            LEFT JOIN users u ON sr.reviewed_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if ($status) {
            $sql .= " AND sr.status = ?";
            $params[] = $status;
        }
        if ($labId) {
            $sql .= " AND sr.laboratory_id = ?";
            $params[] = (int)$labId;
        }

        $sql .= " ORDER BY sr.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $requests = $stmt->fetchAll();

        $laboratories = Laboratory::where('status', 'active');
        view('segment-requests.index', compact('requests', 'laboratories', 'status', 'labId'));
    }

    public function create(): void
    {
        $laboratories = Laboratory::where('status', 'active');
        view('segment-requests.form', compact('laboratories'));
    }

    public function store(): void
    {
        $data = $_POST;
        $errors = $this->validate($data, [
            'requester_name' => 'required',
            'requester_email' => 'required',
            'laboratory_id' => 'required',
            'segment_name' => 'required',
            'purpose' => 'required',
            'request_date' => 'required',
        ]);

        if (!empty($errors)) {
            session_flash('error', 'Please fill in all required request fields.');
            $laboratories = Laboratory::where('status', 'active');
            view('segment-requests.form', compact('laboratories', 'errors'));
            return;
        }

        // Generate Long NR Request Number format: NR-YYYYMMDD-XXXXX-SEG
        $dateStr = date('Ymd');
        $uniq = strtoupper(substr(uniqid(), -4));
        $requestNumber = "NR-{$dateStr}-{$uniq}-SEG";

        SegmentRequest::create([
            'request_number' => $requestNumber,
            'requester_name' => trim($data['requester_name']),
            'requester_email' => trim($data['requester_email']),
            'laboratory_id' => (int)$data['laboratory_id'],
            'segment_name' => trim($data['segment_name']),
            'purpose' => trim($data['purpose']),
            'request_date' => $data['request_date'],
            'start_time' => $data['start_time'] ?? '',
            'end_time' => $data['end_time'] ?? '',
            'expected_users' => intval($data['expected_users'] ?? 1),
            'equipment_needed' => $data['equipment_needed'] ?? '',
            'status' => 'pending',
            'admin_notes' => '',
        ]);

        session_flash('success', "Segment Request submitted successfully with Long NR Reference: {$requestNumber}.");
        redirect('/segment-requests');
    }

    public function approve(int $id): void
    {
        $req = SegmentRequest::find($id);
        if ($req) {
            $reviewerId = $_SESSION['user']['id'] ?? 1;
            SegmentRequest::update($id, [
                'status' => 'approved',
                'reviewed_by' => $reviewerId,
                'admin_notes' => 'Approved for scheduled lab session by administration.',
            ]);
            session_flash('success', "Segment Request #{$req['request_number']} approved.");
        }
        redirect('/segment-requests');
    }

    public function reject(int $id): void
    {
        $req = SegmentRequest::find($id);
        if ($req) {
            $reviewerId = $_SESSION['user']['id'] ?? 1;
            SegmentRequest::update($id, [
                'status' => 'rejected',
                'reviewed_by' => $reviewerId,
                'admin_notes' => $_POST['notes'] ?? 'Segment request declined due to schedule conflict or maintenance.',
            ]);
            session_flash('success', "Segment Request #{$req['request_number']} declined.");
        }
        redirect('/segment-requests');
    }
}
