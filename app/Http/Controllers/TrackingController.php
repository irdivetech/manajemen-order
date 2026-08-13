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
        $currentId = \App\Models\MasterTrackingStatus::orderBy('sort_order')->value('id');
        while ($currentId) {
            $status = \App\Models\MasterTrackingStatus::find($currentId);
            if ($status) {
                if ($status->is_active) {
                    $pipeline->push($status);
                }
                $currentId = \App\Models\TrackingFlowRule::where('from_status_id', $currentId)->value('to_status_id');
            } else {
                $currentId = null;
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
}
