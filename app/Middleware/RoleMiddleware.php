<?php

namespace App\Middleware;

use App\Helpers\Request;
use App\Helpers\Response;
use App\Helpers\Session;

class RoleMiddleware
{
    protected array $allowedRoles;

    public function __construct(array $allowedRoles = [])
    {
        $this->allowedRoles = array_map('strtolower', $allowedRoles);
    }

    public function handle(Request $request): bool
    {
        $user = Session::get('user');

        if (!$user) {
            if ($request->isJson() || str_starts_with($request->uri(), '/api/')) {
                Response::error('Unauthenticated', 401);
                return false;
            }
            Response::redirect('/login');
            return false;
        }

        $userRole = strtolower($user['role_name'] ?? '');

        // Admin has universal access
        if ($userRole === 'admin') {
            return true;
        }

        if (!empty($this->allowedRoles) && !in_array($userRole, $this->allowedRoles, true)) {
            if ($request->isJson() || str_starts_with($request->uri(), '/api/')) {
                Response::error('Forbidden: You do not have permission to access this resource.', 403);
                return false;
            }

            Session::flash('error', 'Access denied: You lack sufficient role permissions.');
            Response::redirect('/dashboard');
            return false;
        }

        return true;
    }
}

