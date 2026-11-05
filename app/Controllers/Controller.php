<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Helpers\Response;
use App\Helpers\Session;
use App\Helpers\Database;

abstract class Controller
{
    protected ?array $user = null;

    public function __construct()
    {
        $this->user = Session::get('user');
    }

    protected function render(string $view, array $data = []): void
    {
        $viewFile = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$viewFile}");
        }

        // Shared view variables
        $data['currentUser'] = $this->user;
        $data['csrfToken'] = Session::generateCsrf();
        $data['flashSuccess'] = Session::flash('success');
        $data['flashError'] = Session::flash('error');
        $data['flashWarning'] = Session::flash('warning');
        $data['flashInfo'] = Session::flash('info');

        extract($data);

        // Capture content of view
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render main layout if not partial/auth view
        if (isset($noLayout) && $noLayout === true) {
            echo $content;
        } else {
            $layoutFile = dirname(__DIR__) . '/Views/layouts/main.php';
            require $layoutFile;
        }
    }

    protected function json(mixed $data, int $status = 200): void
    {
        Response::json([
            'success' => $status >= 200 && $status < 300,
            'data' => $data,
            'message' => null
        ], $status);
    }

    protected function errorJson(string $message, int $status = 400, mixed $errors = null): void
    {
        Response::error($message, $status, $errors);
    }

    protected function validateCsrf(Request $request): bool
    {
        $token = $request->input('_csrf_token') ?? $request->header('X-CSRF-Token');
        if (!Session::validateCsrf($token)) {
            if ($request->isJson()) {
                $this->errorJson('CSRF token validation failed.', 403);
            } else {
                Session::flash('error', 'Security token expired. Please try again.');
                Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
            return false;
        }
        return true;
    }

    protected function logAudit(string $action, string $entityType = 'system', ?int $entityId = null, array $metadata = []): void
    {
        try {
            $userId = $this->user['id'] ?? null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $metaJson = json_encode(array_merge($metadata, ['ip' => $ip]));

            Database::execute(
                "INSERT INTO audit_logs (user_id, action, entity_type, entity_id, status, metadata, created_at)
                 VALUES (?, ?, ?, ?, 'SUCCESS', ?, ?)",
                [$userId, $action, $entityType, $entityId, $metaJson, date('Y-m-d H:i:s')]
            );
        } catch (\Throwable $e) {
            error_log("Audit log failed: " . $e->getMessage());
        }
    }
}

