<?php

namespace App\Middleware;

use App\Helpers\Request;
use App\Helpers\Response;
use App\Helpers\Session;

class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        $user = Session::get('user');

        if (!$user) {
            if ($request->isJson() || str_starts_with($request->uri(), '/api/')) {
                Response::error('Unauthenticated. Please log in.', 401);
                return false;
            }

            Session::flash('error', 'Please log in to continue.');
            Response::redirect('/login');
            return false;
        }

        return true;
    }
}

