<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['creator', 'invoice'])->latest();

        // Optional status filter
        if ($status = $request->query('status')) {
            $query->byStatus($status);
        }

        // Optional archived filter
        if ($request->query('archived') === 'true') {
            $query->archived();
        } elseif ($request->query('archived') === 'false') {
            $query->active();
        }

        $orders = $query->paginate($request->query('per_page', 15));

        return response()->json($orders);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->validated(),
            $request->user()
        );

        return response()->json($order, 201);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['creator', 'invoice', 'trackingHistories.updatedBy:id,name']);

        return response()->json($order);
    }

    /**
     * Update the specified order in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $order = $this->orderService->updateOrder($order, $request->validated());

        return response()->json($order);
    }

    /**
     * Remove the specified order from storage (admin only).
     */
    public function destroy(Request $request, Order $order): JsonResponse
    {
        if (! $request->user()?->isAdmin()) {
            abort(403, 'Access denied. Only admin can delete orders.');
        }

        $order->delete();

        return response()->json(null, 204);
    }
}
