<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\DashboardService;
use App\Services\PythonBridgeService;
use App\Helpers\Database;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct()
    {
        parent::__construct();
        $this->dashboardService = new DashboardService();
    }

    public function index(Request $request): void
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $data = $this->dashboardService->getExecutiveSummary($startDate, $endDate);
        $data['pageTitle'] = 'Executive Dashboard';
        $data['activeMenu'] = 'dashboard';
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;

        $this->render('dashboard.executive', $data);
    }

    public function analyst(Request $request): void
    {
        $data = $this->dashboardService->getExecutiveSummary();
        $data['pageTitle'] = 'Analyst Exploration Workspace';
        $data['activeMenu'] = 'analyst';

        // Additional detailed cuts for analysts
        $data['categories'] = Database::fetchAll("SELECT * FROM categories ORDER BY name ASC");
        $data['regions'] = Database::fetchAll("SELECT * FROM regions ORDER BY name ASC");

        $this->render('dashboard.analyst', $data);
    }

    public function runAnalytics(): void
    {
        $bridge = new PythonBridgeService();
        $res = $bridge->runAllAnalytics();
        $this->logAudit('ANALYTICS_MANUAL_RUN', 'analytics_engine', null, $res);
        $this->json($res);
    }
}

