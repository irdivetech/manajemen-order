<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\InvoiceService;
use App\Http\Requests\UpdatePaymentStatusRequest;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    /**
     * Display the invoice for a given order.
     */
    public function show(Order $order): JsonResponse
    {
        $invoice = $order->invoice;

        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found for this order.'], 404);
        }

        return response()->json($invoice);
    }

    /**
     * Update the payment status of an order's invoice.
     */
    public function updatePayment(UpdatePaymentStatusRequest $request, Order $order): JsonResponse
    {
        $invoice = $order->invoice;

        if (! $invoice) {
            return response()->json(['message' => 'Invoice not found for this order.'], 404);
        }

        $invoice = $this->invoiceService->updatePaymentStatus(
            $invoice,
            $request->validated('payment_status'),
        );

        return response()->json($invoice);
    }
}
