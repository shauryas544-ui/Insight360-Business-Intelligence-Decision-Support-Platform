<?php

require_once dirname(__DIR__) . '/app/Helpers/Autoloader.php';
\App\Helpers\Autoloader::register(dirname(__DIR__));
\App\Helpers\Env::load(dirname(__DIR__) . '/.env');

use App\Helpers\Database;

echo "=== InsightFlow Database Migration Runner ===\n";
$driver = Database::getDriver();
echo "Selected Driver: " . strtoupper($driver) . "\n";

try {
    $schemaFile = $driver === 'mysql'
        ? __DIR__ . '/schema.sql'
        : __DIR__ . '/schema_sqlite.sql';

    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: {$schemaFile}");
    }

    $sql = file_get_contents($schemaFile);

    $pdo = Database::getConnection();

    if ($driver === 'sqlite') {
        $pdo->exec($sql);
    } else {
        // MySQL multi-query execution
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
        $pdo->exec($sql);
    }

    echo "[OK] Schema tables migrated successfully.\n";

} catch (Exception $e) {
    echo "[ERROR] Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

