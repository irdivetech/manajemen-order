<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
        private readonly TrackingService $trackingService,
    ) {}

    /**
     * Create a new order wrapped in a database transaction.
     *
     * Steps performed atomically:
     *  1. Generate order number
     *  2. Calculate total price
     *  3. Create the order record
     *  4. Generate and attach an invoice
     *  5. Create the initial tracking history entry
     *
     * @param  array<string, mixed>  $data
     */
    public function createOrder(array $data, User $creator): Order
    {
        return DB::transaction(function () use ($data, $creator): Order {
            $data['created_by']    = $creator->id;
            $data['order_number']  = $this->generateOrderNumber();
            $data['total_price']   = $data['quantity'] * $data['price'];
            $data['current_status'] = Order::STATUS_ORDER_RECEIVED;

            /** @var Order $order */
            $order = Order::create($data);

            // Generate invoice automatically
            $this->invoiceService->generateInvoice($order);

            // Create initial tracking history
            $this->trackingService->addHistory(
                order: $order,
                status: Order::STATUS_ORDER_RECEIVED,
                description: 'Order has been received and is being processed.',
                updatedBy: $creator,
            );

            return $order->load(['invoice', 'trackingHistories', 'creator']);
        });
    }

    /**
     * Update an order's details.
     *
     * Recalculates total_price if quantity or price changes.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateOrder(Order $order, array $data): Order
    {
        if (isset($data['quantity']) || isset($data['price'])) {
            $quantity          = $data['quantity'] ?? $order->quantity;
            $price             = $data['price']    ?? $order->price;
            $data['total_price'] = $quantity * $price;

            // Sync invoice subtotal
            if ($order->invoice) {
                $this->invoiceService->generateInvoice($order);
            }
        }

        $order->update($data);

        return $order->fresh(['invoice', 'trackingHistories', 'creator']);
    }

    /**
     * Generate a unique sequential order number.
     *
     * Format: ORD-YYYY-XXXX (e.g. ORD-2026-0001)
     */
    public function generateOrderNumber(): string
    {
        $year   = now()->year;
        $prefix = "ORD-{$year}-";

        $last = Order::where('order_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('order_number');

        $nextSequence = $last
            ? (int) substr($last, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
    }
}
