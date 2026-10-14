<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'isbn',
        'title',
        'category_id',
        'author_id',
        'publisher',
        'published_year',
        'pages',
        'cost_price',
        'price',
        'stock',
        'min_stock',
        'sold_count',
        'rating',
        'cover_color',
        'cover_url',
        'description',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'float',
        'price' => 'float',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'sold_count' => 'integer',
        'rating' => 'float',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->min_stock;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->stock <= 0;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->price <= 0) {
            return 0;
        }
        return round((($this->price - $this->cost_price) / $this->price) * 100, 1);
    }

    /**
     * Gợi ý số lượng cần nhập thêm dựa trên tốc độ bán và ngưỡng an toàn.
     */
    public function getRecommendedRestockAttribute(): int
    {
        $target = max($this->min_stock * 3, (int) ceil($this->sold_count * 0.5) + $this->min_stock);
        return max(0, $target - $this->stock);
    }

    /**
     * Giá bìa niêm yết (cao hơn giá bán ưu đãi khoảng 15% - 18%, làm tròn nghìn đồng).
     */
    public function getOriginalPriceAttribute(): float
    {
        return (float) (ceil(($this->price * 1.18) / 1000) * 1000);
    }

    /**
     * Phần trăm tiết kiệm so với giá bìa.
     */
    public function getDiscountBadgePercentAttribute(): int
    {
        $orig = $this->original_price;
        if ($orig <= $this->price || $orig <= 0) {
            return 0;
        }
        return (int) round((($orig - $this->price) / $orig) * 100);
    }

    /**
     * Màu nền bìa sách có chiều sâu ánh sáng như bìa in thực tế.
     */
    public function getCoverGradientAttribute(): string
    {
        $base = $this->cover_color ?: '#1e3a8a';
        return "linear-gradient(145deg, {$base} 0%, #0f172a 100%)";
    }
}
