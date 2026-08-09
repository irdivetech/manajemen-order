<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSize extends Model
{
    protected $fillable = [
        'size_type',
        'code',
        'label',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
