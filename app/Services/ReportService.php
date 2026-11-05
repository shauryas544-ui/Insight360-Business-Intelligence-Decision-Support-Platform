<?php

namespace App\Services;

use App\Helpers\Database;

class ReportService
{
    public function getMonthlyExecutiveReport(): array
    {
        $dash = new DashboardService();
        $summary = $dash->getExecutiveSummary();

        // Anomaly list
        $anomalies = Database::fetchAll("SELECT * FROM anomalies ORDER BY anomaly_date DESC LIMIT 5");

        return [
            'generated_at' => date('Y-m-d H:i:s'),
            'summary' => $summary,
            'anomalies' => $anomalies,
            'forecasts' => $summary['forecasts'],
            'recommendations' => $summary['recommendations']
        ];
    }

    public function exportCsv(string $type): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=insightflow_' . $type . '_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');

        if ($type === 'sales') {
            fputcsv($output, ['Order Number', 'Date', 'Customer Code', 'Region', 'Channel', 'Subtotal', 'Tax', 'Total Amount']);
            $rows = Database::fetchAll("
                SELECT o.order_number, o.order_date, c.customer_code, r.name as region, o.channel, o.subtotal, o.tax, o.total_amount
                FROM orders o
                JOIN customers c ON o.customer_id = c.id
                JOIN regions r ON o.region_id = r.id
                ORDER BY o.order_date DESC
                LIMIT 5000
            ");
            foreach ($rows as $r) {
                fputcsv($output, $r);
            }
        } elseif ($type === 'rfm') {
            fputcsv($output, ['Customer Code', 'Customer Name', 'City', 'State', 'Segment', 'Recency Days', 'Frequency', 'Monetary Spend', 'RFM Score']);
            $rows = Database::fetchAll("
                SELECT c.customer_code, c.name, c.city, c.state, cs.segment_name, cs.recency_days, cs.frequency, cs.monetary, cs.rfm_score
                FROM customer_segments cs
                JOIN customers c ON cs.customer_id = c.id
                ORDER BY cs.monetary DESC
            ");
            foreach ($rows as $r) {
                fputcsv($output, $r);
            }
        } elseif ($type === 'products') {
            fputcsv($output, ['SKU', 'Product Name', 'Category', 'Selling Price', 'Cost Price', 'Units Sold', 'Revenue', 'Profit', 'Margin %', 'Return %', 'Pareto Class']);
            $rows = Database::fetchAll("
                SELECT p.sku, p.product_name, c.name as category, p.selling_price, p.cost_price, pm.units_sold, pm.revenue, pm.profit, pm.margin_pct, pm.return_rate, pm.pareto_class
                FROM products p
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_metrics pm ON p.id = pm.product_id
                ORDER BY pm.revenue DESC
            ");
            foreach ($rows as $r) {
                fputcsv($output, $r);
            }
        }

        fclose($output);
        exit;
    }
}

