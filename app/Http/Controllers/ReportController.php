<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    /**
     * Display various reports based on given period.
     */
    public function index(Request $request): JsonResponse
    {
        $period = $request->query('period', 'monthly');
        
        $from = $request->query('from') ? Carbon::parse($request->query('from')) : null;
        $to = $request->query('to') ? Carbon::parse($request->query('to')) : null;

        return response()->json([
            'orders' => $this->reportService->getOrdersByPeriod($period, $from, $to),
            'monthly_revenue' => $this->reportService->getMonthlyRevenue(),
            'status_breakdown' => $this->reportService->getStatusBreakdown($period),
        ]);
    }
}
