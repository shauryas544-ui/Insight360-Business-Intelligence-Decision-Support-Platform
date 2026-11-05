<?php

declare(strict_types=1);

define('INSIGHTFLOW_START', microtime(true));

$baseDir = dirname(__DIR__);

// 1. PSR-4 Autoloader
require_once $baseDir . '/app/Helpers/Autoloader.php';
\App\Helpers\Autoloader::register($baseDir);

// 2. Load Environment Variables
\App\Helpers\Env::load($baseDir . '/.env');

// 3. Error Handling
$debug = (bool) \App\Helpers\Env::get('APP_DEBUG', false);
if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// 4. Session Start
\App\Helpers\Session::start();

// 5. Load Routes
require_once $baseDir . '/routes/web.php';
require_once $baseDir . '/routes/api.php';

// 6. Dispatch Request
try {
    $request = new \App\Helpers\Request();
    \App\Helpers\Router::dispatch($request);
} catch (\Throwable $e) {
    error_log("Unhandled Application Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());

    $isApi = isset($_SERVER['REQUEST_URI']) && (
        str_starts_with($_SERVER['REQUEST_URI'], '/api/') ||
        (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
    );

    if ($isApi) {
        \App\Helpers\Response::error(
            $debug ? $e->getMessage() : 'An internal system error occurred.',
            500,
            $debug ? ['trace' => explode("\n", $e->getTraceAsString())] : null
        );
    } else {
        http_response_code(500);
        if ($debug) {
            echo "<div style='font-family:sans-serif; background:#fff3f3; color:#b30000; padding:25px; border-radius:8px; margin:40px auto; max-width:900px;'>";
            echo "<h2>System Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
            echo "<pre style='background:#fde8e8; padding:15px; border-radius:4px; overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "</div>";
        } else {
            echo "<div style='font-family:sans-serif; text-align:center; padding:60px;'>";
            echo "<h2>500 - Internal Server Error</h2>";
            echo "<p>Something went wrong. Please check back shortly or review application logs.</p>";
            echo "<a href='/dashboard'>Return to Dashboard</a>";
            echo "</div>";
        }
    }
}

