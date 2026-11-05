<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\CustomerAnalyticsService;

class CustomerController extends Controller
{
    protected CustomerAnalyticsService $customerService;

    public function __construct()
    {
        parent::__construct();
        $this->customerService = new CustomerAnalyticsService();
    }

    public function index(Request $request): void
    {
        $data = $this->customerService->getCustomerAnalytics();
        $data['pageTitle'] = 'Customer Analytics & RFM Segmentation';
        $data['activeMenu'] = 'customers';

        $this->render('customers.index', $data);
    }
}

