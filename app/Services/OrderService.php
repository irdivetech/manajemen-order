<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDesignFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
     *  4. Save design files (if any)
     *  5. Generate and attach an invoice
     *  6. Create the initial tracking history entry
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $designFiles
     */
    public function createOrder(array $data, User $creator, array $designFiles = []): Order
    {
        return DB::transaction(function () use ($data, $creator, $designFiles): Order {
            $data['created_by']    = $creator->id;
            $data['order_number']  = $this->generateOrderNumber();

            $totalPrice = collect($data['size_details'])->sum(function ($detail) {
                return $detail['quantity'] * $detail['price'];
            });
            $data['total_price']    = $totalPrice;
            
            // Snapshot material price
            if (isset($data['material_id'])) {
                $material = \App\Models\MasterMaterial::find($data['material_id']);
                if ($material) {
                    $data['material_price_snapshot'] = $material->price;
                }
            }

            // Get initial status from DB (lowest sort_order is assumed to be initial)
            $initialStatus = \App\Models\MasterTrackingStatus::orderBy('sort_order')->value('code') ?? Order::STATUS_ORDER_RECEIVED;
            $data['current_status'] = $initialStatus;

            $sizeDetailsData = $data['size_details'];
            unset($data['size_details']);

            /** @var Order $order */
            $order = Order::create($data);

            foreach ($sizeDetailsData as $detail) {
                // Ensure the deprecated fields are filled to avoid SQL errors
                $detail['gender'] = \App\Models\OrderSizeDetail::GENDER_MALE;
                $detail['size'] = 'M';
                $order->sizeDetails()->create($detail);
            }

            // Save design files
            if (!empty($designFiles)) {
                $this->handleDesignFiles($order, $designFiles);
            }

            // Generate invoice automatically
            $this->invoiceService->generateInvoice($order);

            // Create initial tracking history
            $this->trackingService->addHistory(
                order: $order,
                status: $initialStatus,
                description: 'Pesanan baru dibuat dan sedang diproses.',
                updatedBy: $creator,
            );

            return $order->load(['invoice', 'trackingHistories', 'creator', 'designFiles']);
        });
    }

    /**
     * Update an order's details.
     *
     * Recalculates total_price if quantity or price changes.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $newDesignFiles
     * @param  array<int, int>  $deleteFileIds
     */
    public function updateOrder(Order $order, array $data, array $newDesignFiles = [], array $deleteFileIds = []): Order
    {
        if (isset($data['size_details'])) {
            $totalPrice = collect($data['size_details'])->sum(function ($detail) {
                return $detail['quantity'] * $detail['price'];
            });

            $data['total_price'] = $totalPrice;

            // Sync invoice subtotal
            if ($order->invoice) {
                // Update order's total price first so invoice service can read it correctly
                $order->total_price = $data['total_price'];
                $this->invoiceService->generateInvoice($order);
            }
        }
        
        // Snapshot material price if material changed
        if (isset($data['material_id']) && $data['material_id'] != $order->material_id) {
            $material = \App\Models\MasterMaterial::find($data['material_id']);
            if ($material) {
                $data['material_price_snapshot'] = $material->price;
            }
        }

        $sizeDetailsData = null;
        if (isset($data['size_details'])) {
            $sizeDetailsData = $data['size_details'];
            unset($data['size_details']);
        }

        $order->update($data);

        if ($sizeDetailsData !== null) {
            $order->sizeDetails()->delete();
            foreach ($sizeDetailsData as $detail) {
                // Ensure the deprecated fields are filled to avoid SQL errors
                $detail['gender'] = \App\Models\OrderSizeDetail::GENDER_MALE;
                $detail['size'] = 'M';
                $order->sizeDetails()->create($detail);
            }
        }

        // Delete marked design files
        if (!empty($deleteFileIds)) {
            $this->deleteDesignFiles($order, $deleteFileIds);
        }

        // Upload new design files
        if (!empty($newDesignFiles)) {
            $this->handleDesignFiles($order, $newDesignFiles);
        }

        return $order->fresh(['invoice', 'trackingHistories', 'creator', 'designFiles']);
    }

    /**
     * Store uploaded design files to disk and record them in the database.
     *
     * @param  array<int, UploadedFile>  $files
     */
    private function handleDesignFiles(Order $order, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->store("designs/{$order->id}", 'public');

            $order->designFiles()->create([
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    /**
     * Delete specific design files by their IDs, removing files from disk too.
     *
     * @param  array<int, int>  $fileIds
     */
    private function deleteDesignFiles(Order $order, array $fileIds): void
    {
        $files = $order->designFiles()->whereIn('id', $fileIds)->get();

        foreach ($files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }
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

