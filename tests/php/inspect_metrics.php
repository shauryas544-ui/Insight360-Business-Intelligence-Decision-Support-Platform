<?php
require_once dirname(__DIR__, 2) . '/app/Helpers/Autoloader.php';
\App\Helpers\Autoloader::register(dirname(__DIR__, 2));
\App\Helpers\Env::load(dirname(__DIR__, 2) . '/.env');

use App\Helpers\Database;

$rows = Database::fetchAll("SELECT year_month, revenue, profit, order_count, aov FROM monthly_metrics ORDER BY year_month DESC LIMIT 6");
foreach ($rows as $r) {
    echo $r['year_month'] . " -> Rev: $" . number_format((float)$r['revenue'], 2) . ", Profit: $" . number_format((float)$r['profit'], 2) . ", Orders: " . $r['order_count'] . ", AOV: $" . number_format((float)$r['aov'], 2) . "\n";
}

