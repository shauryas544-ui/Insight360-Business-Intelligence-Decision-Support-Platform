<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\ReportService;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct()
    {
        parent::__construct();
        $this->reportService = new ReportService();
    }

    public function index(): void
    {
        $reportData = $this->reportService->getMonthlyExecutiveReport();
        $reportData['pageTitle'] = 'Executive Intelligence & Decision Reports';
        $reportData['activeMenu'] = 'reports';

        $this->render('reports.index', $reportData);
    }

    public function printable(): void
    {
        $reportData = $this->reportService->getMonthlyExecutiveReport();
        $reportData['pageTitle'] = 'Monthly Executive Business Intelligence Report';
        $reportData['noLayout'] = true;

        $this->render('reports.printable', $reportData);
    }

    public function export(Request $request): void
    {
        $type = $request->get('type', 'sales');
        $this->reportService->exportCsv($type);
    }
}

