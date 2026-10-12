<?php

namespace App\Services;

use App\Helpers\Database;
use PDO;

class AuthService
{
    public function attempt(string $email, string $password): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ? AND u.status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            $this->logActivity($user['id'], 'login', 'User logged in');
            return true;
        }
        return false;
    }

    public function logout(): void
    {
        if (isset($_SESSION['user'])) {
            $this->logActivity($_SESSION['user']['id'], 'logout', 'User logged out');
        }
        session_destroy();
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user && password_verify($currentPassword, $user['password'])) {
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hash, $userId]);
            $this->logActivity($userId, 'password_change', 'Password changed');
            return true;
        }
        return false;
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $db = Database::connect();
        $allowed = ['name', 'email'];
        $updates = [];
        $params = [];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($updates)) return false;

        $params[] = $userId;
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            $this->logActivity($userId, 'profile_update', 'Profile updated');
            $stmt = $db->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
            $stmt->execute([$userId]);
            $_SESSION['user'] = $stmt->fetch();
        }
        return $result;
    }

    public function createUser(array $data): int
    {
        $db = Database::connect();
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role_id, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['email'], $data['password'], $data['role_id'], $data['status'] ?? 'active']);
        return (int)$db->lastInsertId();
    }

    public function getAllUsers(): array
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC");
        return $stmt->fetchAll();
    }

    private function logActivity(int $userId, string $action, string $description): void
    {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $description, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
    }
}
