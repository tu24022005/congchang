<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $reviewSets = [
            [5, 'Đóng gói cẩn thận, sản phẩm đúng mô tả. Mình sẽ mua lại.', true],
            [5, 'Chất lượng ổn, dùng dịu da và giao hàng nhanh.', false],
            [4, 'Sản phẩm tốt, giá hợp lý. Mong shop bổ sung thêm nhiều phân loại.', true],
        ];
        $names = ['Nguyễn Minh Anh', 'Lê Khánh Linh', 'Trần Ngọc Mai'];
        $userId = DB::table('users')->value('id');

        foreach (DB::table('products')->pluck('id') as $productId) {
            if (DB::table('product_reviews')->where('product_id', $productId)->exists()) {
                continue;
            }

            $variants = DB::table('product_variations')->where('product_id', $productId)->orderBy('id')->get();
            foreach ($reviewSets as $index => [$rating, $comment, $hasMedia]) {
                $variant = $variants->get($index % max(1, $variants->count()));
                $variantLabel = $variant
                    ? trim(implode(' · ', array_filter([
                        $variant->sku,
                        $variant->color,
                        $variant->size_value ? rtrim(rtrim($variant->size_value, '0'), '.') . $variant->size_unit : null,
                    ])))
                    : null;

                DB::table('product_reviews')->insert([
                    'product_id' => $productId,
                    'user_id' => $userId,
                    'reviewer_name' => $names[$index],
                    'rating' => $rating,
                    'comment' => $comment,
                    'variant_label' => $variantLabel,
                    'is_verified_purchase' => true,
                    'has_media' => $hasMedia,
                    'created_at' => now()->subDays(($index + 1) * 3),
                    'updated_at' => now()->subDays(($index + 1) * 3),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('product_reviews')->delete();
    }
};
