<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    /**
     * Display the dashboard.
     */
    public function index(Request $request): View
    {
        $range = $request->query('range', 'this_month');

        $recentOrders = $this->dashboardService->getRecentOrders(5);
        $statusBreakdownUI = $this->dashboardService->getStatusBreakdownUI();

        if (auth()->user()?->isAdmin()) {
            $summary = $this->dashboardService->getSummary();
            $revenueTrend = $this->dashboardService->getRevenueTrend();
            
            return view(isMobile() ? 'dashboard.mobile.admin' : 'dashboard.admin', compact(
                'summary', 
                'recentOrders',
                'statusBreakdownUI',
                'revenueTrend'
            ));
        }

        $ownerSummary = $this->dashboardService->getOwnerSummary($range);
        $topClients = $this->dashboardService->getTopClients(5, $range);
        $recentBatches = $this->dashboardService->getRecentCompletedBatches(5);
        $productMix = $this->dashboardService->getProductMix($range);
        $revenueTrend = $this->dashboardService->getRevenueTrend();

        // Get label for UI
        [,, $rangeLabel] = $this->dashboardService->getRangeDates($range);

        return view(isMobile() ? 'dashboard.mobile.owner' : 'dashboard.owner', compact(
            'ownerSummary', 
            'topClients', 
            'recentBatches',
            'productMix',
            'revenueTrend',
            'rangeLabel',
            'range'
        ));
    }

    /**
     * Export the Owner Dashboard report as PDF.
     */
    public function exportOwnerReport(Request $request)
    {
        if (!auth()->user()?->isOwner()) {
            abort(403, 'Unauthorized action.');
        }

        $range = $request->query('range', '30_days');
        
        $ownerSummary = $this->dashboardService->getOwnerSummary($range);
        $topClients = $this->dashboardService->getTopClients(5, $range);
        $productMix = $this->dashboardService->getProductMix($range);
        [,, $rangeLabel] = $this->dashboardService->getRangeDates($range);
        $generatedAt = now()->translatedFormat('l, d F Y H:i');

        $pdf = Pdf::loadView('dashboard.export-pdf', compact(
            'ownerSummary', 
            'topClients', 
            'productMix', 
            'rangeLabel', 
            'generatedAt'
        ));

        // Format filename: Laporan-Eksekutif-StitchFlow-30_days-2026-07-27.pdf
        $filename = 'Laporan-Eksekutif-StitchFlow-' . $range . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
