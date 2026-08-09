<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTrackingStatus extends Model
{
    protected $fillable = [
        'code',
        'label',
        'group',
        'sort_order',
        'requires_payment',
        'has_sub_type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'requires_payment' => 'boolean',
            'has_sub_type' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
