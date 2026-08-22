<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MasterMaterial;
use App\Services\ReportService;
use App\Services\HppService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class MaterialPurchaseController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
        private readonly HppService $hppService
    ) {}

    /**
     * Display the material purchasing view.
     */
    public function index(Request $request): View
    {
        $period = $request->query('period', 'monthly');
        $from = $request->query('from') ? Carbon::parse($request->query('from')) : null;
        $to = $request->query('to') ? Carbon::parse($request->query('to')) : null;
        $dateColumn = $request->query('date_column', 'order_date');

        // 1. Get Summary grouped by Material
        $summary = $this->reportService->getMaterialPurchasingSummary($period, $from, $to, $dateColumn);

        // 2. Get detailed unpurchased orders
        $unpurchasedOrders = $this->reportService->getUnpurchasedOrders($period, $from, $to, $dateColumn);
        
        // Group orders by material_id for easy display in the view
        $ordersByMaterial = $unpurchasedOrders->groupBy('material_id');

        // Calculate total estimated cash needed from summary
        $totalEstimatedCash = $summary->sum('total_estimated_cost');

        // Calculate total variances for the view to know if there's any price hike
        $totalVariance = 0;
        foreach ($unpurchasedOrders as $order) {
            if ($order->masterMaterial) {
                // For old orders, material_price_snapshot might be null.
                // It's safer to compare the New HPP (using current price) vs the saved total_cost.
                $usageForThisOrder = 0;
                foreach ($order->sizeDetails as $detail) {
                    $est = \App\Models\MaterialUsageEstimate::where('material_id', $order->material_id)
                        ->where('clothing_category_id', $order->clothing_category_id)
                        ->where('size_id', $detail->size_id)
                        ->first();
                    if ($est) {
                        $usageForThisOrder += ($est->estimated_usage * $detail->quantity);
                    }
                }

                $newEstimatedCost = $usageForThisOrder * $order->masterMaterial->price_per_unit;
                // Add a small tolerance for floating point rounding issues
                if ($newEstimatedCost > ($order->total_cost + 1)) {
                    $totalVariance++;
                }
            }
        }

        return view(isMobile() ? 'material-purchases.mobile.index' : 'material-purchases.index', compact(
            'summary',
            'ordersByMaterial',
            'totalEstimatedCash',
            'period',
            'dateColumn',
            'totalVariance'
        ));
    }

    /**
     * Display the printable material purchasing view.
     */
    public function print(Request $request): View
    {
        $period = $request->query('period', 'monthly');
        $from = $request->query('from') ? Carbon::parse($request->query('from')) : null;
        $to = $request->query('to') ? Carbon::parse($request->query('to')) : null;
        $dateColumn = $request->query('date_column', 'order_date');

        $summary = $this->reportService->getMaterialPurchasingSummary($period, $from, $to, $dateColumn);
        $unpurchasedOrders = $this->reportService->getUnpurchasedOrders($period, $from, $to, $dateColumn);
        $ordersByMaterial = $unpurchasedOrders->groupBy('material_id');
        $totalEstimatedCash = $summary->sum('total_estimated_cost');

        return view('material-purchases.print', compact(
            'summary',
            'ordersByMaterial',
            'totalEstimatedCash',
            'period',
            'from',
            'to'
        ));
    }

    /**
     * Mark material for an order as purchased.
     */
    public function markAsPurchased(Order $order)
    {
        if ($order->is_material_purchased) {
            return back()->with('error', 'Bahan untuk pesanan ini sudah ditandai sebagai dibeli.');
        }

        $order->update(['is_material_purchased' => true]);

        return back()->with('success', "Bahan untuk pesanan {$order->order_number} berhasil ditandai sudah dibeli.");
    }

    /**
     * Sync order's HPP to the current market price of the material.
     */
    public function syncHpp(Order $order)
    {
        if (!$order->masterMaterial || !$order->clothingCategory) {
            return back()->with('error', 'Pesanan tidak memiliki data bahan atau kategori pakaian yang valid.');
        }

        $currentPrice = $order->masterMaterial->price_per_unit;
        
        // Re-calculate the estimated HPP
        // To do this, we can call HppService. However, HppService->calculateEstimatedHpp takes size_details array.
        // We can just construct it from the order's size details.
        
        $sizeDetailsArray = $order->sizeDetails->map(function ($detail) {
            return [
                'size_id' => $detail->size_id,
                'quantity' => $detail->quantity,
            ];
        })->toArray();

        $newHpp = $this->hppService->calculateEstimatedHpp(
            $order->material_id,
            $order->clothing_category_id,
            $sizeDetailsArray
        );

        $order->update([
            'total_cost' => $newHpp,
            'material_price_snapshot' => $currentPrice
        ]);

        return back()->with('success', "HPP untuk pesanan {$order->order_number} berhasil disinkronisasi dengan harga bahan saat ini.");
    }
}
