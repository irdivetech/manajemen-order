<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;

class InvoiceService
{
    /**
     * Tax rate percentage (11%).
     */
    private const TAX_RATE = 0.11;

    public function generateInvoice(Order $order): Invoice
    {
        $subtotal   = (float) $order->total_price;
        $grandTotal = $subtotal;

        $invoice = Invoice::firstOrNew(['order_id' => $order->id]);

        if (! $invoice->exists) {
            $invoice->invoice_number = $this->generateInvoiceNumber();
            $invoice->payment_status = Invoice::PAYMENT_UNPAID;
        }

        $invoice->subtotal    = $subtotal;
        $invoice->grand_total = $grandTotal;
        $invoice->save();

        return $invoice;
    }

    /**
     * Update the payment status of an invoice.
     */
    public function updatePaymentStatus(Invoice $invoice, string $status): Invoice
    {
        $invoice->update(['payment_status' => $status]);

        return $invoice->fresh();
    }

    /**
     * Generate a unique sequential invoice number.
     *
     * Format: INV-YYYY-XXXX (e.g. INV-2026-0001)
     */
    public function generateInvoiceNumber(): string
    {
        $year  = now()->year;
        $prefix = "INV-{$year}-";

        $last = Invoice::where('invoice_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('invoice_number');

        $nextSequence = $last
            ? (int) substr($last, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
    }
}
