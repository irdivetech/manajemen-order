<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['creator', 'invoice'])->active()->latest();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->byStatus($status);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view(isMobile() ? 'orders.mobile.index' : 'orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(): View
    {
        $genders = \App\Models\MasterGender::where('is_active', true)->get();
        $sizeCategories = \App\Models\MasterSizeCategory::where('is_active', true)->get();
        $sizes = \App\Models\MasterSize::where('is_active', true)->get();
        $materials = \App\Models\MasterMaterial::where('is_active', true)->get();
        $clothingCategories = \App\Models\MasterClothingCategory::where('is_active', true)->get();

        return view(isMobile() ? 'orders.mobile.create' : 'orders.create', compact('genders', 'sizeCategories', 'sizes', 'materials', 'clothingCategories'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $designFiles = $request->hasFile('design_files')
            ? $request->file('design_files')
            : [];

        $this->orderService->createOrder(
            $request->validated(),
            $request->user(),
            $designFiles
        );

        return redirect()->route('orders.index')
            ->with('success', 'Order berhasil dibuat.');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load(['creator', 'invoice', 'trackingHistories.updatedBy', 'designFiles', 'sizeDetails.gender', 'sizeDetails.sizeCategory', 'sizeDetails.size', 'clothingCategory', 'material']);

        return view(isMobile() ? 'orders.mobile.show' : 'orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     * Only allowed when status is still 'order_received'.
     */
    public function edit(Order $order): View
    {
        if (! $order->isEditable()) {
            abort(403, 'Pesanan hanya dapat diubah saat masih dalam tahap "Pesanan Diterima".');
        }

        $order->load('designFiles', 'sizeDetails');

        $genders = \App\Models\MasterGender::where('is_active', true)->get();
        $sizeCategories = \App\Models\MasterSizeCategory::where('is_active', true)->get();
        $sizes = \App\Models\MasterSize::where('is_active', true)->get();
        $materials = \App\Models\MasterMaterial::where('is_active', true)->get();
        $clothingCategories = \App\Models\MasterClothingCategory::where('is_active', true)->get();

        return view(isMobile() ? 'orders.mobile.edit' : 'orders.edit', compact('order', 'genders', 'sizeCategories', 'sizes', 'materials', 'clothingCategories'));
    }

    /**
     * Update the specified order in storage.
     * Only allowed when status is still 'order_received'.
     */
    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        if (! $order->isEditable()) {
            abort(403, 'Pesanan hanya dapat diubah saat masih dalam tahap "Pesanan Diterima".');
        }

        $newDesignFiles = $request->hasFile('design_files')
            ? $request->file('design_files')
            : [];

        $deleteFileIds = $request->input('delete_design_files', []);

        $this->orderService->updateOrder(
            $order,
            $request->validated(),
            $newDesignFiles,
            array_map('intval', $deleteFileIds)
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order berhasil diperbarui.');
    }

    /**
     * Remove the specified order from storage.
     * Only allowed when status is still 'order_received'.
     */
    public function destroy(Request $request, Order $order): RedirectResponse
    {
        if (! $request->user()?->isAdmin()) {
            abort(403, 'Access denied. Only admin can delete orders.');
        }

        if (! $order->isDeletable()) {
            abort(403, 'Pesanan hanya dapat dihapus saat masih dalam tahap "Pesanan Diterima". Setelah masuk produksi, data tidak boleh dihapus.');
        }

        // Hapus file desain fisik dari storage
        \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory("designs/{$order->id}");

        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order berhasil dihapus.');
    }

    /**
     * Display archived orders.
     */
    public function archives(Request $request): View
    {
        $query = Order::with(['creator', 'invoice'])->archived()->latest();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view(isMobile() ? 'archives.mobile.index' : 'archives.index', compact('orders'));
    }
}
