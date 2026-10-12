<?php

date_default_timezone_set('Asia/Manila');

if (!function_exists('view')) {
    function view(string $name, array $data = []): void
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $name);
        $file = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $path . '.php';
        if (!file_exists($file)) {
            throw new \RuntimeException("View [{$name}] not found at: {$file}");
        }
        extract($data);
        require $file;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $clean = ltrim($path, '/');
        if (str_starts_with($clean, 'assets/')) {
            return '/' . $clean;
        }
        return '/assets/' . $clean;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = $_SESSION['_csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['_csrf_token'] = $token;
        return '<input type="hidden" name="_csrf_token" value="' . $token . '">';
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = null)
    {
        return $_SESSION['_old'][$key] ?? $default;
    }
}

if (!function_exists('session_flash')) {
    function session_flash(string $key, ?string $value = null): ?string
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('isAdminOrFaculty')) {
    function isAdminOrFaculty(): bool
    {
        $roleId = (int)($_SESSION['user']['role_id'] ?? 0);
        // Admin role same as faculty: roles 1 (Super Admin), 2 (Lab Admin), 4 (Faculty)
        return in_array($roleId, [1, 2, 4]);
    }
}

if (!function_exists('escape')) {
    function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('now')) {
    function now(string $format = 'Y-m-d H:i:s'): string
    {
        $dt = new DateTime('now', new DateTimeZone('Asia/Manila'));
        return $dt->format($format);
    }
}

if (!function_exists('today')) {
    function today(): string
    {
        return now('Y-m-d');
    }
}

if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        static $settings = null;
        if ($settings === null) {
            $settings = [];
            try {
                $db = \App\Helpers\Database::connect();
                $rows = $db->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
                foreach ($rows as $r) {
                    $settings[$r['setting_key']] = $r['setting_value'];
                }
            } catch (\Exception $e) {
                // Ignore during installation
            }
        }
        return $settings[$key] ?? $default;
    }
}

if (!function_exists('currentUser')) {
    function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('userRole')) {
    function userRole(): int
    {
        return (int)($_SESSION['user']['role_id'] ?? 0);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return in_array(userRole(), [1, 2]);
    }
}

if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin(): bool
    {
        return userRole() === 1;
    }
}

if (!function_exists('isFaculty')) {
    function isFaculty(): bool
    {
        return userRole() === 4;
    }
}

if (!function_exists('isCustodian')) {
    function isCustodian(): bool
    {
        return userRole() === 3;
    }
}

if (!function_exists('isHR')) {
    function isHR(): bool
    {
        return userRole() === 6;
    }
}

if (!function_exists('canManageUsers')) {
    function canManageUsers(): bool
    {
        return isAdmin();
    }
}

if (!function_exists('canDeleteAssets')) {
    function canDeleteAssets(): bool
    {
        return isAdmin();
    }
}
