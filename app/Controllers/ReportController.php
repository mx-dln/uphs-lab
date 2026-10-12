<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Models\Laboratory;
use App\Services\ReportingService;

class ReportController extends BaseController
{
    private ReportingService $reportingService;

    public function __construct()
    {
        $this->reportingService = new ReportingService();
    }

    public function index(): void
    {
        view('reports.index');
    }

    public function attendance(): void
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $labId = $_GET['laboratory_id'] ?? null;
        $data = $this->reportingService->attendanceReport($startDate, $endDate, $labId ? (int)$labId : null);
        $laboratories = Laboratory::where('status', 'active');
        view('reports.attendance', compact('data', 'startDate', 'endDate', 'labId', 'laboratories'));
    }

    public function equipment(): void
    {
        $labId = $_GET['laboratory_id'] ?? null;
        $type = $_GET['type'] ?? 'all';
        $laboratories = Laboratory::where('status', 'active');

        if ($type === 'missing') {
            $data = $this->reportingService->missingEquipmentReport();
        } elseif ($type === 'defective') {
            $data = $this->reportingService->defectiveEquipmentReport();
        } else {
            $data = $this->reportingService->equipmentInventoryReport($labId ? (int)$labId : null);
        }

        view('reports.equipment', compact('data', 'type', 'labId', 'laboratories'));
    }

    public function audit(): void
    {
        $auditId = $_GET['audit_id'] ?? null;
        $data = $this->reportingService->auditReport($auditId ? (int)$auditId : null);
        view('reports.audit', ['data' => $data]);
    }

    public function borrowing(): void
    {
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $data = $this->reportingService->borrowingReport($startDate, $endDate);
        view('reports.borrowing', compact('data', 'startDate', 'endDate'));
    }

    public function export(string $type, string $format): void
    {
        session_flash('info', 'Export feature requires PhpSpreadsheet/Dompdf. Please run: composer require phpoffice/phpspreadsheet dompdf/dompdf');
        redirect('/reports');
    }
}
