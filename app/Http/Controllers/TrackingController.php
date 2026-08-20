<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTrackingRequest;
use App\Models\Order;
use App\Services\TrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function __construct(private readonly TrackingService $trackingService)
    {
    }

    /**
     * Display the tracking history for a given order.
     */
    public function index(Order $order): View
    {
        $order->load(['trackingHistories.updatedBy', 'invoice']);
        $history = $this->trackingService->getHistory($order);

        // Fetch sequential pipeline (only active statuses)
        $pipeline = collect();
        $allStatuses = \App\Models\MasterTrackingStatus::where('is_active', true)
            ->get()
            ->keyBy('code');

        $route = $order->production_route;

        $codes = [
            'order_received',
            'material_order_pending',
            'material_order_ready',
            'fabric_cutting',
            'production',
        ];

        if (!$order->has_embroidery) {
            $codes[] = 'sewing';
        } else {
            if ($route === 'bordir') {
                $codes[] = 'embroidery';
                $codes[] = 'sewing';
            } elseif ($route === 'penjahitan') {
                $codes[] = 'sewing';
                $codes[] = 'embroidery';
            } elseif ($route === 'barengan') {
                // Skips individual embroidery and sewing
            } else {
                // If not yet selected but has embroidery, show default
                $codes[] = 'embroidery';
                $codes[] = 'sewing';
            }
        }

        $codes = array_merge($codes, [
            'button_installation',
            'qc',
            'ironing',
            'packing',
            'shipping',
        ]);

        foreach ($codes as $code) {
            if ($allStatuses->has($code)) {
                $pipeline->push($allStatuses->get($code));
            }
        }

        $isPaymentFulfilled = $order->invoice && $order->invoice->isPaid();

        return view(isMobile() ? 'tracking.mobile.index' : 'tracking.index', compact('order', 'history', 'pipeline', 'isPaymentFulfilled'));
    }

    /**
     * Add a new tracking status entry for the order.
     */
    public function store(UpdateTrackingRequest $request, Order $order): RedirectResponse
    {
        $this->trackingService->addHistory(
            order: $order,
            status: $request->validated('status'),
            description: $request->validated('description'),
            updatedBy: $request->user(),
            subType: $request->validated('sub_type')
        );

        return redirect()->route('orders.tracking', $order)
            ->with('success', 'Status produksi berhasil diperbarui.');
    }

    /**
     * Print shipping label / resi for a shipped order.
     */
    public function printShippingLabel(Order $order): View
    {
        // Only allow printing when status is shipping
        if (!$order->isShipped()) {
            abort(403, 'Resi hanya dapat dicetak setelah pesanan masuk tahap Pengiriman.');
        }

        $order->load('sizeDetails');

        return view('tracking.shipping_label', compact('order'));
    }
}
