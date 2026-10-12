<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Models\Laboratory;
use App\Models\Equipment;
use App\Services\AuditService;

class AuditController extends BaseController
{
    private AuditService $auditService;

    public function __construct()
    {
        $this->auditService = new AuditService();
    }

    public function index(): void
    {
        $laboratoryId = $_GET['laboratory_id'] ?? null;
        $audits = $this->auditService->getAuditHistory($laboratoryId ? (int)$laboratoryId : null);
        $laboratories = Laboratory::where('status', 'active');
        view('audits.index', compact('audits', 'laboratories', 'laboratoryId'));
    }

    public function start(): void
    {
        $labId = (int)($_POST['laboratory_id'] ?? 0);
        $userId = $_SESSION['user']['id'];
        $result = $this->auditService->startAudit($labId, $userId);

        if ($result['success']) {
            session_flash('success', 'Audit started successfully.');
            redirect('/audits/' . $result['audit_id']);
        } else {
            session_flash('error', $result['message']);
            redirect('/audits');
        }
    }

    public function show(int $id): void
    {
        $audit = $this->auditService->getAuditResults($id);
        if (!$audit) {
            view('errors.404');
            return;
        }
        view('audits.show', ['audit' => $audit]);
    }

    public function scan(int $id): void
    {
        $equipmentId = (int)($_POST['equipment_id'] ?? 0);
        $result = $this->auditService->scanEquipment($id, $equipmentId);
        $this->json($result);
    }

    public function complete(int $id): void
    {
        $userId = $_SESSION['user']['id'];
        $result = $this->auditService->completeAudit($id, $userId);
        if ($result['success']) {
            session_flash('success', 'Audit completed successfully.');
        } else {
            session_flash('error', $result['message']);
        }
        redirect('/audits/' . $id);
    }
}
