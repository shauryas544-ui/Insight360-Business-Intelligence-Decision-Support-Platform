<?php

namespace App\Services;

use App\Helpers\Database;

class OperationsAnalyticsService
{
    public function getOperationsAnalytics(): array
    {
        // Return Reasons Breakdown
        $returnQuery = "
            SELECT
                reason,
                COUNT(id) as count,
                COALESCE(SUM(refund_amount), 0.0) as total_refunded
            FROM returns
            GROUP BY reason
            ORDER BY count DESC
        ";
        $returnReasons = Database::fetchAll($returnQuery);

        // Inventory Alerts (Items near or below safety stock / reorder level)
        $invQuery = "
            SELECT
                p.sku,
                p.product_name,
                c.name as category_name,
                i.current_stock,
                i.safety_stock,
                i.reorder_level,
                CASE
                    WHEN i.current_stock <= i.safety_stock THEN 'CRITICAL'
                    WHEN i.current_stock <= i.reorder_level THEN 'WARNING'
                    ELSE 'HEALTHY'
                END as stock_status
            FROM inventory i
            JOIN products p ON i.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            ORDER BY i.current_stock ASC
        ";
        $inventory = Database::fetchAll($invQuery);

        $totalOrders = (int) Database::fetchColumn("SELECT COUNT(*) FROM orders");
        $totalReturns = (int) Database::fetchColumn("SELECT COUNT(*) FROM returns");
        $totalRefunded = (float) Database::fetchColumn("SELECT SUM(refund_amount) FROM returns");
        $returnRate = $totalOrders > 0 ? round(($totalReturns / $totalOrders) * 100.0, 2) : 0.0;

        return [
            'total_orders' => $totalOrders,
            'total_returns' => $totalReturns,
            'total_refunded' => $totalRefunded,
            'return_rate' => $returnRate,
            'return_reasons' => $returnReasons,
            'inventory' => $inventory
        ];
    }
}

