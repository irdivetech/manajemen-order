<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialUsageEstimate extends Model
{
    protected $fillable = [
        'material_id',
        'size_id',
        'estimated_usage',
    ];

    protected function casts(): array
    {
        return [
            'estimated_usage' => 'decimal:4',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(MasterMaterial::class, 'material_id');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(MasterSize::class, 'size_id');
    }
}
