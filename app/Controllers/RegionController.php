<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\RegionalAnalyticsService;

class RegionController extends Controller
{
    protected RegionalAnalyticsService $regionalService;

    public function __construct()
    {
        parent::__construct();
        $this->regionalService = new RegionalAnalyticsService();
    }

    public function index(Request $request): void
    {
        $data = $this->regionalService->getRegionalAnalytics();
        $data['pageTitle'] = 'Regional Performance & Territory Matrix';
        $data['activeMenu'] = 'regions';

        $this->render('regions.index', $data);
    }
}

