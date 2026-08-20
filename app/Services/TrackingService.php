<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TrackingHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class TrackingService
{
    /**
     * Add a new tracking history entry for the given order and update
     * the order's current_status. The new status must be the next one
     * in the sequential pipeline — no skipping, no going backward.
     *
     * If the new status is 'shipping', the order will also be
     * automatically archived.
     *
     * @throws \InvalidArgumentException If the status transition is invalid.
     */
    public function addHistory(
        Order $order,
        string $status,
        string $description,
        User $updatedBy,
        ?string $subType = null,
    ): TrackingHistory {
        // Enforce sequential advancement, unless it's the very first entry (initial status)
        $isFirstEntry = ! $order->trackingHistories()->exists();
        
        if (! $isFirstEntry && ! $order->canAdvanceTo($status)) {
            $nextStatus = $order->getNextStatus();
            $nextLabel = $nextStatus ? Order::statusLabel($nextStatus) : 'Selesai';

            throw new \InvalidArgumentException(
                "Status tidak valid. Pesanan ini hanya bisa dilanjutkan ke tahap: \"{$nextLabel}\"."
            );
        }

        // Check if target status requires payment
        $targetStatusModel = \App\Models\MasterTrackingStatus::where('code', $status)->first();
        if ($targetStatusModel && $targetStatusModel->requires_payment) {
            $order->loadMissing('invoice');
            if (!$order->invoice || !$order->invoice->isPaid()) {
                throw new \InvalidArgumentException(
                    "Pesanan harus lunas sebelum bisa lanjut ke tahap \"{$targetStatusModel->label}\"."
                );
            }
        }

        // Create the tracking history record
        $tracking = TrackingHistory::create([
            'order_id'    => $order->id,
            'status'      => $status,
            'sub_type'    => $subType,
            'description' => $description,
            'updated_by'  => $updatedBy->id,
        ]);

        // Update the order's current status
        $order->current_status = $status;

        // Save the chosen production route if the current stage is production
        if ($status === 'production' && $subType) {
            $order->production_route = $subType;
        }

        // Auto-archive when shipping status is reached
        if ($status === Order::STATUS_SHIPPING) {
            $order->archived_at = Carbon::now();
        }

        $order->save();

        return $tracking;
    }

    /**
     * Get all tracking histories for a given order, ordered newest first.
     *
     * @return Collection<int, TrackingHistory>
     */
    public function getHistory(Order $order): Collection
    {
        return $order->trackingHistories()
            ->with('updatedBy:id,name,role')
            ->get();
    }

    /**
     * Get the latest tracking history entry for an order.
     */
    public function getLatest(Order $order): ?TrackingHistory
    {
        return $order->trackingHistories()->first();
    }
}
