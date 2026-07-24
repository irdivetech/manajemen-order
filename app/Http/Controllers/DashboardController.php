<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    /**
     * Display the dashboard summary and recent data.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'summary' => $this->dashboardService->getSummary(),
            'recent_orders' => $this->dashboardService->getRecentOrders(),
            'nearing_deadline' => $this->dashboardService->getOrdersNearingDeadline(),
        ]);
    }
}
