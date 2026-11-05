<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Helpers\Response;
use App\Helpers\Session;
use App\Services\ImportService;

class ImportController extends Controller
{
    protected ImportService $importService;

    public function __construct()
    {
        parent::__construct();
        $this->importService = new ImportService();
    }

    public function index(): void
    {
        $imports = $this->importService->getImportHistory();

        $data = [
            'pageTitle' => 'Data Import & Data Quality Engine',
            'activeMenu' => 'import',
            'imports' => $imports
        ];

        $this->render('import.index', $data);
    }

    public function upload(Request $request): void
    {
        if (!$this->validateCsrf($request)) {
            return;
        }

        $file = $request->file('dataset_file');
        if (!$file || empty($file['tmp_name'])) {
            Session::flash('error', 'Please choose a valid CSV file to upload.');
            Response::redirect('/import');
            return;
        }

        $userId = $this->user['id'] ?? null;
        $res = $this->importService->handleUpload($file, $userId);

        $this->logAudit('DATASET_UPLOAD', 'data_imports', $res['import_id'] ?? null, $res);

        if ($res['success']) {
            Session::flash('success', "Dataset uploaded and processed successfully. Quality score computed.");
        } else {
            Session::flash('error', "Import error: " . ($res['message'] ?? 'Validation failure.'));
        }

        Response::redirect('/import/' . ($res['import_id'] ?? ''));
    }

    public function show(Request $request, string $id): void
    {
        $import = $this->importService->getImportDetails((int) $id);
        if (!$import) {
            Session::flash('error', 'Import job record not found.');
            Response::redirect('/import');
            return;
        }

        $data = [
            'pageTitle' => 'Data Quality Report #' . $id,
            'activeMenu' => 'import',
            'import' => $import
        ];

        $this->render('import.show', $data);
    }

    public function sampleTemplate(): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=insightflow_sample_import_template.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['order_number', 'order_date', 'customer_code', 'sku', 'quantity', 'unit_price', 'discount', 'channel']);
        fputcsv($out, ['ORD-99001', '2026-11-01', 'CUST-1005', 'SKU-CLD-01', '2', '2400.00', '0.00', 'Direct Sales']);
        fputcsv($out, ['ORD-99002', '2026-11-02', 'CUST-1012', 'SKU-AIM-01', '1', '4800.00', '200.00', 'Partner Portal']);
        fputcsv($out, ['ORD-99003', '2026-11-03', 'CUST-1045', 'SKU-SEC-01', '5', '1600.00', '100.00', 'Online Store']);
        fclose($out);
        exit;
    }
}

