<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    /**
     * Payment status constants.
     */
    public const PAYMENT_UNPAID  = 'unpaid';
    public const PAYMENT_PAID    = 'paid';
    public const PAYMENT_PARTIAL = 'partial';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'invoice_number',
        'subtotal',
        'tax',
        'grand_total',
        'payment_status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal'    => 'decimal:2',
            'tax'         => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    /**
     * Get the order associated with this invoice.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ─── Helper Methods ──────────────────────────────────────────────────────

    /**
     * Check if the invoice is fully paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    /**
     * Check if the invoice is unpaid.
     */
    public function isUnpaid(): bool
    {
        return $this->payment_status === self::PAYMENT_UNPAID;
    }
}
