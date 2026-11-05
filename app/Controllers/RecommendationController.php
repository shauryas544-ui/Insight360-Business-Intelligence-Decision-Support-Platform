<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\RecommendationService;

class RecommendationController extends Controller
{
    protected RecommendationService $recService;

    public function __construct()
    {
        parent::__construct();
        $this->recService = new RecommendationService();
    }

    public function index(Request $request): void
    {
        $status = $request->get('status');
        $recommendations = $this->recService->getAllRecommendations($status);

        $data = [
            'pageTitle' => 'Decision Support: Evidence-based Recommendations',
            'activeMenu' => 'recommendations',
            'recommendations' => $recommendations,
            'currentFilter' => $status
        ];

        $this->render('recommendations.index', $data);
    }

    public function updateStatus(Request $request, string $id): void
    {
        if (!$this->validateCsrf($request)) {
            return;
        }

        $newStatus = (string) $request->post('status', 'COMPLETED');
        $success = $this->recService->updateStatus((int) $id, $newStatus);

        $this->logAudit('RECOMMENDATION_STATUS_UPDATE', 'recommendations', (int) $id, ['new_status' => $newStatus]);

        if ($request->isJson()) {
            $this->json(['success' => $success]);
        } else {
            \App\Helpers\Session::flash('success', "Recommendation status updated to {$newStatus}.");
            \App\Helpers\Response::redirect('/recommendations');
        }
    }
}

