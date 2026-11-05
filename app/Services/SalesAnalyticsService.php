<?php

namespace App\Services;

use App\Helpers\Database;

class SalesAnalyticsService
{
    public function getSalesAnalytics(?string $startDate = null, ?string $endDate = null): array
    {
        $dateCondition = "";
        $params = [];
        if ($startDate && $endDate) {
            $dateCondition = " WHERE o.order_date BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }

        // Monthly Breakdown
        $monthlyQuery = "
            SELECT
                " . Database::dateFormat('o.order_date', 'YYYY-MM') . " as ym,
                COALESCE(SUM(o.total_amount), 0.0) as revenue,
                COALESCE(SUM(oi.profit), 0.0) as profit,
                COUNT(DISTINCT o.id) as orders,
                COUNT(DISTINCT o.customer_id) as customers
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            {$dateCondition}
            GROUP BY ym
            ORDER BY ym ASC
        ";
        $monthly = Database::fetchAll($monthlyQuery, $params);

        // Sales by Channel
        $channelQuery = "
            SELECT
                o.channel,
                COUNT(o.id) as order_count,
                COALESCE(SUM(o.total_amount), 0.0) as revenue
            FROM orders o
            {$dateCondition}
            GROUP BY o.channel
            ORDER BY revenue DESC
        ";
        $channels = Database::fetchAll($channelQuery, $params);

        // Top Sales Reps
        $repQuery = "
            SELECT
                s.name as rep_name,
                r.name as region_name,
                s.target_quarterly,
                COUNT(DISTINCT o.id) as deals_closed,
                COALESCE(SUM(o.total_amount), 0.0) as total_sales
            FROM salespersons s
            JOIN regions r ON s.region_id = r.id
            LEFT JOIN orders o ON s.id = o.salesperson_id
            GROUP BY s.id
            ORDER BY total_sales DESC
        ";
        $salesReps = Database::fetchAll($repQuery);

        // Product Category Contribution
        $catQuery = "
            SELECT
                c.name as category_name,
                COUNT(DISTINCT oi.order_id) as order_count,
                COALESCE(SUM(oi.quantity), 0) as units_sold,
                COALESCE(SUM(oi.unit_price * oi.quantity - oi.discount), 0.0) as revenue,
                COALESCE(SUM(oi.profit), 0.0) as profit
            FROM categories c
            JOIN products p ON c.id = p.category_id
            JOIN order_items oi ON p.id = oi.product_id
            GROUP BY c.id
            ORDER BY revenue DESC
        ";
        $categories = Database::fetchAll($catQuery);

        return [
            'monthly' => $monthly,
            'channels' => $channels,
            'sales_reps' => $salesReps,
            'categories' => $categories
        ];
    }
}

