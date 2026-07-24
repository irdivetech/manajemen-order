<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TrackingHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TrackingService
{
    /**
     * Add a new tracking history entry for the given order and update
     * the order's current_status. If the new status is 'shipping',
     * the order will also be automatically archived.
     */
    public function addHistory(
        Order $order,
        string $status,
        string $description,
        User $updatedBy,
    ): TrackingHistory {
        // Create the tracking history record
        $tracking = TrackingHistory::create([
            'order_id'    => $order->id,
            'status'      => $status,
            'description' => $description,
            'updated_by'  => $updatedBy->id,
        ]);

        // Update the order's current status
        $order->current_status = $status;

        // Auto-archive when shipping status is reached
        if ($status === Order::STATUS_SHIPPING) {
            $order->archived_at = now();
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
