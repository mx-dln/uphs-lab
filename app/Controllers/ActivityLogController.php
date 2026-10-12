<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Helpers\Database;

class ActivityLogController extends BaseController
{
    public function index(): void
    {
        if (!isAdmin()) {
            http_response_code(403);
            view('errors.403');
            return;
        }

        $action = $_GET['action'] ?? '';
        $sql = "SELECT al.*, u.name as user_name FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id";
        $params = [];
        if ($action) {
            $sql .= " WHERE al.action = ?";
            $params[] = $action;
        }
        $sql .= " ORDER BY al.created_at DESC LIMIT 200";
        $logs = Database::connect()->prepare($sql);
        $logs->execute($params);
        $logs = $logs->fetchAll();

        $actions = Database::connect()->query("SELECT DISTINCT action FROM activity_logs ORDER BY action")->fetchAll();
        view('audit-logs.index', ['logs' => $logs, 'actions' => $actions, 'selectedAction' => $action]);
    }
}
