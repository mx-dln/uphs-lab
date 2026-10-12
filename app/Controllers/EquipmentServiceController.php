<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Helpers\Database;
use App\Helpers\Constants;
use App\Models\EquipmentService;
use App\Models\Equipment;

class EquipmentServiceController extends BaseController
{
    public function index(): void
    {
        $db = Database::connect();
        $type = $_GET['type'] ?? '';
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $sql = "
            SELECT es.*, e.name as equipment_name, e.asset_tag, e.property_code, e.serial_number, l.name as laboratory_name
            FROM equipment_services es
            JOIN equipment e ON es.equipment_id = e.id
            JOIN laboratories l ON e.laboratory_id = l.id
            WHERE 1=1
        ";
        $params = [];

        if ($type) {
            $sql .= " AND es.service_type = ?";
            $params[] = $type;
        }
        if ($status) {
            $sql .= " AND es.status = ?";
            $params[] = $status;
        }
        if ($search) {
            $sql .= " AND (e.name LIKE ? OR e.asset_tag LIKE ? OR e.property_code LIKE ? OR es.technician_name LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY es.service_date DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $services = $stmt->fetchAll();

        $serviceTypes = Constants::SERVICE_TYPES;
        view('equipment-services.index', compact('services', 'serviceTypes', 'type', 'status', 'search'));
    }

    public function create(): void
    {
        $equipmentId = !empty($_GET['equipment_id']) ? (int)$_GET['equipment_id'] : null;
        $equipmentList = Equipment::all();
        $serviceTypes = Constants::SERVICE_TYPES;
        view('equipment-services.form', compact('equipmentId', 'equipmentList', 'serviceTypes'));
    }

    public function store(): void
    {
        $data = $_POST;
        $errors = $this->validate($data, [
            'equipment_id' => 'required',
            'service_type' => 'required',
            'service_date' => 'required',
        ]);

        if (!empty($errors)) {
            session_flash('error', 'Please fill in all required service fields.');
            $equipmentList = Equipment::all();
            $serviceTypes = Constants::SERVICE_TYPES;
            $equipmentId = $data['equipment_id'] ?? null;
            view('equipment-services.form', compact('equipmentId', 'equipmentList', 'serviceTypes', 'errors'));
            return;
        }

        EquipmentService::create([
            'equipment_id' => (int)$data['equipment_id'],
            'service_type' => $data['service_type'],
            'service_date' => $data['service_date'],
            'technician_name' => $data['technician_name'] ?? '',
            'company_or_dept' => $data['company_or_dept'] ?? '',
            'cost' => floatval($data['cost'] ?? 0),
            'status' => $data['status'] ?? 'completed',
            'notes' => $data['notes'] ?? '',
            'next_service_date' => !empty($data['next_service_date']) ? $data['next_service_date'] : null,
        ]);

        // If service is marked in progress or under repair, update equipment status
        if (($data['status'] ?? '') === 'in_progress') {
            Equipment::update((int)$data['equipment_id'], ['status' => 'under_repair']);
        } elseif (($data['status'] ?? '') === 'completed') {
            Equipment::update((int)$data['equipment_id'], ['status' => 'active']);
        }

        session_flash('success', 'Service maintenance log saved successfully.');
        redirect('/equipment-services');
    }

    public function complete(int $id): void
    {
        $service = EquipmentService::find($id);
        if ($service) {
            EquipmentService::update($id, [
                'status' => 'completed',
                'notes' => ($service['notes'] ? $service['notes'] . " | " : "") . "Completed on " . date('Y-m-d')
            ]);
            Equipment::update($service['equipment_id'], ['status' => 'active']);
            session_flash('success', 'Service marked as completed.');
        }
        redirect('/equipment-services');
    }
}
