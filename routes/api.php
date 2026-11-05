<?php

use App\Helpers\Router;
use App\Controllers\ApiController;

Router::group(['prefix' => 'api/v1'], function () {
    Router::get('/dashboard/summary', [ApiController::class, 'dashboardSummary']);
    Router::get('/dashboard/trends', [ApiController::class, 'dashboardTrends']);

    Router::get('/sales', [ApiController::class, 'sales']);
    Router::get('/customers', [ApiController::class, 'customers']);
    Router::get('/products', [ApiController::class, 'products']);
    Router::get('/regions', [ApiController::class, 'regions']);

    Router::get('/analytics/rfm', [ApiController::class, 'rfm']);
    Router::get('/analytics/pareto', [ApiController::class, 'pareto']);
    Router::get('/analytics/anomalies', [ApiController::class, 'anomalies']);
    Router::get('/analytics/forecast', [ApiController::class, 'forecast']);

    Router::get('/alerts', [ApiController::class, 'alerts']);
    Router::get('/recommendations', [ApiController::class, 'recommendations']);

    Router::get('/system/health', [ApiController::class, 'systemHealth']);
});

