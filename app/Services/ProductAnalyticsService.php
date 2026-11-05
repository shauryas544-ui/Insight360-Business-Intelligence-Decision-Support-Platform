<?php

namespace App\Services;

use App\Helpers\Database;

class ProductAnalyticsService
{
    public function getProductAnalytics(): array
    {
        $query = "
            SELECT
                p.id,
                p.sku,
                p.product_name,
                c.name as category_name,
                p.cost_price,
                p.selling_price,
                pm.units_sold,
                pm.revenue,
                pm.profit,
                pm.margin_pct,
                pm.return_rate,
                pm.pareto_class,
                i.current_stock,
                i.safety_stock
            FROM products p
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_metrics pm ON p.id = pm.product_id
            LEFT JOIN inventory i ON p.id = i.product_id
            ORDER BY pm.revenue DESC
        ";
        $products = Database::fetchAll($query);

        // Pareto Class Aggregation
        $paretoStats = ['A' => ['count' => 0, 'revenue' => 0.0], 'B' => ['count' => 0, 'revenue' => 0.0], 'C' => ['count' => 0, 'revenue' => 0.0]];
        $totalRev = 0.0;

        foreach ($products as $p) {
            $class = $p['pareto_class'] ?: 'B';
            $r = (float) $p['revenue'];
            $paretoStats[$class]['count']++;
            $paretoStats[$class]['revenue'] += $r;
            $totalRev += $r;
        }

        // 4-Quadrant Categorization
        // Median revenue and median margin
        $quadrants = [
            'stars' => [], // High Rev, High Margin
            'volume_risk' => [], // High Rev, Low Margin
            'niche' => [], // Low Rev, High Margin
            'underperforming' => [] // Low Rev, Low Margin
        ];

        $avgRev = count($products) > 0 ? $totalRev / count($products) : 0;
        $benchmarkMargin = 45.0; // 45% benchmark

        foreach ($products as $p) {
            $r = (float) $p['revenue'];
            $m = (float) $p['margin_pct'];
            if ($r >= $avgRev && $m >= $benchmarkMargin) {
                $quadrants['stars'][] = $p;
            } elseif ($r >= $avgRev && $m < $benchmarkMargin) {
                $quadrants['volume_risk'][] = $p;
            } elseif ($r < $avgRev && $m >= $benchmarkMargin) {
                $quadrants['niche'][] = $p;
            } else {
                $quadrants['underperforming'][] = $p;
            }
        }

        return [
            'products' => $products,
            'total_products' => count($products),
            'total_revenue' => $totalRev,
            'pareto_stats' => $paretoStats,
            'quadrants' => $quadrants
        ];
    }
}

