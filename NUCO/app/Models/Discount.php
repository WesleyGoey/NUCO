<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'type',
        'min_order_amount',
    ];

    public function periods(): HasMany
    {
        return $this->hasMany(Period::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}