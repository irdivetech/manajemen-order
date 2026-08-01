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

    /**
     * Generate and persist a new invoice for the given order.
     *
     * This is called inside the DB transaction when an order is created.
     */
    public function generateInvoice(Order $order): Invoice
    {
        $subtotal   = (float) $order->total_price;
        $grandTotal = $subtotal; // Tax has been removed per user request

        $invoice = Invoice::firstOrNew(['order_id' => $order->id]);

        if (! $invoice->exists) {
            $invoice->invoice_number = $this->generateInvoiceNumber();
            $invoice->payment_status = Invoice::PAYMENT_UNPAID;
            $invoice->paid_amount    = 0;
        }

        $invoice->subtotal    = $subtotal;
        $invoice->grand_total = $grandTotal;

        // Re-evaluate payment status if invoice already exists, since grand_total might have changed
        if ($invoice->exists) {
            if ($invoice->paid_amount >= $grandTotal) {
                $invoice->payment_status = Invoice::PAYMENT_PAID;
            } elseif ($invoice->paid_amount > 0) {
                $invoice->payment_status = Invoice::PAYMENT_PARTIAL;
            } else {
                $invoice->payment_status = Invoice::PAYMENT_UNPAID;
            }
        }

        $invoice->save();

        return $invoice;
    }

    /**
     * Add a payment record and update the invoice status.
     */
    public function addPayment(Invoice $invoice, float $amount, ?string $method = null, ?string $notes = null): Invoice
    {
        $invoice->payments()->create([
            'amount'         => $amount,
            'payment_method' => $method,
            'notes'          => $notes,
            'paid_at'        => now(),
        ]);

        $paidAmount = $invoice->payments()->sum('amount');
        $invoice->paid_amount = $paidAmount;

        if ($paidAmount >= $invoice->grand_total) {
            $invoice->payment_status = Invoice::PAYMENT_PAID;
        } elseif ($paidAmount > 0) {
            $invoice->payment_status = Invoice::PAYMENT_PARTIAL;
        } else {
            $invoice->payment_status = Invoice::PAYMENT_UNPAID;
        }

        $invoice->save();

        return $invoice;
    }

    /**
     * Update the payment status of an invoice manually (for backward compatibility or direct overrides).
     */
    public function updatePaymentStatus(Invoice $invoice, string $status): Invoice
    {
        $invoice->payment_status = $status;
        
        // If manually set to paid, assume the remaining amount is paid in full
        if ($status === Invoice::PAYMENT_PAID && $invoice->paid_amount < $invoice->grand_total) {
            $remaining = $invoice->remainingAmount();
            $this->addPayment($invoice, $remaining, 'Manual Status Update', 'Status changed to Paid manually');
        } else {
            $invoice->save();
        }

        return $invoice;
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
