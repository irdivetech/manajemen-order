<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderSizeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BestSellerController extends Controller
{
    /**
     * Display best seller analytics page.
     */
    public function index(Request $request): View
    {
        [$from, $to, $periodLabel] = $this->getDateRange($request);

        $topClothingCategories = $this->getTopClothingCategories($from, $to);
        $topColors = $this->getTopColors($from, $to);
        $topMaterials = $this->getTopMaterials($from, $to);

        // Summary counts
        $summary = [
            'total_categories' => $topClothingCategories->count(),
            'total_colors'     => $topColors->count(),
            'total_materials'  => $topMaterials->count(),
        ];

        $period = $request->query('period', 'this_month');

        return view('best-sellers.index', compact(
            'topClothingCategories',
            'topColors',
            'topMaterials',
            'summary',
            'periodLabel',
            'period'
        ));
    }

    /**
     * Get top clothing categories ranked by total quantity.
     */
    private function getTopClothingCategories($from, $to)
    {
        return Order::query()
            ->join('master_clothing_categories', 'orders.clothing_category_id', '=', 'master_clothing_categories.id')
            ->join('order_size_details', 'orders.id', '=', 'order_size_details.order_id')
            ->when($from && $to, fn ($q) => $q->whereBetween('orders.order_date', [$from, $to]))
            ->whereNull('orders.archived_at')
            ->whereNotNull('orders.clothing_category_id')
            ->select(
                'master_clothing_categories.name',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(order_size_details.quantity) as total_quantity')
            )
            ->groupBy('master_clothing_categories.id', 'master_clothing_categories.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
    }

    /**
     * Get top colors ranked by total quantity.
     */
    private function getTopColors($from, $to)
    {
        return OrderSizeDetail::query()
            ->join('orders', 'order_size_details.order_id', '=', 'orders.id')
            ->when($from && $to, fn ($q) => $q->whereBetween('orders.order_date', [$from, $to]))
            ->whereNull('orders.archived_at')
            ->whereNotNull('order_size_details.color')
            ->where('order_size_details.color', '!=', '')
            ->select(
                'order_size_details.color',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(order_size_details.quantity) as total_quantity')
            )
            ->groupBy('order_size_details.color')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
    }

    /**
     * Get top materials ranked by total quantity.
     */
    private function getTopMaterials($from, $to)
    {
        return Order::query()
            ->join('master_materials', 'orders.material_id', '=', 'master_materials.id')
            ->join('order_size_details', 'orders.id', '=', 'order_size_details.order_id')
            ->when($from && $to, fn ($q) => $q->whereBetween('orders.order_date', [$from, $to]))
            ->whereNull('orders.archived_at')
            ->whereNotNull('orders.material_id')
            ->select(
                'master_materials.name',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(order_size_details.quantity) as total_quantity')
            )
            ->groupBy('master_materials.id', 'master_materials.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
    }

    /**
     * Parse the selected period into a date range.
     *
     * @return array{0: string|null, 1: string|null, 2: string}
     */
    private function getDateRange(Request $request): array
    {
        $period = $request->query('period', 'this_month');

        return match ($period) {
            'this_month'  => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString(), 'Bulan Ini'],
            'last_month'  => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString(), 'Bulan Lalu'],
            '3_months'    => [now()->subMonths(3)->startOfMonth()->toDateString(), now()->toDateString(), '3 Bulan Terakhir'],
            '6_months'    => [now()->subMonths(6)->startOfMonth()->toDateString(), now()->toDateString(), '6 Bulan Terakhir'],
            '1_year'      => [now()->subYear()->startOfMonth()->toDateString(), now()->toDateString(), '1 Tahun Terakhir'],
            'custom'      => [$request->query('from'), $request->query('to'), 'Kustom'],
            default       => [null, null, 'Semua Waktu'], // 'all'
        };
    }
}
