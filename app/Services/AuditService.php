<?php

namespace App\Services;

use App\Helpers\Database;

class AuditService
{
    public function startAudit(int $laboratoryId, int $userId): array
    {
        $db = Database::connect();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("INSERT INTO audits (laboratory_id, conducted_by, audit_date, status) VALUES (?, ?, CURDATE(), 'in_progress')");
            $stmt->execute([$laboratoryId, $userId]);
            $auditId = (int)$db->lastInsertId();

            $equipment = $db->prepare("SELECT id FROM equipment WHERE laboratory_id = ? AND status IN ('active', 'missing', 'defective')");
            $equipment->execute([$laboratoryId]);
            $items = $equipment->fetchAll();

            $insert = $db->prepare("INSERT INTO audit_items (audit_id, equipment_id, status) VALUES (?, ?, 'missing')");
            foreach ($items as $item) {
                $insert->execute([$auditId, $item['id']]);
            }

            $db->commit();
            $this->logActivity($userId, 'audit_start', "Audit #{$auditId} started for laboratory #{$laboratoryId}");
            return ['success' => true, 'audit_id' => $auditId];
        } catch (\Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function scanEquipment(int $auditId, int $equipmentId): array
    {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE audit_items SET status = 'present', scanned_at = NOW() WHERE audit_id = ? AND equipment_id = ?");
        $stmt->execute([$auditId, $equipmentId]);
        if ($stmt->rowCount() > 0) {
            $this->logActivity(0, 'equipment_scan', "Equipment #{$equipmentId} scanned in audit #{$auditId}");
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Equipment not found in this audit.'];
    }

    public function completeAudit(int $auditId, int $userId): array
    {
        $db = Database::connect();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("UPDATE audits SET status = 'completed', completed_at = NOW() WHERE id = ?");
            $stmt->execute([$auditId]);

            $missing = $db->prepare("SELECT ai.id, ai.equipment_id, e.name FROM audit_items ai JOIN equipment e ON ai.equipment_id = e.id WHERE ai.audit_id = ? AND ai.status = 'missing'");
            $missing->execute([$auditId]);
            $missingItems = $missing->fetchAll();

            // Mark present items as active
            $present = $db->prepare("SELECT equipment_id FROM audit_items WHERE audit_id = ? AND status = 'present'");
            $present->execute([$auditId]);
            $presentItems = $present->fetchAll();
            $updatePresent = $db->prepare("UPDATE equipment SET status = 'active' WHERE id = ?");
            foreach ($presentItems as $item) {
                $updatePresent->execute([$item['equipment_id']]);
            }

            // Mark missing items and create investigations
            $updateEq = $db->prepare("UPDATE equipment SET status = 'missing' WHERE id = ?");
            $checkInvestigation = $db->prepare("SELECT COUNT(*) FROM investigations WHERE equipment_id = ? AND status IN ('open', 'under_review')");
            $createInvestigation = $db->prepare("INSERT INTO investigations (equipment_id, audit_id, initiated_by, status) VALUES (?, ?, ?, 'open')");

            foreach ($missingItems as $item) {
                $updateEq->execute([$item['equipment_id']]);
                $checkInvestigation->execute([$item['equipment_id']]);
                if ($checkInvestigation->fetchColumn() == 0) {
                    $createInvestigation->execute([$item['equipment_id'], $auditId, $userId]);
                }
            }

            $db->commit();
            $this->logActivity($userId, 'audit_complete', "Audit #{$auditId} completed with " . count($missingItems) . " missing items");
            return ['success' => true, 'missing_items' => $missingItems];
        } catch (\Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getAuditResults(int $auditId): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT a.*, u.name as conducted_by_name, l.name as laboratory_name FROM audits a JOIN users u ON a.conducted_by = u.id JOIN laboratories l ON a.laboratory_id = l.id WHERE a.id = ?");
        $stmt->execute([$auditId]);
        $audit = $stmt->fetch();
        if (!$audit) return null;

        $items = $db->prepare("SELECT ai.*, e.name as equipment_name, e.asset_tag, e.status as equipment_status FROM audit_items ai JOIN equipment e ON ai.equipment_id = e.id WHERE ai.audit_id = ?");
        $items->execute([$auditId]);
        $audit['items'] = $items->fetchAll();

        $total = count($audit['items']);
        $present = count(array_filter($audit['items'], fn($i) => $i['status'] === 'present'));
        $audit['completion_percentage'] = $total > 0 ? round(($present / $total) * 100, 2) : 0;

        return $audit;
    }

    public function getAuditHistory(int $laboratoryId = null): array
    {
        $db = Database::connect();
        $sql = "SELECT a.*, u.name as conducted_by_name, l.name as laboratory_name FROM audits a JOIN users u ON a.conducted_by = u.id JOIN laboratories l ON a.laboratory_id = l.id";
        $params = [];
        if ($laboratoryId) {
            $sql .= " WHERE a.laboratory_id = ?";
            $params[] = $laboratoryId;
        }
        $sql .= " ORDER BY a.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getDashboardStats(): array
    {
        $db = Database::connect();
        $total = (int)$db->query("SELECT COUNT(*) FROM audits")->fetchColumn();
        $completed = (int)$db->query("SELECT COUNT(*) FROM audits WHERE status = 'completed'")->fetchColumn();
        $inProgress = (int)$db->query("SELECT COUNT(*) FROM audits WHERE status = 'in_progress'")->fetchColumn();
        $cancelled = (int)$db->query("SELECT COUNT(*) FROM audits WHERE status = 'cancelled'")->fetchColumn();
        $totalMissing = (int)$db->query("SELECT COUNT(*) FROM audit_items WHERE status = 'missing'")->fetchColumn();
        $totalPresent = (int)$db->query("SELECT COUNT(*) FROM audit_items WHERE status = 'present'")->fetchColumn();
        $auditedLabs = (int)$db->query("SELECT COUNT(DISTINCT laboratory_id) FROM audits")->fetchColumn();
        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'cancelled' => $cancelled,
            'total_missing' => $totalMissing,
            'total_present' => $totalPresent,
            'audited_labs' => $auditedLabs,
        ];
    }

    public function getRecentAudits(int $limit = 5): array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT a.*, u.name as conducted_by_name, l.name as laboratory_name, l.room_number FROM audits a JOIN users u ON a.conducted_by = u.id JOIN laboratories l ON a.laboratory_id = l.id ORDER BY a.created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getAuditStatusDistribution(): array
    {
        $db = Database::connect();
        return $db->query("SELECT status, COUNT(*) as count FROM audits GROUP BY status")->fetchAll();
    }

    private function logActivity(int $userId, string $action, string $description): void
    {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $description, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
    }
}
