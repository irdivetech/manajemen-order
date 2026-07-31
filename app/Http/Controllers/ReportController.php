<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    /**
     * Display report page.
     */
    public function index(Request $request): View
    {
        $period = $request->query('period', 'monthly');

        $from = $request->query('from') ? Carbon::parse($request->query('from')) : null;
        $to = $request->query('to') ? Carbon::parse($request->query('to')) : null;

        $orders = $this->reportService->getOrdersByPeriod($period, $from, $to);
        
        $totalRevenue = (float) $orders->filter(function ($order) {
            return $order->invoice !== null && $order->invoice->isPaid();
        })->sum(function ($order) {
            return (float) $order->invoice->grand_total;
        });

        $statusBreakdown = $this->reportService->getStatusBreakdown($period);

        return view(isMobile() ? 'reports.mobile.index' : 'reports.index', compact('orders', 'totalRevenue', 'statusBreakdown', 'period'));
    }

    /**
     * Export reports to formatted XLSX (Excel).
     */
    public function export(Request $request)
    {
        $period   = $request->query('period', 'monthly');
        $from     = $request->query('from') ? Carbon::parse($request->query('from')) : null;
        $to       = $request->query('to') ? Carbon::parse($request->query('to')) : null;

        $orders   = $this->reportService->getOrdersByPeriod($period, $from, $to);
        $tempPath = $this->reportService->exportXlsx($orders, $period);

        $filename = "laporan_{$period}_" . now()->format('Ymd_His') . ".xlsx";

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
