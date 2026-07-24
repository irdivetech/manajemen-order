<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /**
     * Production status constants.
     */
    public const STATUS_ORDER_RECEIVED    = 'order_received';
    public const STATUS_FABRIC_CUTTING    = 'fabric_cutting';
    public const STATUS_SEWING            = 'sewing';
    public const STATUS_EMBROIDERY        = 'embroidery';
    public const STATUS_BUTTON_INSTALLATION = 'button_installation';
    public const STATUS_SHIPPING          = 'shipping';

    /**
     * Ordered list of all production statuses.
     */
    public const STATUSES = [
        self::STATUS_ORDER_RECEIVED,
        self::STATUS_FABRIC_CUTTING,
        self::STATUS_SEWING,
        self::STATUS_EMBROIDERY,
        self::STATUS_BUTTON_INSTALLATION,
        self::STATUS_SHIPPING,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'created_by',
        'order_number',
        'customer_name',
        'customer_phone',
        'product_name',
        'product_type',
        'color',
        'size',
        'quantity',
        'price',
        'total_price',
        'order_date',
        'deadline',
        'current_status',
        'notes',
        'archived_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order_date'  => 'date',
            'deadline'    => 'date',
            'archived_at' => 'datetime',
            'price'       => 'decimal:2',
            'total_price' => 'decimal:2',
            'quantity'    => 'integer',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    /**
     * Get the user who created this order.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all tracking histories for this order.
     */
    public function trackingHistories(): HasMany
    {
        return $this->hasMany(TrackingHistory::class)->latest('created_at');
    }

    /**
     * Get the invoice associated with this order.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /**
     * Scope a query to only include archived orders.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope a query to only include active (non-archived) orders.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('current_status', $status);
    }

    // ─── Helper Methods ──────────────────────────────────────────────────────

    /**
     * Check if the order is archived.
     */
    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * Check if the order has reached the shipping status.
     */
    public function isShipped(): bool
    {
        return $this->current_status === self::STATUS_SHIPPING;
    }
}
