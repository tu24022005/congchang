<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProductReviewSeeder extends Seeder
{
    /**
     * Seed a small, repeatable set of Vietnamese customer reviews.
     *
     * The product/user pair is the natural idempotency key used by the
     * application when it prevents a customer from reviewing twice.
     */
    public function run(): void
    {
        $user = User::query()->orderBy('id')->first();

        if (!$user) {
            $user = User::updateOrCreate(
                ['email' => 'reviewer.demo@example.com'],
                [
                    'name' => 'Nguyễn Minh Anh',
                    'password' => Hash::make('reviewer-demo-password'),
                ]
            );
        }

        $comments = [
            'Đóng gói rất cẩn thận, sản phẩm đúng mô tả và dùng khá dễ chịu. Mình sẽ mua lại.',
            'Chất lượng ổn trong tầm giá, giao hàng nhanh. Dùng vài ngày thấy da/tóc mềm hơn.',
            'Mùi thơm nhẹ, không bị gắt. Sản phẩm tiện dụng cho chu trình hằng ngày.',
            'Shop tư vấn nhiệt tình, sản phẩm mới và sạch. Rất hài lòng với lần mua này.',
            'Kết cấu dễ dùng, thấm nhanh và không gây cảm giác bí. Sẽ giới thiệu cho bạn bè.',
        ];
        $reviewerNames = ['Nguyễn Minh Anh', 'Lê Khánh Linh', 'Trần Ngọc Mai', 'Phạm Thu Hà'];
        $media = ['reviews/review-swatch.svg', 'reviews/review-packing.svg', 'reviews/review-texture.svg'];

        foreach (Product::query()->orderBy('id')->get() as $index => $product) {
            $variant = $product->variations()->orderBy('id')->first();
            $variantLabel = $variant
                ? trim(implode(' · ', array_filter([$variant->sku, $variant->color, $variant->size_value])))
                : null;
            $hasMedia = $index % 3 === 0;
            $review = ProductReview::query()->firstOrNew([
                'product_id' => $product->id,
                'user_id' => $user->id,
            ]);
            $review->fill([
                'reviewer_name' => $reviewerNames[$index % count($reviewerNames)],
                'rating' => $index % 4 === 0 ? 4 : 5,
                'comment' => $comments[$index % count($comments)],
                'variant_label' => $variantLabel ?: null,
                'is_verified_purchase' => true,
                'has_media' => $hasMedia,
                'media_paths' => $hasMedia ? [$media[$index % count($media)]] : null,
            ]);
            $review->save();
        }

        // Keep the tracked demo assets available even on a fresh storage disk.
        foreach ($media as $path) {
            if (!Storage::disk('public')->exists($path)) {
                $this->command?->warn("Missing review asset: {$path}");
            }
        }
    }
}
