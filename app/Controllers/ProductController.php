<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\ProductAnalyticsService;

class ProductController extends Controller
{
    protected ProductAnalyticsService $productService;

    public function __construct()
    {
        parent::__construct();
        $this->productService = new ProductAnalyticsService();
    }

    public function index(Request $request): void
    {
        $data = $this->productService->getProductAnalytics();
        $data['pageTitle'] = 'Product Performance & Pareto Analysis';
        $data['activeMenu'] = 'products';

        $this->render('products.index', $data);
    }
}

