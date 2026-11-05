<?php

require_once dirname(__DIR__) . '/app/Helpers/Autoloader.php';
\App\Helpers\Autoloader::register(dirname(__DIR__));
\App\Helpers\Env::load(dirname(__DIR__) . '/.env');

require_once __DIR__ . '/seeders/DatabaseSeeder.php';

$seeder = new \Database\Seeders\DatabaseSeeder();
$seeder->run();
