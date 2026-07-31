<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePaymentStatusRequest;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    /**
     * Display the invoice for a given order.
     */
    public function show(Order $order): View
    {
        $order->load(['invoice.payments', 'creator', 'sizeDetails']);
        $invoice = $order->invoice;

        if (! $invoice) {
            abort(404, 'Invoice tidak ditemukan untuk order ini.');
        }

        $bankAccounts = \App\Models\BankAccount::active()->get();

        return view(isMobile() ? 'invoices.mobile.show' : 'invoices.show', compact('order', 'invoice', 'bankAccounts'));
    }

    /**
     * Update the payment status of an order's invoice and add payment history.
     */
    public function updatePayment(UpdatePaymentStatusRequest $request, Order $order): RedirectResponse
    {
        $invoice = $order->invoice;

        if (! $invoice) {
            abort(404, 'Invoice tidak ditemukan untuk order ini.');
        }

        $status = $request->validated('payment_status');
        $amount = (float) $request->validated('payment_amount', 0);
        
        if ($status === \App\Models\Invoice::PAYMENT_UNPAID) {
            $this->invoiceService->updatePaymentStatus($invoice, $status);
        } else {
            // For partial or paid, if they submitted an amount, record the payment
            if ($amount > 0) {
                $this->invoiceService->addPayment(
                    $invoice,
                    $amount,
                    $request->validated('payment_method'),
                    $request->validated('payment_notes')
                );
            } else {
                // If they just changed status to paid without amount, update status
                $this->invoiceService->updatePaymentStatus($invoice, $status);
            }
        }

        return redirect()->route('orders.invoice', $order)
            ->with('success', 'Informasi pembayaran berhasil diperbarui.');
    }

    /**
     * Display printable invoice view.
     */
    public function print(Order $order): View
    {
        $order->load(['invoice', 'creator', 'sizeDetails']);
        $invoice = $order->invoice;

        if (! $invoice) {
            abort(404, 'Invoice tidak ditemukan untuk order ini.');
        }

        $bankAccounts = \App\Models\BankAccount::active()->get();

        return view('invoices.print', compact('order', 'invoice', 'bankAccounts'));
    }
}
