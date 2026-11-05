<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\AlertService;

class AlertController extends Controller
{
    protected AlertService $alertService;

    public function __construct()
    {
        parent::__construct();
        $this->alertService = new AlertService();
    }

    public function index(Request $request): void
    {
        $status = $request->get('status');
        $alerts = $this->alertService->getAllAlerts($status);
        $counts = $this->alertService->getActiveCounts();

        $data = [
            'pageTitle' => 'Decision Support: Alerts Center',
            'activeMenu' => 'alerts',
            'alerts' => $alerts,
            'counts' => $counts,
            'currentFilter' => $status
        ];

        $this->render('alerts.index', $data);
    }

    public function updateStatus(Request $request, string $id): void
    {
        if (!$this->validateCsrf($request)) {
            return;
        }

        $newStatus = (string) $request->post('status', 'DISMISSED');
        $success = $this->alertService->updateStatus((int) $id, $newStatus);

        $this->logAudit('ALERT_STATUS_UPDATE', 'alerts', (int) $id, ['new_status' => $newStatus]);

        if ($request->isJson()) {
            $this->json(['success' => $success]);
        } else {
            \App\Helpers\Session::flash('success', "Alert status updated to {$newStatus}.");
            \App\Helpers\Response::redirect('/alerts');
        }
    }
}

