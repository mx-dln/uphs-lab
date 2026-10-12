<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Laboratory;
use App\Models\EquipmentService;
use App\Models\EquipmentDispatch;
use App\Services\QRService;
use App\Helpers\Database;
use App\Helpers\Constants;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EquipmentController extends BaseController
{
    private QRService $qrService;

    public function __construct()
    {
        $this->qrService = new QRService();
    }

    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        $labId = $_GET['laboratory_id'] ?? '';
        $classification = $_GET['classification'] ?? '';
        $status = $_GET['status'] ?? '';
        $filter = $_GET['filter'] ?? '';

        $db = Database::connect();
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        $stmtNew = $db->prepare("SELECT COUNT(*) as c FROM equipment WHERE acquisition_date >= ? OR created_at >= ?");
        $stmtNew->execute([$thirtyDaysAgo, $thirtyDaysAgo]);
        $newEquipmentCount = (int)$stmtNew->fetch()['c'];

        $sql = "SELECT e.*, ec.name as category_name, l.name as laboratory_name 
                FROM equipment e 
                JOIN equipment_categories ec ON e.category_id = ec.id 
                JOIN laboratories l ON e.laboratory_id = l.id 
                WHERE 1=1";
        $params = [];

        if ($filter === 'new') {
            $sql .= " AND (e.acquisition_date >= ? OR e.created_at >= ?)";
            $params[] = $thirtyDaysAgo;
            $params[] = $thirtyDaysAgo;
        }

        if ($search) {
            $sql .= " AND (e.name LIKE ? OR e.asset_tag LIKE ? OR e.property_code LIKE ? OR e.serial_number LIKE ? OR e.brand LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($labId) {
            $sql .= " AND e.laboratory_id = ?";
            $params[] = (int)$labId;
        }
        if ($classification) {
            $sql .= " AND e.classification = ?";
            $params[] = $classification;
        }
        if ($status) {
            $sql .= " AND e.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY e.created_at DESC";
        $equipment = Equipment::raw($sql, $params);
        $laboratories = Laboratory::where('status', 'active');
        $categories = EquipmentCategory::all();
        $classifications = Constants::EQUIPMENT_CLASSIFICATIONS;

        view('equipment.index', compact('equipment', 'search', 'labId', 'classification', 'status', 'laboratories', 'categories', 'classifications', 'filter', 'newEquipmentCount', 'thirtyDaysAgo'));
    }

    public function create(): void
    {
        $laboratories = Laboratory::where('status', 'active');
        $categories = EquipmentCategory::all();
        $classifications = Constants::EQUIPMENT_CLASSIFICATIONS;
        view('equipment.form', [
            'equipment' => null, 
            'laboratories' => $laboratories, 
            'categories' => $categories, 
            'classifications' => $classifications
        ]);
    }

    public function store(): void
    {
        $data = $_POST;
        $errors = $this->validate($data, [
            'asset_tag' => 'required',
            'name' => 'required',
            'category_id' => 'required',
            'laboratory_id' => 'required',
        ]);

        if (!empty($errors)) {
            session_flash('error', 'Please fill in all required fields.');
            $laboratories = Laboratory::where('status', 'active');
            $categories = EquipmentCategory::all();
            $classifications = Constants::EQUIPMENT_CLASSIFICATIONS;
            view('equipment.form', [
                'equipment' => null, 
                'laboratories' => $laboratories, 
                'categories' => $categories, 
                'classifications' => $classifications, 
                'errors' => $errors
            ]);
            return;
        }

        // Auto-generate Property Code if blank
        if (empty($data['property_code'])) {
            $year = date('Y');
            $data['property_code'] = 'PROP-' . $year . '-LAB' . $data['laboratory_id'] . '-' . strtoupper(substr(uniqid(), -4));
        }

        if (empty($data['classification'])) {
            $data['classification'] = 'ICT Equipment';
        }

        $id = Equipment::create($data);
        $qrPath = $this->qrService->generateEquipmentQR($id, $data['asset_tag']);
        Equipment::update($id, ['qr_code' => $qrPath]);

        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, 'equipment_create', ?, ?, ?)");
        $stmt->execute([$_SESSION['user']['id'] ?? 1, "Equipment '{$data['name']}' ({$data['property_code']}) created", $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);

        session_flash('success', "Equipment '{$data['name']}' added successfully with Property Code: {$data['property_code']}.");
        redirect('/equipment');
    }

    public function show(int $id): void
    {
        $equipment = Equipment::rawOne(
            "SELECT e.*, ec.name as category_name, l.name as laboratory_name, l.room_number 
             FROM equipment e 
             JOIN equipment_categories ec ON e.category_id = ec.id 
             JOIN laboratories l ON e.laboratory_id = l.id 
             WHERE e.id = ?", [$id]
        );

        if (!$equipment) {
            view('errors.404');
            return;
        }

        $db = Database::connect();
        // Fetch services history for this equipment
        $stmt = $db->prepare("SELECT * FROM equipment_services WHERE equipment_id = ? ORDER BY service_date DESC");
        $stmt->execute([$id]);
        $services = $stmt->fetchAll();

        // Fetch dispatch history for this equipment
        $stmtDisp = $db->prepare("
            SELECT ed.*, fl.name as from_lab_name, tl.name as to_lab_name, u.name as user_name 
            FROM equipment_dispatches ed 
            LEFT JOIN laboratories fl ON ed.from_lab_id = fl.id 
            LEFT JOIN laboratories tl ON ed.to_lab_id = tl.id 
            LEFT JOIN users u ON ed.dispatched_to_user_id = u.id 
            WHERE ed.equipment_id = ? ORDER BY ed.dispatch_date DESC
        ");
        $stmtDisp->execute([$id]);
        $dispatches = $stmtDisp->fetchAll();

        $activeLaboratories = Laboratory::where('status', 'active');

        view('equipment.show', compact('equipment', 'services', 'dispatches', 'activeLaboratories'));
    }

    public function edit(int $id): void
    {
        $equipment = Equipment::find($id);
        if (!$equipment) {
            view('errors.404');
            return;
        }
        $laboratories = Laboratory::where('status', 'active');
        $categories = EquipmentCategory::all();
        $classifications = Constants::EQUIPMENT_CLASSIFICATIONS;
        view('equipment.form', compact('equipment', 'laboratories', 'categories', 'classifications'));
    }

    public function update(int $id): void
    {
        $data = $_POST;
        if (empty($data['classification'])) {
            $data['classification'] = 'ICT Equipment';
        }
        Equipment::update($id, $data);
        session_flash('success', 'Equipment updated successfully.');
        redirect("/equipment/{$id}");
    }

    public function destroy(int $id): void
    {
        if (!isAdmin()) {
            session_flash('error', 'Unauthorized. Only Administrators can delete equipment assets.');
            redirect('/equipment');
            return;
        }
        Equipment::delete($id);
        session_flash('success', 'Equipment deleted successfully.');
        redirect('/equipment');
    }

    /**
     * Dispatch equipment to another laboratory, department, or user
     */
    public function dispatch(int $id): void
    {
        $equipment = Equipment::find($id);
        if (!$equipment) {
            view('errors.404');
            return;
        }

        $toLabId = !empty($_POST['to_lab_id']) ? (int)$_POST['to_lab_id'] : null;
        $segment = trim($_POST['segment'] ?? '');
        $purpose = trim($_POST['purpose'] ?? '');
        $returnDueDate = !empty($_POST['return_due_date']) ? $_POST['return_due_date'] : null;
        $dispatchedToUserId = !empty($_POST['dispatched_to_user_id']) ? (int)$_POST['dispatched_to_user_id'] : null;
        $currentUserId = $_SESSION['user']['id'] ?? 1;

        $db = Database::connect();
        $stmt = $db->prepare("
            INSERT INTO equipment_dispatches 
            (equipment_id, from_lab_id, to_lab_id, dispatched_to_user_id, segment, dispatch_date, return_due_date, purpose, status, dispatched_by) 
            VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?, 'dispatched', ?)
        ");
        $stmt->execute([
            $id, 
            $equipment['laboratory_id'], 
            $toLabId, 
            $dispatchedToUserId, 
            $segment, 
            $returnDueDate, 
            $purpose, 
            $currentUserId
        ]);

        // Update equipment status
        Equipment::update($id, [
            'status' => 'borrowed',
            'notes' => ($equipment['notes'] ? $equipment['notes'] . " | " : "") . "Dispatched to segment/lab on " . date('Y-m-d') . ": {$purpose}"
        ]);

        session_flash('success', "Equipment '{$equipment['name']}' dispatched successfully.");
        redirect("/equipment/{$id}");
    }

    /**
     * Return dispatched equipment
     */
    public function returnDispatch(int $dispatchId): void
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM equipment_dispatches WHERE id = ?");
        $stmt->execute([$dispatchId]);
        $dispatch = $stmt->fetch();

        if ($dispatch) {
            $update = $db->prepare("UPDATE equipment_dispatches SET status = 'returned', actual_return_date = CURDATE() WHERE id = ?");
            $update->execute([$dispatchId]);

            // Restore equipment status
            Equipment::update($dispatch['equipment_id'], [
                'status' => 'active'
            ]);

            session_flash('success', 'Dispatched equipment returned to active inventory.');
            redirect("/equipment/{$dispatch['equipment_id']}");
            return;
        }

        session_flash('error', 'Dispatch record not found.');
        redirect('/equipment');
    }

    /**
     * Choice File Import: Imports CSV or Excel files with user file picker
     */
    public function importChoiceFile(): void
    {
        if (empty($_FILES['choice_file']['tmp_name'])) {
            session_flash('error', 'Please choose a valid CSV or Excel file to import.');
            redirect('/equipment');
            return;
        }

        $file = $_FILES['choice_file'];
        $fileName = $file['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['csv', 'xlsx', 'xls'])) {
            session_flash('error', 'Invalid file type. Please select a .csv, .xlsx, or .xls file.');
            redirect('/equipment');
            return;
        }

        $imported = 0;
        $errors = [];

        try {
            $rows = [];
            if ($ext === 'csv') {
                $handle = fopen($file['tmp_name'], 'r');
                while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            } else {
                $spreadsheet = IOFactory::load($file['tmp_name']);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
            }

            // Remove header row if present
            if (!empty($rows) && (strtolower(trim($rows[0][0] ?? '')) === 'asset tag' || strtolower(trim($rows[0][0] ?? '')) === 'tag')) {
                array_shift($rows);
            }

            $categories = EquipmentCategory::all();
            $catMap = [];
            foreach ($categories as $c) {
                $catMap[strtolower($c['name'])] = $c['id'];
            }

            $labs = Laboratory::all();
            $labMap = [];
            foreach ($labs as $l) {
                $labMap[strtolower($l['name'])] = $l['id'];
                $labMap[strtolower($l['room_number'])] = $l['id'];
            }
            $defaultLabId = !empty($labs) ? $labs[0]['id'] : 1;
            $defaultCatId = !empty($categories) ? $categories[0]['id'] : 1;

            foreach ($rows as $idx => $row) {
                if (empty($row[0])) continue; // Empty line

                $assetTag = trim($row[0] ?? '');
                $propertyCode = trim($row[1] ?? '');
                $name = trim($row[2] ?? '');
                $catInput = strtolower(trim($row[3] ?? ''));
                $classification = trim($row[4] ?? 'ICT Equipment');
                $labInput = strtolower(trim($row[5] ?? ''));
                $brand = trim($row[6] ?? '');
                $model = trim($row[7] ?? '');
                $serial = trim($row[8] ?? '');
                $cost = floatval(str_replace([',', '$', '₱', 'PHP', ' '], '', $row[9] ?? '0'));
                $status = strtolower(trim($row[10] ?? 'active'));

                if (empty($assetTag) || empty($name)) {
                    $errors[] = "Row " . ($idx + 2) . ": Asset tag or name is missing.";
                    continue;
                }

                // Check duplicate
                $existing = Equipment::whereFirst('asset_tag', $assetTag);
                if ($existing) {
                    $errors[] = "Row " . ($idx + 2) . ": Asset tag {$assetTag} already exists.";
                    continue;
                }

                $catId = $catMap[$catInput] ?? $defaultCatId;
                $labId = $labMap[$labInput] ?? $defaultLabId;

                if (empty($propertyCode)) {
                    $propertyCode = 'PROP-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));
                }

                if (!in_array($status, Constants::EQUIPMENT_STATUS)) {
                    $status = 'active';
                }

                $eqData = [
                    'asset_tag' => $assetTag,
                    'property_code' => $propertyCode,
                    'name' => $name,
                    'category_id' => $catId,
                    'classification' => $classification,
                    'laboratory_id' => $labId,
                    'brand' => $brand,
                    'model' => $model,
                    'serial_number' => $serial,
                    'purchase_cost' => $cost,
                    'status' => $status,
                    'acquisition_date' => date('Y-m-d'),
                ];

                $newId = Equipment::create($eqData);
                $qrPath = $this->qrService->generateEquipmentQR($newId, $assetTag);
                Equipment::update($newId, ['qr_code' => $qrPath]);
                $imported++;
            }

            $msg = "Imported {$imported} equipment item(s) successfully.";
            if (!empty($errors)) {
                $msg .= " Some rows were skipped: " . implode(' ', array_slice($errors, 0, 3));
            }
            session_flash($imported > 0 ? 'success' : 'error', $msg);

        } catch (\Exception $e) {
            session_flash('error', "Import error: " . $e->getMessage());
        }

        redirect('/equipment');
    }

    /**
     * Property Code Print & Export to PDF
     */
    public function exportPdf(): void
    {
        $labId = $_GET['laboratory_id'] ?? null;
        $classification = $_GET['classification'] ?? null;
        $status = $_GET['status'] ?? null;

        $sql = "SELECT e.*, ec.name as category_name, l.name as laboratory_name, l.room_number 
                FROM equipment e 
                JOIN equipment_categories ec ON e.category_id = ec.id 
                JOIN laboratories l ON e.laboratory_id = l.id 
                WHERE 1=1";
        $params = [];

        if ($labId) {
            $sql .= " AND e.laboratory_id = ?";
            $params[] = (int)$labId;
        }
        if ($classification) {
            $sql .= " AND e.classification = ?";
            $params[] = $classification;
        }
        if ($status) {
            $sql .= " AND e.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY l.name, e.classification, e.name";
        $equipment = Equipment::raw($sql, $params);

        $totalCost = array_sum(array_column($equipment, 'purchase_cost'));

        // Build HTML for PDF report
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>UPHS Laboratory Equipment & Property Code Inventory</title>
            <style>
                body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #222; margin: 15px; }
                .header { text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 12px; margin-bottom: 15px; }
                .header h1 { font-size: 16px; margin: 0; color: #1e3a8a; text-transform: uppercase; font-weight: bold; }
                .header h2 { font-size: 12px; margin: 3px 0; color: #444; font-weight: normal; }
                .header p { font-size: 9px; margin: 2px 0; color: #666; }
                .meta-table { width: 100%; margin-bottom: 12px; font-size: 9px; }
                .meta-table td { padding: 2px; }
                table.data { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9px; }
                table.data th { background-color: #1e3a8a; color: #ffffff; text-align: left; padding: 6px 4px; border: 1px solid #1e3a8a; }
                table.data td { padding: 5px 4px; border: 1px solid #ddd; }
                table.data tr:nth-child(even) { background-color: #f8fafc; }
                .tag { font-family: Courier, monospace; font-weight: bold; }
                .footer { margin-top: 30px; }
                .sign-table { width: 100%; margin-top: 40px; font-size: 9px; }
                .sign-table td { width: 33%; text-align: center; padding-top: 40px; border-top: 1px solid #999; }
                .badge { padding: 2px 4px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
                .badge-active { background: #dcfce7; color: #15803d; }
                .badge-missing { background: #fee2e2; color: #b91c1c; }
                .badge-repair { background: #fef3c7; color: #b45309; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>University of Perpetual Help System</h1>
                <h2>Cauayan City Campus &middot; Computer Laboratory Management System</h2>
                <p><strong>OFFICIAL PROPERTY & EQUIPMENT INVENTORY REPORT</strong></p>
                <p>Generated on ' . date('F d, Y \a\t h:i A') . ' &middot; Mode: Standalone Offline Register</p>
            </div>

            <table class="meta-table">
                <tr>
                    <td><strong>Total Items:</strong> ' . count($equipment) . ' units</td>
                    <td><strong>Total Valued Cost:</strong> PHP ' . number_format($totalCost, 2) . '</td>
                    <td><strong>Academic Semester:</strong> First Semester 2026-2027</td>
                </tr>
            </table>

            <table class="data">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="14%">Property Code</th>
                        <th width="12%">Asset Tag</th>
                        <th width="20%">Equipment Name</th>
                        <th width="14%">Classification</th>
                        <th width="13%">Laboratory / Room</th>
                        <th width="12%">Serial Number</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($equipment as $i => $item) {
            $badgeClass = ($item['status'] === 'active') ? 'badge-active' : (($item['status'] === 'missing') ? 'badge-missing' : 'badge-repair');
            $html .= '<tr>
                <td>' . ($i + 1) . '</td>
                <td class="tag" style="color: #1e40af;">' . htmlspecialchars($item['property_code'] ?? 'N/A') . '</td>
                <td class="tag">' . htmlspecialchars($item['asset_tag']) . '</td>
                <td><strong>' . htmlspecialchars($item['name']) . '</strong><br><span style="color:#666;font-size:8px;">' . htmlspecialchars($item['brand'] . ' ' . $item['model']) . '</span></td>
                <td>' . htmlspecialchars($item['classification'] ?? 'ICT Equipment') . '</td>
                <td>' . htmlspecialchars($item['laboratory_name']) . ' (' . htmlspecialchars($item['room_number']) . ')</td>
                <td class="tag">' . htmlspecialchars($item['serial_number'] ?? 'N/A') . '</td>
                <td><span class="badge ' . $badgeClass . '">' . ucfirst(str_replace('_', ' ', $item['status'])) . '</span></td>
            </tr>';
        }

        $html .= '</tbody>
            </table>

            <div class="footer">
                <table class="sign-table">
                    <tr>
                        <td>
                            <strong>Prepared By:</strong><br><br>
                            ' . htmlspecialchars($_SESSION['user']['name'] ?? 'Laboratory Staff') . '<br>
                            <span style="font-size:8px;color:#666;">Property Custodian / Faculty</span>
                        </td>
                        <td>
                            <strong>Verified By:</strong><br><br>
                            Laboratory Administrator<br>
                            <span style="font-size:8px;color:#666;">IT Laboratories Head</span>
                        </td>
                        <td>
                            <strong>Approved By:</strong><br><br>
                            Campus Dean / Director<br>
                            <span style="font-size:8px;color:#666;">University of Perpetual Help System</span>
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("UPHS_Equipment_Property_Codes_" . date('Ymd_His') . ".pdf", ["Attachment" => false]);
        exit;
    }

    /**
     * Individual Equipment Property Tag / Card PDF (Printable Sticker format)
     */
    public function propertyCardPdf(int $id): void
    {
        $equipment = Equipment::rawOne(
            "SELECT e.*, ec.name as category_name, l.name as laboratory_name, l.room_number 
             FROM equipment e 
             JOIN equipment_categories ec ON e.category_id = ec.id 
             JOIN laboratories l ON e.laboratory_id = l.id 
             WHERE e.id = ?", [$id]
        );

        if (!$equipment) {
            view('errors.404');
            return;
        }

        $qrFile = dirname(__DIR__, 2) . '/public/' . ltrim($equipment['qr_code'] ?? '', '/');
        $qrBase64 = '';
        if (file_exists($qrFile)) {
            $qrBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($qrFile));
        }

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Property Tag - ' . htmlspecialchars($equipment['property_code']) . '</title>
            <style>
                body { font-family: Helvetica, Arial, sans-serif; margin: 20px; color: #111; }
                .card { width: 340px; border: 2px solid #1e3a8a; border-radius: 8px; padding: 12px; background: #fff; }
                .top { text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 6px; margin-bottom: 8px; }
                .top h2 { font-size: 11px; margin: 0; color: #1e3a8a; text-transform: uppercase; }
                .top h3 { font-size: 9px; margin: 2px 0 0 0; color: #333; }
                .body { width: 100%; }
                .prop-title { font-size: 8px; color: #666; text-transform: uppercase; }
                .prop-val { font-size: 12px; font-weight: bold; font-family: Courier, monospace; color: #1e40af; margin-bottom: 4px; }
                .field { font-size: 9px; margin-bottom: 3px; }
                .field strong { color: #333; }
                .qr-box { text-align: center; margin-top: 6px; }
                .qr-box img { width: 90px; height: 90px; }
                .caution { font-size: 7px; color: #b91c1c; text-align: center; margin-top: 6px; border-top: 1px dashed #ccc; padding-top: 4px; }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="top">
                    <h2>Univ. of Perpetual Help System</h2>
                    <h3>PROPERTY INVENTORY TAG &middot; FIRST SEMESTER</h3>
                </div>
                <div class="body">
                    <div class="prop-title">Official Property Code</div>
                    <div class="prop-val">' . htmlspecialchars($equipment['property_code'] ?? 'PROP-N/A') . '</div>

                    <div class="field"><strong>Asset Tag:</strong> ' . htmlspecialchars($equipment['asset_tag']) . '</div>
                    <div class="field"><strong>Item Name:</strong> ' . htmlspecialchars($equipment['name']) . '</div>
                    <div class="field"><strong>Classification:</strong> ' . htmlspecialchars($equipment['classification'] ?? 'ICT Equipment') . '</div>
                    <div class="field"><strong>Location:</strong> ' . htmlspecialchars($equipment['laboratory_name']) . ' (' . htmlspecialchars($equipment['room_number']) . ')</div>
                    <div class="field"><strong>Serial No:</strong> ' . htmlspecialchars($equipment['serial_number'] ?? 'N/A') . '</div>
                    <div class="field"><strong>Acquired:</strong> ' . htmlspecialchars($equipment['acquisition_date'] ?? date('Y-m-d')) . '</div>

                    ' . ($qrBase64 ? '<div class="qr-box"><img src="' . $qrBase64 . '"><br><span style="font-size:7px;color:#777;">SCAN FOR VERIFICATION</span></div>' : '') . '

                    <div class="caution">DO NOT REMOVE OR TAMPER UNDER PENALTY OF LAW</div>
                </div>
            </div>
        </body>
        </html>';

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 400, 360]); // Custom sticker card size
        $dompdf->render();
        $dompdf->stream("Property_Tag_" . $equipment['asset_tag'] . ".pdf", ["Attachment" => false]);
        exit;
    }

    public function regenerateQR(int $id): void
    {
        $eq = Equipment::find($id);
        if ($eq) {
            $qrPath = $this->qrService->generateEquipmentQR($id, $eq['asset_tag']);
            Equipment::update($id, ['qr_code' => $qrPath]);
            session_flash('success', 'QR code regenerated.');
        }
        redirect("/equipment/{$id}");
    }

    public function viewQR(int $id): void
    {
        $equipment = Equipment::find($id);
        if (!$equipment) {
            view('errors.404');
            return;
        }
        view('equipment.qr', ['equipment' => $equipment]);
    }

    public function scanEquipmentQR(int $id): void
    {
        $equipment = Equipment::find($id);
        if (!$equipment) {
            view('errors.404');
            return;
        }
        view('equipment.scan-result', ['equipment' => $equipment]);
    }

    public function statusDistribution(): void
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT status, COUNT(*) as count FROM equipment GROUP BY status");
        $this->json($stmt->fetchAll());
    }

    public function findByAssetTag(): void
    {
        $query = $_POST['query'] ?? '';
        if (empty($query)) {
            $this->json(null, 404);
            return;
        }
        $equipment = Equipment::whereFirst('asset_tag', $query);
        if (!$equipment) {
            $equipment = Equipment::whereFirst('property_code', $query);
        }
        if ($equipment) {
            $this->json($equipment);
        } else {
            $stmt = Database::connect()->prepare("SELECT * FROM equipment WHERE id = ?");
            $stmt->execute([(int)$query]);
            $result = $stmt->fetch();
            $this->json($result ?: null, $result ? 200 : 404);
        }
    }

    public function printQr(): void
    {
        $labId = $_GET['laboratory_id'] ?? null;
        $sql = "SELECT e.*, ec.name as category_name, l.name as laboratory_name FROM equipment e JOIN equipment_categories ec ON e.category_id = ec.id JOIN laboratories l ON e.laboratory_id = l.id WHERE e.qr_code IS NOT NULL";
        $params = [];
        if ($labId) {
            $sql .= " AND e.laboratory_id = ?";
            $params[] = (int)$labId;
        }
        $sql .= " ORDER BY l.name, e.name";
        $equipment = Equipment::raw($sql, $params);
        $laboratories = Laboratory::where('status', 'active');
        view('equipment.print-all', compact('equipment', 'laboratories', 'labId'));
    }
}
