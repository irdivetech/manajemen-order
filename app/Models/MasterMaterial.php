<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterMaterial extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'price_per_unit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_unit' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
