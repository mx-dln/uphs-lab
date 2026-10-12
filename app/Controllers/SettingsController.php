<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Helpers\Database;

class SettingsController extends BaseController
{
    public function index(): void
    {
        if (!isAdmin()) {
            http_response_code(403);
            view('errors.403');
            return;
        }

        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM settings");
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        view('settings.index', ['settings' => $settings]);
    }

    public function update(): void
    {
        if (!isAdmin()) {
            http_response_code(403);
            view('errors.403');
            return;
        }

        $db = Database::connect();
        foreach ($_POST as $key => $value) {
            $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$key, $value, $value]);
        }
        session_flash('success', 'Settings updated successfully.');
        redirect('/settings');
    }
}
