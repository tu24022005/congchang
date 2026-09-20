<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

class CartService
{
    public function forUser(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public function items(User $user): Collection
    {
        return $this->forUser($user)->items()->with(['product.category', 'variation'])->get();
    }

    public function asArray(User $user): array
    {
        $cart = [];

        foreach ($this->items($user) as $item) {
            if (!$item->product) {
                $item->delete();
                continue;
            }

            $variation = $item->variation;
            $originalPrice = $variation ? (float) $variation->price : (float) $item->product->price;
            $currentPrice = $item->product->effectivePrice($variation);
            if ((float) $item->price !== $currentPrice) {
                $item->update(['price' => $currentPrice]);
            }
            $variationLabel = $variation
                ? collect([
                    $variation->sku,
                    $variation->color,
                    $variation->size_value
                        ? rtrim(rtrim($variation->size_value, '0'), '.') . $variation->size_unit
                        : null,
                    $variation->storage,
                ])->filter()->implode(' · ')
                : null;

            $key = $variation ? $item->product_id . ':' . $variation->id : (string) $item->product_id;
            $cart[$key] = [
                'name' => $item->product->name,
                'price' => $currentPrice,
                'original_price' => $originalPrice,
                'promotion_label' => $currentPrice < $originalPrice ? 'Flash sale' : null,
                'quantity' => $item->quantity,
                'image' => $item->product->image,
                'category' => $item->product->category?->name ?? 'Chưa phân loại',
                'product_id' => $item->product_id,
                'variation_id' => $variation?->id,
                'variation' => $variationLabel,
            ];
        }

        return $cart;
    }

    public function syncSession(User $user): array
    {
        $cart = $this->asArray($user);
        session()->put('cart', $cart);

        return $cart;
    }

    public function mergeSession(User $user): void
    {
        $sessionCart = session()->pull('cart', []);
        if (empty($sessionCart)) {
            return;
        }

        $cart = $this->forUser($user);
        foreach ($sessionCart as $key => $details) {
            $parts = explode(':', (string) $key);
            $productId = (int) ($details['product_id'] ?? $parts[0]);
            $variationId = isset($parts[1]) ? (int) $parts[1] : ($details['variation_id'] ?? null);
            if (!Product::whereKey($productId)->exists()) {
                continue;
            }

            $query = $cart->items()->where('product_id', $productId);
            $query = $variationId ? $query->where('variation_id', $variationId) : $query->whereNull('variation_id');
            $item = $query->first() ?? new CartItem([
                'product_id' => $productId,
                'variation_id' => $variationId,
            ]);
            $product = Product::find($productId);
            $variation = $variationId ? $product?->variations()->find($variationId) : null;
            $item->price = $product ? $product->effectivePrice($variation) : (float) $details['price'];
            $item->quantity = ($item->quantity ?? 0) + max(1, (int) $details['quantity']);
            $item->save();
        }

        $this->syncSession($user);
    }

    public function clear(User $user): void
    {
        $this->forUser($user)->items()->delete();
        session()->forget('cart');
    }
}
