<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\MarketingAnalyticsService;

class MarketingController extends Controller
{
    protected MarketingAnalyticsService $marketingService;

    public function __construct()
    {
        parent::__construct();
        $this->marketingService = new MarketingAnalyticsService();
    }

    public function index(Request $request): void
    {
        $data = $this->marketingService->getMarketingAnalytics();
        $data['pageTitle'] = 'Marketing Campaigns & CAC / ROI Analytics';
        $data['activeMenu'] = 'marketing';

        $this->render('marketing.index', $data);
    }
}

