<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'customer_id',
        'channel',
        'subtotal',
        'discount_amount',
        'total_amount',
        'payment_method',
        'status',
        'note',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount_amount' => 'float',
        'total_amount' => 'float',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Hoàn tất',
            'pending' => 'Đang xử lý',
            'cancelled' => 'Đã hủy',
            default => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getPaymentLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'qr_transfer' => 'Chuyển khoản QR',
            'card' => 'Thẻ ngân hàng',
            default => 'Tiền mặt',
        };
    }

    public function getChannelLabelAttribute(): string
    {
        return $this->channel === 'online' ? 'Trực tuyến (Online)' : 'Tại quầy (POS)';
    }

    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->items()->sum('line_total');
        $discountPercent = $this->customer ? $this->customer->discount_percent : 0;
        $discount = round($subtotal * ($discountPercent / 100));
        $this->subtotal = $subtotal;
        $this->discount_amount = $discount;
        $this->total_amount = max(0, $subtotal - $discount);
        $this->save();
    }
}
