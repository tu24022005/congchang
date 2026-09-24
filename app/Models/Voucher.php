<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['code', 'scope', 'user_id', 'type', 'value', 'min_order_value', 'usage_limit', 'expires_at', 'applies_to', 'category_id', 'product_id'];

    protected $casts = [
        'expires_at' => 'date',
        'used_count' => 'integer',
        'usage_limit' => 'integer',
    ];

    public function isAvailable(): bool
    {
        return (!$this->expires_at || !$this->expires_at->isBefore(today()))
            && (!$this->usage_limit || $this->used_count < $this->usage_limit);
    }

    public function remainingUses(): ?int
    {
        return $this->usage_limit === null
            ? null
            : max(0, $this->usage_limit - $this->used_count);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function collectedByUsers()
    {
        return $this->belongsToMany(User::class, 'voucher_user')->withTimestamps();
    }

    public function appliesToCart(array $cart): bool
    {
        return $this->eligibleSubtotal($cart) >= (int) $this->min_order_value;
    }

    public function eligibleSubtotal(array $cart): float
    {
        if ($this->applies_to === 'all' || $this->type === 'free_shipping') {
            return collect($cart)->sum(fn (array $item) => $item['price'] * $item['quantity']);
        }

        $productIds = collect($cart)->pluck('product_id')->filter()->unique();
        $products = Product::with('category')->whereIn('id', $productIds)->get()->keyBy('id');

        return collect($cart)->filter(function (array $item) use ($products) {
            $product = $products->get($item['product_id'] ?? null);
            return $product && (
                ($this->applies_to === 'product' && $product->id === $this->product_id)
                || ($this->applies_to === 'category' && $product->category_id === $this->category_id)
            );
        })->sum(fn (array $item) => $item['price'] * $item['quantity']);
    }
}