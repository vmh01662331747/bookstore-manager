<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'tier',
        'points',
        'total_spent',
    ];

    protected $casts = [
        'points' => 'integer',
        'total_spent' => 'float',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getTierLabelAttribute(): string
    {
        return match ($this->tier) {
            'diamond' => 'Kim Cương',
            'gold' => 'Vàng',
            'silver' => 'Bạc',
            default => 'Đồng',
        };
    }

    public function getTierBadgeAttribute(): string
    {
        return match ($this->tier) {
            'diamond' => 'info',
            'gold' => 'warning',
            'silver' => 'secondary',
            default => 'dark',
        };
    }

    public function getDiscountPercentAttribute(): int
    {
        return match ($this->tier) {
            'diamond' => 10,
            'gold' => 5,
            'silver' => 3,
            default => 0,
        };
    }

    public function recalculateTier(): void
    {
        if ($this->total_spent >= 5000000) {
            $this->tier = 'diamond';
        } elseif ($this->total_spent >= 2000000) {
            $this->tier = 'gold';
        } elseif ($this->total_spent >= 500000) {
            $this->tier = 'silver';
        } else {
            $this->tier = 'bronze';
        }
    }
}
