<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Helpers\Database;
use App\Models\AccountRequest;
use App\Models\User;
use App\Models\Role;

class AccountRequestController extends BaseController
{
    /**
     * Public submission of account request
     */
    public function requestForm(): void
    {
        view('auth.request-account');
    }

    public function submitRequest(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $requestedRoleId = intval($_POST['requested_role_id'] ?? 4);
        $deptSegment = trim($_POST['department_segment'] ?? '');
        $idNumber = trim($_POST['id_number'] ?? '');
        $reason = trim($_POST['reason'] ?? '');

        if (empty($name) || empty($email) || empty($deptSegment)) {
            session_flash('error', 'Please fill in all required fields.');
            redirect('/request-account');
            return;
        }

        $db = Database::connect();

        // Check if user already exists
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            session_flash('error', 'An account with this email address already exists. Please login instead.');
            redirect('/login');
            return;
        }

        // Generate Long NR Request Number
        $dateStr = date('Ymd');
        $uniq = strtoupper(substr(uniqid(), -4));
        $reqNumber = "NR-{$dateStr}-ACC-{$uniq}";

        AccountRequest::create([
            'request_number' => $reqNumber,
            'name' => $name,
            'email' => $email,
            'requested_role_id' => $requestedRoleId,
            'department_segment' => $deptSegment,
            'id_number' => $idNumber,
            'reason' => $reason,
            'status' => 'pending',
        ]);

        session_flash('success', "Your account request was submitted successfully with Reference #{$reqNumber}. Administrators will review your request shortly.");
        redirect('/login');
    }

    /**
     * Administrator list of account requests
     */
    public function index(): void
    {
        if (!isAdmin()) {
            http_response_code(403);
            view('errors.403');
            return;
        }

        $db = Database::connect();
        $status = $_GET['status'] ?? '';

        $sql = "
            SELECT ar.*, r.name as requested_role_name, u.name as reviewer_name
            FROM account_requests ar
            LEFT JOIN roles r ON ar.requested_role_id = r.id
            LEFT JOIN users u ON ar.reviewed_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if ($status) {
            $sql .= " AND ar.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY ar.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $requests = $stmt->fetchAll();

        $counts = [
            'all' => (int)$db->query("SELECT COUNT(*) as c FROM account_requests")->fetch()['c'],
            'pending' => (int)$db->query("SELECT COUNT(*) as c FROM account_requests WHERE status = 'pending'")->fetch()['c'],
            'approved' => (int)$db->query("SELECT COUNT(*) as c FROM account_requests WHERE status = 'approved'")->fetch()['c'],
            'rejected' => (int)$db->query("SELECT COUNT(*) as c FROM account_requests WHERE status = 'rejected'")->fetch()['c'],
        ];

        view('account-requests.index', compact('requests', 'status', 'counts'));
    }

    /**
     * Approve account request -> automatically creates user in users table
     */
    public function approve(int $id): void
    {
        if (!isAdmin()) {
            http_response_code(403);
            view('errors.403');
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM account_requests WHERE id = ?");
        $stmt->execute([$id]);
        $req = $stmt->fetch();

        if (!$req) {
            session_flash('error', 'Account request not found.');
            redirect('/account-requests');
            return;
        }

        // Check if user already exists
        $userCheck = $db->prepare("SELECT id FROM users WHERE email = ?");
        $userCheck->execute([$req['email']]);
        if (!$userCheck->fetch()) {
            $defaultPassword = password_hash('password123', PASSWORD_BCRYPT);
            $createUser = $db->prepare("INSERT INTO users (name, email, password, role_id, department_segment, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $createUser->execute([
                $req['name'],
                $req['email'],
                $defaultPassword,
                $req['requested_role_id'],
                $req['department_segment'],
            ]);
        }

        $reviewerId = $_SESSION['user']['id'] ?? 1;
        $updateReq = $db->prepare("UPDATE account_requests SET status = 'approved', reviewed_by = ?, admin_notes = 'Account approved and created (default password: password123)' WHERE id = ?");
        $updateReq->execute([$reviewerId, $id]);

        session_flash('success', "Account request for '{$req['name']}' approved! User account is now active.");
        redirect('/account-requests');
    }

    /**
     * Reject account request
     */
    public function reject(int $id): void
    {
        if (!isAdmin()) {
            http_response_code(403);
            view('errors.403');
            return;
        }

        $db = Database::connect();
        $reviewerId = $_SESSION['user']['id'] ?? 1;
        $stmt = $db->prepare("UPDATE account_requests SET status = 'rejected', reviewed_by = ?, admin_notes = 'Declined by administration' WHERE id = ?");
        $stmt->execute([$reviewerId, $id]);

        session_flash('success', 'Account request declined.');
        redirect('/account-requests');
    }
}
