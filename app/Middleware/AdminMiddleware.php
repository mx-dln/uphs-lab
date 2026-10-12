<?php

namespace App\Middleware;

/**
 * AdminMiddleware
 * Restricts access strictly to Administrators (Super Admin & Laboratory Admin).
 * Prevents Faculty, HR, and other roles from accessing core admin functions.
 */
class AdminMiddleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return false;
        }

        $userRole = (int)($_SESSION['user']['role_id'] ?? 0);
        // Only Super Admin (1) and Laboratory Admin (2)
        if (!in_array($userRole, [1, 2])) {
            if (!headers_sent()) {
                http_response_code(403);
            }
            view('errors.403');
            return false;
        }

        return true;
    }
}
