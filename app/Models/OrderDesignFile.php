<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrderDesignFile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'file_path',
        'original_name',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    /**
     * Get the order that owns this design file.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    /**
     * Get the public URL for this design file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
