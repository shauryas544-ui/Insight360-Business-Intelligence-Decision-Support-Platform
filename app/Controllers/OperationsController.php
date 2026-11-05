<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\OperationsAnalyticsService;

class OperationsController extends Controller
{
    protected OperationsAnalyticsService $operationsService;

    public function __construct()
    {
        parent::__construct();
        $this->operationsService = new OperationsAnalyticsService();
    }

    public function index(Request $request): void
    {
        $data = $this->operationsService->getOperationsAnalytics();
        $data['pageTitle'] = 'Operations, Returns & Inventory Health';
        $data['activeMenu'] = 'operations';

        $this->render('operations.index', $data);
    }
}

