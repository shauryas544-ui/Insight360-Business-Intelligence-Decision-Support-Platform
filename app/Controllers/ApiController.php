<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Helpers\Database;
use App\Services\DashboardService;
use App\Services\SalesAnalyticsService;
use App\Services\CustomerAnalyticsService;
use App\Services\ProductAnalyticsService;
use App\Services\RegionalAnalyticsService;
use App\Services\AlertService;
use App\Services\RecommendationService;
use App\Services\PythonBridgeService;

class ApiController extends Controller
{
    public function dashboardSummary(Request $request): void
    {
        $dash = new DashboardService();
        $this->json($dash->getExecutiveSummary($request->get('start_date'), $request->get('end_date')));
    }

    public function dashboardTrends(): void
    {
        $trends = Database::fetchAll("SELECT * FROM monthly_metrics ORDER BY year_month ASC");
        $this->json($trends);
    }

    public function sales(Request $request): void
    {
        $sales = new SalesAnalyticsService();
        $this->json($sales->getSalesAnalytics($request->get('start_date'), $request->get('end_date')));
    }

    public function customers(): void
    {
        $cust = new CustomerAnalyticsService();
        $this->json($cust->getCustomerAnalytics());
    }

    public function products(): void
    {
        $prod = new ProductAnalyticsService();
        $this->json($prod->getProductAnalytics());
    }

    public function regions(): void
    {
        $reg = new RegionalAnalyticsService();
        $this->json($reg->getRegionalAnalytics());
    }

    public function rfm(): void
    {
        $segments = Database::fetchAll("SELECT * FROM customer_segments ORDER BY monetary DESC");
        $this->json($segments);
    }

    public function pareto(): void
    {
        $metrics = Database::fetchAll("
            SELECT pm.*, p.product_name, p.sku
            FROM product_metrics pm
            JOIN products p ON pm.product_id = p.id
            ORDER BY pm.revenue DESC
        ");
        $this->json($metrics);
    }

    public function anomalies(): void
    {
        $anomalies = Database::fetchAll("SELECT * FROM anomalies ORDER BY anomaly_date DESC");
        $this->json($anomalies);
    }

    public function forecast(): void
    {
        $forecasts = Database::fetchAll("SELECT * FROM forecast_results ORDER BY metric_name, horizon_days ASC");
        $this->json($forecasts);
    }

    public function alerts(): void
    {
        $alertService = new AlertService();
        $this->json($alertService->getAllAlerts());
    }

    public function recommendations(): void
    {
        $recService = new RecommendationService();
        $this->json($recService->getAllRecommendations());
    }

    public function systemHealth(): void
    {
        $dbStatus = 'HEALTHY';
        try {
            Database::getConnection();
        } catch (\Throwable $e) {
            $dbStatus = 'FAILED: ' . $e->getMessage();
        }

        $bridge = new PythonBridgeService();
        $pyCheck = $bridge->runCommand('--help');
        $pythonStatus = $pyCheck['success'] ? 'HEALTHY' : 'UNREACHABLE';

        $totalOrders = (int) Database::fetchColumn("SELECT COUNT(*) FROM orders");
        $lastImport = Database::fetchOne("SELECT * FROM data_imports ORDER BY id DESC LIMIT 1");

        $this->json([
            'database' => ['driver' => Database::getDriver(), 'status' => $dbStatus],
            'php_api' => ['status' => 'HEALTHY', 'version' => PHP_VERSION],
            'python_engine' => ['status' => $pythonStatus],
            'total_orders' => $totalOrders,
            'last_import' => $lastImport,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

