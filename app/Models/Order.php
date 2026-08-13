<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property \Illuminate\Support\Carbon|null $order_date
 * @property \Illuminate\Support\Carbon|null $deadline
 * @property \Illuminate\Support\Carbon|null $archived_at
 */
class Order extends Model
{
    use HasFactory;

    /**
     * Production status constants.
     * These must match the `code` values in the `master_tracking_statuses` table.
     */
    public const STATUS_ORDER_RECEIVED        = 'order_received';
    public const STATUS_MATERIAL_ORDER_PENDING = 'material_order_pending';
    public const STATUS_MATERIAL_ORDER_READY   = 'material_order_ready';
    public const STATUS_FABRIC_CUTTING        = 'fabric_cutting';
    public const STATUS_PRODUCTION            = 'production';
    public const STATUS_EMBROIDERY            = 'embroidery';
    public const STATUS_SEWING                = 'sewing';
    public const STATUS_BUTTON_INSTALLATION   = 'button_installation';
    public const STATUS_QC                    = 'qc';
    public const STATUS_IRONING               = 'ironing';
    public const STATUS_PACKING               = 'packing';
    public const STATUS_SHIPPING              = 'shipping';

    /**
     * All available statuses in pipeline order.
     *
     * @var list<string>
     */
    public const STATUSES = [
        self::STATUS_ORDER_RECEIVED,
        self::STATUS_MATERIAL_ORDER_PENDING,
        self::STATUS_MATERIAL_ORDER_READY,
        self::STATUS_FABRIC_CUTTING,
        self::STATUS_PRODUCTION,
        self::STATUS_EMBROIDERY,
        self::STATUS_SEWING,
        self::STATUS_BUTTON_INSTALLATION,
        self::STATUS_QC,
        self::STATUS_IRONING,
        self::STATUS_PACKING,
        self::STATUS_SHIPPING,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_category',
        'customer_title',
        'customer_address',
        'customer_city', // NEW
        'customer_district', // NEW
        'product_name',
        'product_type',
        'model_product', // NEW
        'has_embroidery', // NEW
        'clothing_category_id', // NEW
        'material_id', // NEW
        'material_price_snapshot', // NEW
        'total_price',
        'total_cost',
        'order_date',
        'deadline',
        'notes',
        'current_status',
        'production_route',
        'created_by',
        'archived_at',
        'is_material_purchased',
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
            'total_price' => 'decimal:2',
            'total_cost'  => 'decimal:2',
            'has_embroidery' => 'boolean',
            'is_material_purchased' => 'boolean',
            'material_price_snapshot' => 'decimal:2',
        ];
    }

    // ─── Accessors ───────────────────────────────────────────────────────────
    
    /**
     * Get unique colors from size details as comma separated string.
     */
    public function getColorAttribute(): string
    {
        return $this->sizeDetails->pluck('color')->filter()->unique()->implode(', ');
    }

    /**
     * Get material name from relation.
     */
    public function getMaterialAttribute(): string
    {
        return $this->masterMaterial?->name ?? '-';
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

    // ─── Relationships ───────────────────────────────────────────────────────

    /**
     * Get the user who created this order.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function clothingCategory(): BelongsTo
    {
        return $this->belongsTo(MasterClothingCategory::class, 'clothing_category_id');
    }

    public function masterMaterial(): BelongsTo
    {
        return $this->belongsTo(MasterMaterial::class, 'material_id');
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

    /**
     * Get all size details for this order.
     */
    public function sizeDetails(): HasMany
    {
        return $this->hasMany(OrderSizeDetail::class);
    }

    /**
     * Get all design files for this order.
     */
    public function designFiles(): HasMany
    {
        return $this->hasMany(OrderDesignFile::class);
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

    /**
     * Get the next status in the sequential production pipeline from database rules.
     * Returns null if the order is already at the final status.
     */
    public function getNextStatus(): ?string
    {
        $currentStatus = $this->current_status;
        $route = $this->production_route;

        // Conditional Routing Logic for Production Phase
        if ($currentStatus === 'production') {
            if ($route === 'bordir') return 'embroidery';
            if ($route === 'penjahitan') return 'sewing';
            if ($route === 'penjahitan_dan_bordir') return 'button_installation';
        }

        if ($currentStatus === 'embroidery') {
            if ($route === 'bordir') return 'sewing';
            return 'button_installation';
        }

        if ($currentStatus === 'sewing') {
            if ($route === 'penjahitan') return 'embroidery';
            return 'button_installation';
        }

        // Default sequential logic from DB for other statuses
        $currentStatusId = \App\Models\MasterTrackingStatus::where('code', $currentStatus)->value('id');
        if (!$currentStatusId) {
            return null;
        }

        // Loop through flow rules until we find an active status
        $nextStatusId = \App\Models\TrackingFlowRule::where('from_status_id', $currentStatusId)->value('to_status_id');
        while ($nextStatusId) {
            $status = \App\Models\MasterTrackingStatus::find($nextStatusId);
            if ($status && $status->is_active) {
                return $status->code;
            }
            $nextStatusId = \App\Models\TrackingFlowRule::where('from_status_id', $nextStatusId)->value('to_status_id');
        }

        return null;
    }

    /**
     * Check if the order can advance to the given status.
     * The new status must be exactly the next one in the pipeline.
     */
    public function canAdvanceTo(string $newStatus): bool
    {
        return $this->getNextStatus() === $newStatus;
    }

    /**
     * Check if the order data (customer info, product, price, etc.) can still be edited.
     * Orders can only be edited while still in "Pesanan Diterima" status.
     */
    public function isEditable(): bool
    {
        return $this->current_status === self::STATUS_ORDER_RECEIVED && !$this->isArchived();
    }

    /**
     * Check if the order can be deleted.
     * Orders can only be deleted while still in "Pesanan Diterima" status.
     */
    public function isDeletable(): bool
    {
        return $this->current_status === self::STATUS_ORDER_RECEIVED && !$this->isArchived();
    }

    /**
     * Get the human-readable label for a status.
     */
    public static function statusLabel(string $status): string
    {
        $master = \App\Models\MasterTrackingStatus::where('code', $status)->first();
        return $master ? $master->label : ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Get total quantity of items in this order.
     */
    public function totalQuantity(): int
    {
        return $this->sizeDetails->sum('quantity');
    }

    /**
     * Get total quantity of items in this order by gender name.
     * This is primarily used for the excel report.
     */
    public function quantityByGender(string $genderName): int
    {
        return $this->sizeDetails->filter(function ($detail) use ($genderName) {
            // master_genders -> name (e.g. "Laki-laki", "Perempuan", "Anak-anak")
            // The old hardcoded genders were 'male', 'female', 'child'
            // We map them loosely or exactly
            $name = strtolower($detail->gender?->name ?? '');
            if ($genderName === 'male') return str_contains($name, 'laki');
            if ($genderName === 'female') return str_contains($name, 'perempuan');
            if ($genderName === 'child') return str_contains($name, 'anak');
            return $name === strtolower($genderName);
        })->sum('quantity');
    }

    /**
     * Calculate the net profit of this order.
     */
    public function getProfit(): float
    {
        return max(0, $this->total_price - $this->total_cost);
    }

    /**
     * Calculate the profit margin percentage of this order.
     */
    public function getProfitMargin(): float
    {
        if ($this->total_price <= 0) {
            return 0;
        }

        return round(($this->getProfit() / $this->total_price) * 100, 1);
    }
}
