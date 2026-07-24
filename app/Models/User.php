<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    /**
     * Get all orders created by this user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    /**
     * Get all tracking histories updated by this user.
     */
    public function trackingHistories(): HasMany
    {
        return $this->hasMany(TrackingHistory::class, 'updated_by');
    }

    // ─── Helper Methods ──────────────────────────────────────────────────────

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is an owner.
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }
}
