<?php

namespace App\Controllers;

use App\Helpers\BaseController;
use App\Services\AuthService;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function loginForm(): void
    {
        if (isset($_SESSION['user'])) {
            redirect('/dashboard');
        }
        view('auth.login');
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $errors = $this->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!empty($errors)) {
            $_SESSION['_old'] = $_POST;
            session_flash('error', 'Please check your input.');
            view('auth.login', ['errors' => $errors]);
            return;
        }

        if ($this->authService->attempt($email, $password)) {
            session_flash('success', 'Welcome back, ' . $_SESSION['user']['name'] . '!');
            redirect('/dashboard');
        } else {
            session_flash('error', 'Invalid email or password.');
            view('auth.login');
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
        session_flash('success', 'You have been logged out.');
        redirect('/login');
    }

    public function profile(): void
    {
        view('auth.profile');
    }

    public function updateProfile(): void
    {
        $user = $_SESSION['user'];
        $this->authService->updateProfile($user['id'], $_POST);
        session_flash('success', 'Profile updated successfully.');
        redirect('/profile');
    }

    public function changePasswordForm(): void
    {
        view('auth.change-password');
    }

    public function changePassword(): void
    {
        $user = $_SESSION['user'];
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($new !== $confirm) {
            session_flash('error', 'New passwords do not match.');
            redirect('/change-password');
            return;
        }

        if ($this->authService->changePassword($user['id'], $current, $new)) {
            session_flash('success', 'Password changed successfully.');
        } else {
            session_flash('error', 'Current password is incorrect.');
        }
        redirect('/change-password');
    }
}
