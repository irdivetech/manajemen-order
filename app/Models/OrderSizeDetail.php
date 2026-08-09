<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderSizeDetail extends Model
{
    use HasFactory;

    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';
    public const GENDER_CHILD = 'child';

    public const GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
        self::GENDER_CHILD,
    ];

    public const STANDARD_SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    public const CHILD_SIZES = ['1', '2', '4', '6', '8', '10', '12', '14'];

    protected $fillable = [
        'order_id',
        'color', // NEW
        'gender_id', // NEW
        'size_category_id', // NEW
        'size_type', // NEW
        'size_id', // NEW
        'gender', // DEPRECATED
        'size', // DEPRECATED
        'quantity',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->quantity * $this->price;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function masterGender(): BelongsTo
    {
        return $this->belongsTo(MasterGender::class, 'gender_id');
    }

    public function sizeCategory(): BelongsTo
    {
        return $this->belongsTo(MasterSizeCategory::class, 'size_category_id');
    }

    public function masterSize(): BelongsTo
    {
        return $this->belongsTo(MasterSize::class, 'size_id');
    }
}
