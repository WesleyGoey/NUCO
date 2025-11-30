<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function processedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'cashier_id');
    }

    // Helper methods
    // public function hasRole(string $roleName): bool
    // {
    //     return $this->role && $this->role->name === $roleName;
    // }

    // public function isOwner(): bool
    // {
    //     return $this->hasRole('owner');
    // }
    
    // public function isWaiter(): bool
    // {
    //     return $this->hasRole('waiter');
    // }

    // public function isChef(): bool
    // {
    //     return $this->hasRole('chef');
    // }

    // public function isCashier(): bool
    // {
    //     return $this->hasRole('cashier');
    // }
    
    // public function isReviewer(): bool
    // {
    //     return $this->hasRole('reviewer');
    // }
}