<?php

namespace App\Services;

use App\Helpers\Database;

class RegionalAnalyticsService
{
    public function getRegionalAnalytics(): array
    {
        $query = "
            SELECT
                r.id,
                r.name as region_name,
                r.country,
                r.state,
                r.city,
                r.code,
                COALESCE(SUM(o.total_amount), 0.0) as revenue,
                COALESCE(SUM(oi.profit), 0.0) as profit,
                COUNT(DISTINCT o.id) as orders,
                COUNT(DISTINCT o.customer_id) as customers,
                (SELECT COUNT(*) FROM returns ret JOIN orders o2 ON ret.order_id = o2.id WHERE o2.region_id = r.id) as returns_count
            FROM regions r
            LEFT JOIN orders o ON r.id = o.region_id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY r.id
            ORDER BY revenue DESC
        ";
        $regions = Database::fetchAll($query);

        $totalRevenue = array_sum(array_column($regions, 'revenue'));

        foreach ($regions as &$reg) {
            $r = (float) $reg['revenue'];
            $p = (float) $reg['profit'];
            $o = (int) $reg['orders'];
            $ret = (int) $reg['returns_count'];

            $reg['share_pct'] = $totalRevenue > 0 ? round(($r / $totalRevenue) * 100.0, 2) : 0.0;
            $reg['margin_pct'] = $r > 0 ? round(($p / $r) * 100.0, 2) : 0.0;
            $reg['aov'] = $o > 0 ? round($r / $o, 2) : 0.0;
            $reg['return_rate'] = $o > 0 ? round(($ret / $o) * 100.0, 2) : 0.0;
        }

        return [
            'regions' => $regions,
            'total_revenue' => $totalRevenue,
            'top_region' => $regions[0] ?? null,
            'lagging_region' => end($regions) ?: null
        ];
    }
}

