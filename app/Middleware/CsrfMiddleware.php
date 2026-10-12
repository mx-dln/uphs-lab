<?php

namespace App\Middleware;

class CsrfMiddleware
{
    public function handle(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrf_token'] ?? '';
            $sessionToken = $_SESSION['_csrf_token'] ?? '';
            if (empty($token) || !hash_equals($sessionToken, $token)) {
                http_response_code(419);
                die('CSRF token mismatch.');
            }
        }
        return true;
    }
}
