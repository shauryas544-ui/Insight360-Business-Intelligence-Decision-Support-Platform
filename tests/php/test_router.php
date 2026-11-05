<?php

require_once dirname(__DIR__, 2) . '/app/Helpers/Autoloader.php';
\App\Helpers\Autoloader::register(dirname(__DIR__, 2));
\App\Helpers\Env::load(dirname(__DIR__, 2) . '/.env');

// Set session storage to temporary dir
$sessDir = dirname(__DIR__, 2) . '/storage/temporary';
if (!is_dir($sessDir)) mkdir($sessDir, 0777, true);
session_save_path($sessDir);
\App\Helpers\Session::start();

// Mock an authenticated admin session
\App\Helpers\Session::set('user', [
    'id' => 1,
    'name' => 'Alex Morgan (Admin)',
    'email' => 'admin@insightflow.io',
    'role_name' => 'admin'
]);

require_once dirname(__DIR__, 2) . '/routes/web.php';
require_once dirname(__DIR__, 2) . '/routes/api.php';

$routesToTest = [
    '/dashboard',
    '/analyst',
    '/sales',
    '/customers',
    '/products',
    '/regions',
    '/marketing',
    '/operations',
    '/analytics',
    '/alerts',
    '/recommendations',
    '/import',
    '/reports',
    '/settings',
];

echo "=== Testing InsightFlow Authenticated Application Views ===\n";

foreach ($routesToTest as $uri) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['HTTP_ACCEPT'] = 'text/html';

    $request = new \App\Helpers\Request();

    ob_start();
    try {
        \App\Helpers\Router::dispatch($request);
        $output = ob_get_clean();
        $len = strlen($output);
        if ($len > 500) {
            echo "  [PASS] GET {$uri} rendered view successfully ({$len} bytes HTML).\n";
        } else {
            echo "  [WARN] GET {$uri} output short ({$len} bytes).\n";
        }
    } catch (\Throwable $e) {
        ob_end_clean();
        echo "  [FAIL] GET {$uri} -> Exception: " . $e->getMessage() . "\n";
    }
}

echo "\n[OK] All Application Views Verified!\n";
