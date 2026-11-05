<?php

use App\Helpers\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\SalesController;
use App\Controllers\CustomerController;
use App\Controllers\ProductController;
use App\Controllers\RegionController;
use App\Controllers\MarketingController;
use App\Controllers\OperationsController;
use App\Controllers\AnalyticsController;
use App\Controllers\AlertController;
use App\Controllers\RecommendationController;
use App\Controllers\ImportController;
use App\Controllers\ReportController;
use App\Controllers\UserController;

// Guest Auth Routes
Router::get('/', [AuthController::class, 'showLogin']);
Router::get('/login', [AuthController::class, 'showLogin']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);

// Authenticated Application Routes
Router::group(['middleware' => 'auth'], function () {

    // Dashboards
    Router::get('/dashboard', [DashboardController::class, 'index']);
    Router::get('/analyst', [DashboardController::class, 'analyst']);
    Router::post('/dashboard/run-analytics', [DashboardController::class, 'runAnalytics']);

    // Analytics Modules
    Router::get('/sales', [SalesController::class, 'index']);
    Router::get('/sales/why-change', [SalesController::class, 'whyChange']);
    Router::post('/sales/why-change', [SalesController::class, 'whyChange']);

    Router::get('/customers', [CustomerController::class, 'index']);
    Router::get('/products', [ProductController::class, 'index']);
    Router::get('/regions', [RegionController::class, 'index']);
    Router::get('/marketing', [MarketingController::class, 'index']);
    Router::get('/operations', [OperationsController::class, 'index']);

    Router::get('/analytics', [AnalyticsController::class, 'index']);
    Router::post('/analytics/run', [AnalyticsController::class, 'triggerRun']);

    // Decision Support Layer
    Router::get('/alerts', [AlertController::class, 'index']);
    Router::post('/alerts/{id}/status', [AlertController::class, 'updateStatus']);

    Router::get('/recommendations', [RecommendationController::class, 'index']);
    Router::post('/recommendations/{id}/status', [RecommendationController::class, 'updateStatus']);

    // Reports
    Router::get('/reports', [ReportController::class, 'index']);
    Router::get('/reports/printable', [ReportController::class, 'printable']);
    Router::get('/reports/export', [ReportController::class, 'export']);

    // Data Imports (Admin & Analyst)
    Router::group(['middleware' => 'role:admin,analyst'], function () {
        Router::get('/import', [ImportController::class, 'index']);
        Router::post('/import/upload', [ImportController::class, 'upload']);
        Router::get('/import/template', [ImportController::class, 'sampleTemplate']);
        Router::get('/import/{id}', [ImportController::class, 'show']);
    });

    // Administration & Security (Admin only)
    Router::group(['middleware' => 'role:admin'], function () {
        Router::get('/settings', [UserController::class, 'index']);
        Router::post('/settings/users', [UserController::class, 'createUser']);
    });
});

