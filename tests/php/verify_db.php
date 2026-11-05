<?php

require_once dirname(__DIR__, 2) . '/app/Helpers/Autoloader.php';
\App\Helpers\Autoloader::register(dirname(__DIR__, 2));
\App\Helpers\Env::load(dirname(__DIR__, 2) . '/.env');

use App\Helpers\Database;

echo "--- Seeded Database Verification ---\n";
echo "Users: " . Database::fetchColumn("SELECT COUNT(*) FROM users") . "\n";
echo "Customers: " . Database::fetchColumn("SELECT COUNT(*) FROM customers") . "\n";
echo "Products: " . Database::fetchColumn("SELECT COUNT(*) FROM products") . "\n";
echo "Orders: " . Database::fetchColumn("SELECT COUNT(*) FROM orders") . "\n";
echo "Order Items: " . Database::fetchColumn("SELECT COUNT(*) FROM order_items") . "\n";
echo "Returns: " . Database::fetchColumn("SELECT COUNT(*) FROM returns") . "\n";
$totalRev = Database::fetchColumn("SELECT SUM(total_amount) FROM orders");
echo "Total Order Revenue: $" . number_format((float)$totalRev, 2) . "\n";
$firstOrder = Database::fetchColumn("SELECT MIN(order_date) FROM orders");
$lastOrder = Database::fetchColumn("SELECT MAX(order_date) FROM orders");
echo "Date Span: {$firstOrder} to {$lastOrder}\n";

