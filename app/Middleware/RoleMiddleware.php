<?php

namespace App\Middleware;

class RoleMiddleware
{
    private array $allowedRoles;

    public function __construct(array $allowedRoles = [])
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(): bool
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return false;
        }

        if (!empty($this->allowedRoles)) {
            $userRole = (int)($_SESSION['user']['role_id'] ?? 0);
            
            $effectiveAllowed = $this->allowedRoles;
            // Super Admin (1) and Lab Admin (2) can access general faculty, custodian, and HR management routes
            if (in_array(4, $this->allowedRoles) || in_array(3, $this->allowedRoles) || in_array(6, $this->allowedRoles)) {
                $effectiveAllowed[] = 1;
                $effectiveAllowed[] = 2;
            }
            // Faculty (4) and HR (6) cannot access restricted admin routes unless explicitly listed

            if (!in_array($userRole, $effectiveAllowed)) {
                if (!headers_sent()) {
                    http_response_code(403);
                }
                view('errors.403');
                return false;
            }
        }
        return true;
    }
}
