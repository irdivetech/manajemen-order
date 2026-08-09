<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterGender extends Model
{
    protected $fillable = [
        'code',
        'label',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
