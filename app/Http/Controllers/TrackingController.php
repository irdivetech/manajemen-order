<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\TrackingService;
use App\Http\Requests\UpdateTrackingRequest;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    public function __construct(private readonly TrackingService $trackingService)
    {
    }

    /**
     * Display the tracking history for a given order.
     */
    public function index(Order $order): JsonResponse
    {
        $history = $this->trackingService->getHistory($order);

        return response()->json($history);
    }

    /**
     * Add a new tracking status entry for the order.
     */
    public function store(UpdateTrackingRequest $request, Order $order): JsonResponse
    {
        $tracking = $this->trackingService->addHistory(
            order: $order,
            status: $request->validated('status'),
            description: $request->validated('description'),
            updatedBy: $request->user(),
        );

        return response()->json($tracking, 201);
    }
}
