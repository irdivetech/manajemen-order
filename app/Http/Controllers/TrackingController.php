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
        $order->load(['trackingHistories.updatedBy']);
        $history = $this->trackingService->getHistory($order);

        return view(isMobile() ? 'tracking.mobile.index' : 'tracking.index', compact('order', 'history'));
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
        );

        return redirect()->route('orders.tracking', $order)
            ->with('success', 'Status produksi berhasil diperbarui.');
    }
}
