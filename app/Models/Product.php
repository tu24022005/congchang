<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'quantity',
        'price',
        'flash_sale_price',
        'flash_sale_starts_at',
        'flash_sale_ends_at',
        'category_id',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'flash_sale_price' => 'decimal:2',
        'flash_sale_starts_at' => 'datetime',
        'flash_sale_ends_at' => 'datetime',
    ];

    public function isFlashSaleActive(): bool
    {
        return $this->flash_sale_price !== null
            && $this->flash_sale_starts_at?->isPast()
            && $this->flash_sale_ends_at?->isFuture()
            && (float) $this->flash_sale_price < (float) $this->price;
    }

    public function effectivePrice(?ProductVariation $variation = null): float
    {
        $basePrice = $variation ? (float) $variation->price : (float) $this->price;

        return $this->isFlashSaleActive()
            ? min($basePrice, (float) $this->flash_sale_price)
            : $basePrice;
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
    // Một sản phẩm có nhiều biến thể
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class)->latest();
    }
// Liên kết 1 - Nhiều: 1 Sản phẩm có nhiều ảnh trong gallery
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    // Một sản phẩm thuộc về một danh mục
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function wishlistedByUsers()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    public function stockAlertSubscriptions()
    {
        return $this->hasMany(StockAlertSubscription::class);
    }
}