<?php

namespace App\Middleware;

class GuestMiddleware
{
    public function handle(): bool
    {
        if (isset($_SESSION['user'])) {
            header('Location: /dashboard');
            return false;
        }
        return true;
    }
}
