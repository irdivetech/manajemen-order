<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Get system alerts (deadlines, unpaid invoices, etc).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAlerts(): array
    {
        $alerts = [];

        // 1. Unpaid Invoices
        $unpaidInvoices = Invoice::where('payment_status', 'unpaid')
            ->with('order')
            ->latest()
            ->take(5)
            ->get();

        foreach ($unpaidInvoices as $invoice) {
            if (!$invoice->order) continue;
            
            $alerts[] = [
                'type'    => 'invoice',
                'title'   => 'Tagihan Belum Dibayar',
                'message' => "Faktur #{$invoice->invoice_number} (Rp " . number_format($invoice->subtotal, 0, ',', '.') . ") milik {$invoice->order->customer_name} belum dibayar.",
                'link'    => route('orders.invoice', $invoice->order_id),
                'icon'    => 'bi-receipt',
                'color'   => 'danger',
                'time'    => $invoice->created_at->diffForHumans(),
            ];
        }

        // 2. Deadlines approaching (within 3 days) and not completed
        $nearingDeadlines = Order::whereNotIn('current_status', [Order::STATUS_SHIPPING])
            ->whereNull('archived_at')
            ->whereDate('deadline', '<=', Carbon::now()->addDays(3))
            ->latest('deadline')
            ->take(5)
            ->get();

        foreach ($nearingDeadlines as $order) {
            $daysLeft = (int) Carbon::now()->startOfDay()->diffInDays(Carbon::parse($order->deadline)->startOfDay(), false);

            if ($daysLeft < 0) {
                $text  = "Terlambat " . abs($daysLeft) . " hari!";
                $color = 'danger';
            } elseif ($daysLeft === 0) {
                $text  = "Deadline HARI INI!";
                $color = 'danger';
            } else {
                $text  = "Sisa {$daysLeft} hari lagi.";
                $color = 'warning';
            }

            $alerts[] = [
                'type'    => 'deadline',
                'title'   => 'Tenggat Waktu Kritis',
                'message' => "Pesanan {$order->order_number} ({$order->customer_name}): {$text}",
                'link'    => route('orders.show', $order->id),
                'icon'    => 'bi-clock-history',
                'color'   => $color,
                'time'    => $order->updated_at->diffForHumans(),
            ];
        }

        return $alerts;
    }
}
