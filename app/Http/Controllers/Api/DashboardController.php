<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;
    public function __construct(DashboardService $dashboardService) {
        $this->dashboardService = $dashboardService;
    }

    public function analytics()
    {
        $data = $this->dashboardService->getAnalytics();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
