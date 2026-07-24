<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;

class DashboardService
{
    /**
     * Get the overall summary statistics for the dashboard.
     *
     * @return array<string, mixed>
     */
    public function getSummary(): array
    {
        $totalOrders  = Order::count();
        $activeOrders = Order::active()->count();
        $archived     = Order::archived()->count();

        $statusBreakdown = [];
        foreach (Order::STATUSES as $status) {
            $statusBreakdown[$status] = Order::byStatus($status)->count();
        }

        $totalRevenue = Invoice::where('payment_status', Invoice::PAYMENT_PAID)
            ->sum('grand_total');

        $pendingPayments = Invoice::where('payment_status', Invoice::PAYMENT_UNPAID)
            ->sum('grand_total');

        return [
            'total_orders'       => $totalOrders,
            'active_orders'      => $activeOrders,
            'archived_orders'    => $archived,
            'status_breakdown'   => $statusBreakdown,
            'total_revenue'      => (float) $totalRevenue,
            'pending_payments'   => (float) $pendingPayments,
        ];
    }

    /**
     * Get recent orders for the dashboard feed.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Order>
     */
    public function getRecentOrders(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Order::with(['creator:id,name', 'invoice:id,order_id,payment_status,grand_total'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get orders nearing their deadline (within N days).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Order>
     */
    public function getOrdersNearingDeadline(int $days = 3): \Illuminate\Database\Eloquent\Collection
    {
        return Order::active()
            ->whereBetween('deadline', [now()->toDateString(), now()->addDays($days)->toDateString()])
            ->with('creator:id,name')
            ->orderBy('deadline')
            ->get();
    }
}
