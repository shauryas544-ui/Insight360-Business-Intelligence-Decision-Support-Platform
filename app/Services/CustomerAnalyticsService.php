<?php

namespace App\Services;

use App\Helpers\Database;

class CustomerAnalyticsService
{
    public function getCustomerAnalytics(): array
    {
        // Segment Distribution from RFM
        $segmentQuery = "
            SELECT
                cs.segment_name,
                COUNT(cs.customer_id) as customer_count,
                COALESCE(SUM(cs.monetary), 0.0) as total_revenue,
                COALESCE(AVG(cs.monetary), 0.0) as avg_revenue,
                COALESCE(AVG(cs.recency_days), 0.0) as avg_recency,
                COALESCE(AVG(cs.frequency), 0.0) as avg_frequency
            FROM customer_segments cs
            GROUP BY cs.segment_name
            ORDER BY total_revenue DESC
        ";
        $segments = Database::fetchAll($segmentQuery);

        // Top 20 High-Value Customers
        $topCustQuery = "
            SELECT
                c.id,
                c.customer_code,
                c.name,
                c.city,
                c.state,
                r.name as region_name,
                cs.segment_name,
                cs.recency_days,
                cs.frequency,
                cs.monetary,
                cs.rfm_score
            FROM customer_segments cs
            JOIN customers c ON cs.customer_id = c.id
            JOIN regions r ON c.region_id = r.id
            ORDER BY cs.monetary DESC
            LIMIT 25
        ";
        $topCustomers = Database::fetchAll($topCustQuery);

        // Cohort Retention Matrix
        $cohortQuery = "
            SELECT
                cohort_month,
                period_month,
                active_customers,
                retention_rate,
                cohort_revenue
            FROM customer_cohorts
            ORDER BY cohort_month ASC, period_month ASC
        ";
        $cohortRows = Database::fetchAll($cohortQuery);

        // Format cohorts as matrix
        $cohortMatrix = [];
        $cohortMonths = [];
        $maxPeriod = 0;
        foreach ($cohortRows as $row) {
            $cMonth = $row['cohort_month'];
            $p = (int) $row['period_month'];
            if (!isset($cohortMatrix[$cMonth])) {
                $cohortMatrix[$cMonth] = [];
                $cohortMonths[] = $cMonth;
            }
            $cohortMatrix[$cMonth][$p] = [
                'active' => (int) $row['active_customers'],
                'rate' => (float) $row['retention_rate'],
                'revenue' => (float) $row['cohort_revenue']
            ];
            if ($p > $maxPeriod) {
                $maxPeriod = $p;
            }
        }

        // Customer KPIs
        $totalCustomers = (int) Database::fetchColumn("SELECT COUNT(*) FROM customers");
        $repeatCustomers = (int) Database::fetchColumn("SELECT COUNT(*) FROM customer_segments WHERE frequency > 1");
        $repeatRate = $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100.0, 2) : 0.0;

        return [
            'total_customers' => $totalCustomers,
            'repeat_customers' => $repeatCustomers,
            'repeat_rate' => $repeatRate,
            'segments' => $segments,
            'top_customers' => $topCustomers,
            'cohort_matrix' => $cohortMatrix,
            'cohort_months' => $cohortMonths,
            'max_period' => min($maxPeriod, 8)
        ];
    }
}

