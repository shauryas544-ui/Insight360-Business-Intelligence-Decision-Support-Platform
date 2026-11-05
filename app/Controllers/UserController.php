<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Response;

class UserController extends Controller
{
    public function index(): void
    {
        $users = Database::fetchAll("
            SELECT u.id, u.name, u.email, u.status, u.last_login_at, u.created_at, r.name as role_name, r.display_name as role_display
            FROM users u
            JOIN roles r ON u.role_id = r.id
            ORDER BY u.id ASC
        ");

        $roles = Database::fetchAll("SELECT * FROM roles ORDER BY id ASC");
        $auditLogs = Database::fetchAll("
            SELECT a.*, u.name as user_name
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.id DESC
            LIMIT 50
        ");

        $data = [
            'pageTitle' => 'System Administration & Security Audits',
            'activeMenu' => 'settings',
            'users' => $users,
            'roles' => $roles,
            'auditLogs' => $auditLogs,
        ];

        $this->render('settings.index', $data);
    }

    public function createUser(Request $request): void
    {
        if (!$this->validateCsrf($request)) {
            return;
        }

        $name = trim((string) $request->post('name'));
        $email = trim((string) $request->post('email'));
        $password = (string) $request->post('password');
        $roleId = (int) $request->post('role_id', 2);

        if (empty($name) || empty($email) || empty($password)) {
            Session::flash('error', 'All fields are required to create a new user.');
            Response::redirect('/settings');
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            Database::execute(
                "INSERT INTO users (name, email, password_hash, role_id, status) VALUES (?, ?, ?, ?, 'active')",
                [$name, $email, $hash, $roleId]
            );
            $newId = Database::lastInsertId();
            $this->logAudit('USER_CREATED', 'users', (int) $newId, ['email' => $email, 'role_id' => $roleId]);
            Session::flash('success', "User {$name} created successfully.");
        } catch (\Throwable $e) {
            Session::flash('error', 'User creation failed: Email may already exist.');
        }

        Response::redirect('/settings');
    }
}

