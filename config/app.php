<?php

use App\Helpers\Env;

return [
    'name' => Env::get('APP_NAME', 'InsightFlow'),
    'tagline' => Env::get('APP_TAGLINE', 'Sales & Business Intelligence Analytics Platform'),
    'env' => Env::get('APP_ENV', 'production'),
    'debug' => (bool) Env::get('APP_DEBUG', false),
    'url' => Env::get('APP_URL', 'http://localhost:8080'),
    'key' => Env::get('APP_KEY', 'default_secret_key_change_in_production'),
    'session_lifetime' => (int) Env::get('SESSION_LIFETIME', 120),
    'csrf_protection' => (bool) Env::get('CSRF_PROTECTION', true),
    'python_path' => Env::get('PYTHON_PATH', 'python'),
    'python_service_url' => Env::get('PYTHON_SERVICE_URL', 'http://127.0.0.1:8000'),
    'python_service_token' => Env::get('PYTHON_SERVICE_TOKEN', ''),
];

