<?php

namespace App\Services;

use App\Helpers\Database;

class InventoryService
{
    public function getDashboardStats(): array
    {
        $db = Database::connect();
        $total = $db->query("SELECT COUNT(*) as c FROM equipment")->fetch()['c'];
        $present = $db->query("SELECT COUNT(*) as c FROM equipment WHERE status = 'active'")->fetch()['c'];
        $missing = $db->query("SELECT COUNT(*) as c FROM equipment WHERE status = 'missing'")->fetch()['c'];
        $defective = $db->query("SELECT COUNT(*) as c FROM equipment WHERE status = 'defective'")->fetch()['c'];
        return [
            'total' => (int)$total,
            'present' => (int)$present,
            'missing' => (int)$missing,
            'defective' => (int)$defective,
        ];
    }

    public function getEquipmentByLaboratory(int $labId): array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM equipment WHERE laboratory_id = ? ORDER BY created_at DESC");
        $stmt->execute([$labId]);
        return $stmt->fetchAll();
    }

    public function getEquipmentByCategory(): array
    {
        $db = Database::connect();
        return $db->query("SELECT ec.name as category, COUNT(e.id) as count FROM equipment_categories ec LEFT JOIN equipment e ON e.category_id = ec.id GROUP BY ec.id, ec.name")->fetchAll();
    }

    public function getInventoryStatus(): array
    {
        $db = Database::connect();
        return $db->query("SELECT status, COUNT(*) as count FROM equipment GROUP BY status")->fetchAll();
    }
}
