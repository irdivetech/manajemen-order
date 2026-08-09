<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingFlowRule extends Model
{
    protected $fillable = [
        'from_status_id',
        'to_status_id',
    ];

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(MasterTrackingStatus::class, 'from_status_id');
    }

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(MasterTrackingStatus::class, 'to_status_id');
    }
}
