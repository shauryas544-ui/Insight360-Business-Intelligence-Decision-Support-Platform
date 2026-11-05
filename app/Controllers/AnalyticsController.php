<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Helpers\Database;
use App\Services\PythonBridgeService;

class AnalyticsController extends Controller
{
    public function index(): void
    {
        $forecasts = Database::fetchAll("SELECT * FROM forecast_results ORDER BY metric_name, horizon_days ASC");
        $anomalies = Database::fetchAll("SELECT * FROM anomalies ORDER BY anomaly_date DESC");

        $data = [
            'pageTitle' => 'Advanced Analytics & Predictive Engine',
            'activeMenu' => 'advanced_analytics',
            'forecasts' => $forecasts,
            'anomalies' => $anomalies,
        ];

        $this->render('analytics.index', $data);
    }

    public function triggerRun(): void
    {
        $bridge = new PythonBridgeService();
        $res = $bridge->runAllAnalytics();
        $this->logAudit('ANALYTICS_JOB_TRIGGER', 'analytics_engine', null, $res);
        $this->json($res);
    }
}

