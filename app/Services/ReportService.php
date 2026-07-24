<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ReportService
{
    /**
     * Get orders filtered by a predefined period keyword
     * or by a custom date range.
     *
     * @param  string  $period  'daily' | 'weekly' | 'monthly' | 'yearly'
     * @return Collection<int, Order>
     */
    public function getOrdersByPeriod(
        string $period = 'monthly',
        ?Carbon $from = null,
        ?Carbon $to = null,
    ): Collection {
        $query = Order::with(['creator:id,name', 'invoice']);

        if ($from && $to) {
            $query->whereBetween('order_date', [
                $from->startOfDay(),
                $to->endOfDay(),
            ]);
        } else {
            $query->where(function ($q) use ($period): void {
                match ($period) {
                    'daily'   => $q->whereDate('order_date', today()),
                    'weekly'  => $q->whereBetween('order_date', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ]),
                    'monthly' => $q->whereMonth('order_date', now()->month)
                                   ->whereYear('order_date', now()->year),
                    'yearly'  => $q->whereYear('order_date', now()->year),
                    default   => $q->whereMonth('order_date', now()->month)
                                   ->whereYear('order_date', now()->year),
                };
            });
        }

        return $query->orderByDesc('order_date')->get();
    }

    /**
     * Get revenue summary grouped by month for the current year.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function getMonthlyRevenue(): \Illuminate\Support\Collection
    {
        return Invoice::selectRaw(
            'MONTH(created_at) as month,
             YEAR(created_at) as year,
             SUM(grand_total) as total_revenue,
             COUNT(*) as total_invoices'
        )
            ->whereYear('created_at', now()->year)
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) ASC, MONTH(created_at) ASC')
            ->get();
    }

    /**
     * Get order count breakdown by production status for a given period.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function getStatusBreakdown(string $period = 'monthly'): \Illuminate\Support\Collection
    {
        $query = Order::selectRaw('current_status, COUNT(*) as total');

        match ($period) {
            'daily'  => $query->whereDate('order_date', today()),
            'weekly' => $query->whereBetween('order_date', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]),
            'yearly' => $query->whereYear('order_date', now()->year),
            default  => $query->whereMonth('order_date', now()->month)
                               ->whereYear('order_date', now()->year),
        };

        return $query->groupBy('current_status')->get();
    }
}
