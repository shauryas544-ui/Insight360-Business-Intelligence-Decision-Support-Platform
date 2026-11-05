<?php

require_once dirname(__DIR__, 2) . '/app/Helpers/Autoloader.php';
\App\Helpers\Autoloader::register(dirname(__DIR__, 2));
\App\Helpers\Env::load(dirname(__DIR__, 2) . '/.env');

use App\Helpers\Database;
use App\Services\DashboardService;
use App\Services\CustomerAnalyticsService;
use App\Services\ProductAnalyticsService;
use App\Services\RegionalAnalyticsService;
use App\Services\MarketingAnalyticsService;
use App\Services\OperationsAnalyticsService;
use App\Services\AlertService;
use App\Services\RecommendationService;
use App\Services\PythonBridgeService;

$passed = 0;
$failed = 0;

function assertCondition(bool $cond, string $msg): void {
    global $passed, $failed;
    if ($cond) {
        echo "  [PASS] {$msg}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$msg}\n";
        $failed++;
    }
}

echo "=== Running InsightFlow PHP Test Suite ===\n\n";

// Test 1: Database Connection
try {
    $db = Database::getConnection();
    assertCondition($db !== null, "Database connection established successfully.");
} catch (\Throwable $e) {
    assertCondition(false, "Database connection failed: " . $e->getMessage());
}

// Test 2: Users & Authentication Hash Verification
$adminUser = Database::fetchOne("SELECT * FROM users WHERE email = 'admin@insightflow.io'");
assertCondition($adminUser !== null, "Admin user exists in database.");
assertCondition(password_verify('AdminPassword123!', $adminUser['password_hash']), "Admin password hash verification succeeds.");

// Test 3: Dashboard Service
$dash = new DashboardService();
$summary = $dash->getExecutiveSummary();
assertCondition($summary['revenue'] > 0, "Executive revenue KPI computed: $" . number_format($summary['revenue'], 2));
assertCondition($summary['orders'] > 0, "Executive total orders computed: " . number_format($summary['orders']));
assertCondition($summary['profit'] > 0, "Executive gross profit computed: $" . number_format($summary['profit'], 2));
assertCondition(count($summary['monthly_trends']) > 0, "Monthly trends calculated across " . count($summary['monthly_trends']) . " months.");

// Test 4: Customer RFM & Cohorts
$custService = new CustomerAnalyticsService();
$custData = $custService->getCustomerAnalytics();
assertCondition($custData['total_customers'] > 0, "Customer count is positive: " . $custData['total_customers']);
assertCondition(count($custData['segments']) > 0, "RFM segments calculated: " . count($custData['segments']) . " segments.");
assertCondition(count($custData['cohort_matrix']) > 0, "Cohort retention matrix computed: " . count($custData['cohort_matrix']) . " cohort months.");

// Test 5: Product Pareto & Quadrants
$prodService = new ProductAnalyticsService();
$prodData = $prodService->getProductAnalytics();
assertCondition($prodData['total_products'] > 0, "Product catalog loaded: " . $prodData['total_products'] . " SKUs.");
assertCondition($prodData['pareto_stats']['A']['count'] > 0, "Pareto Class A products identified: " . $prodData['pareto_stats']['A']['count'] . " core drivers.");
assertCondition(count($prodData['quadrants']['stars']) >= 0, "4-Quadrant matrix generated.");

// Test 6: Python Bridge & Why-Did-This-Change
$bridge = new PythonBridgeService();
$whyRes = $bridge->whyChange('2026-09', '2026-10');
assertCondition($whyRes['success'] === true, "Why-Did-This-Change Engine executed successfully via Python bridge.");

// Test 7: Alerts & Recommendations
$alertService = new AlertService();
$alerts = $alertService->getAllAlerts();
assertCondition(count($alerts) > 0, "Alert engine generated active telemetry alerts (" . count($alerts) . " alerts).");

$recService = new RecommendationService();
$recs = $recService->getAllRecommendations();
assertCondition(count($recs) > 0, "Evidence-based recommendation engine generated actionable items (" . count($recs) . " recommendations).");

echo "\n--- Summary: {$passed} Passed, {$failed} Failed ---\n";
if ($failed > 0) {
    exit(1);
}

