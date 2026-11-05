<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\SalesAnalyticsService;
use App\Services\PythonBridgeService;

class SalesController extends Controller
{
    protected SalesAnalyticsService $salesService;

    public function __construct()
    {
        parent::__construct();
        $this->salesService = new SalesAnalyticsService();
    }

    public function index(Request $request): void
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $data = $this->salesService->getSalesAnalytics($startDate, $endDate);
        $data['pageTitle'] = 'Sales & Revenue Analytics';
        $data['activeMenu'] = 'sales';

        $this->render('sales.index', $data);
    }

    public function whyChange(Request $request): void
    {
        $p1 = $request->get('period1') ?: $request->post('period1');
        $p2 = $request->get('period2') ?: $request->post('period2');

        $bridge = new PythonBridgeService();
        $result = $bridge->whyChange($p1, $p2);

        $this->json($result);
    }
}

