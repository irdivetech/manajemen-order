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
        $order->load(['invoice', 'creator']);
        $invoice = $order->invoice;

        if (! $invoice) {
            abort(404, 'Invoice tidak ditemukan untuk order ini.');
        }

        return view(isMobile() ? 'invoices.mobile.show' : 'invoices.show', compact('order', 'invoice'));
    }

    /**
     * Update the payment status of an order's invoice.
     */
    public function updatePayment(UpdatePaymentStatusRequest $request, Order $order): RedirectResponse
    {
        $invoice = $order->invoice;

        if (! $invoice) {
            abort(404, 'Invoice tidak ditemukan untuk order ini.');
        }

        $this->invoiceService->updatePaymentStatus(
            $invoice,
            $request->validated('payment_status'),
        );

        return redirect()->route('orders.invoice', $order)
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /**
     * Display printable invoice view.
     */
    public function print(Order $order): View
    {
        $order->load(['invoice', 'creator']);
        $invoice = $order->invoice;

        if (! $invoice) {
            abort(404, 'Invoice tidak ditemukan untuk order ini.');
        }

        return view('invoices.print', compact('order', 'invoice'));
    }
}
