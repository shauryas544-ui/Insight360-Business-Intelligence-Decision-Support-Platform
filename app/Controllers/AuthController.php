<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Helpers\Response;
use App\Helpers\Session;
use App\Helpers\Database;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Session::get('user')) {
            Response::redirect('/dashboard');
        }
        $this->render('auth.login', ['noLayout' => true]);
    }

    public function login(Request $request): void
    {
        if (!$this->validateCsrf($request)) {
            return;
        }

        $email = trim((string) $request->post('email'));
        $password = (string) $request->post('password');

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Please provide both email and password.');
            Response::redirect('/login');
            return;
        }

        $user = Database::fetchOne(
            "SELECT u.*, r.name as role_name, r.display_name as role_display
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.email = ? AND u.status = 'active'",
            [$email]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            Session::flash('error', 'Invalid email credentials or inactive account.');
            Response::redirect('/login');
            return;
        }

        // Update last login
        Database::execute("UPDATE users SET last_login_at = ? WHERE id = ?", [date('Y-m-d H:i:s'), $user['id']]);

        // Store user in session (excluding password_hash)
        unset($user['password_hash']);
        Session::set('user', $user);

        $this->logAudit('USER_LOGIN', 'users', $user['id'], ['email' => $email]);

        Session::flash('success', "Welcome back, {$user['name']}!");
        Response::redirect('/dashboard');
    }

    public function logout(): void
    {
        if ($this->user) {
            $this->logAudit('USER_LOGOUT', 'users', $this->user['id']);
        }
        Session::destroy();
        Response::redirect('/login');
    }
}

