<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Order;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'variant_label' => 'nullable|string|max:150',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $order = Order::whereKey($validated['order_id'])
            ->where('user_id', $request->user()->id)
            ->where('status', 'completed')
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->first();

        if (!$order) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá sau khi đã mua và hoàn tất đơn hàng.');
        }

        if ($product->reviews()->where('user_id', $request->user()->id)->exists()) {
            return back()->with('error', 'Bạn chỉ được đánh giá sản phẩm này một lần.');
        }

        $mediaPaths = [];
        foreach ($request->file('images', []) as $image) {
            $mediaPaths[] = $image->store('reviews', 'public');
        }

        $product->reviews()->create([
            'user_id' => $request->user()->id,
            'order_id' => $order->id,
            'reviewer_name' => $request->user()->name,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'variant_label' => $validated['variant_label'] ?? null,
            'is_verified_purchase' => true,
            'has_media' => count($mediaPaths) > 0,
            'media_paths' => $mediaPaths ?: null,
        ]);

        return back()->with('review_success', 'Cảm ơn bạn đã đánh giá sản phẩm.');
    }

    public function update(Request $request, Product $product, ProductReview $review)
    {
        abort_unless($review->product_id === $product->id && $review->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $mediaPaths = $review->media_paths ?? [];
        foreach ($request->file('images', []) as $image) {
            if (count($mediaPaths) >= 3) {
                break;
            }
            $mediaPaths[] = $image->store('reviews', 'public');
        }

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'has_media' => count($mediaPaths) > 0,
            'media_paths' => $mediaPaths ?: null,
        ]);

        return back()->with('review_success', 'Đã cập nhật đánh giá của bạn.');
    }
}
