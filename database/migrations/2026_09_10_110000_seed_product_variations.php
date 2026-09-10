<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $variants = [
            1 => [['color' => 'Mặc định', 'size_value' => 30, 'size_unit' => 'ml'], ['color' => 'Mặc định', 'size_value' => 50, 'size_unit' => 'ml']],
            2 => [['color' => 'Mặc định', 'size_value' => 30, 'size_unit' => 'ml'], ['color' => 'Mặc định', 'size_value' => 50, 'size_unit' => 'ml']],
            3 => [['color' => 'Mặc định', 'size_value' => 100, 'size_unit' => 'ml'], ['color' => 'Mặc định', 'size_value' => 200, 'size_unit' => 'ml']],
            4 => [['color' => 'Mặc định', 'size_value' => 50, 'size_unit' => 'g'], ['color' => 'Mặc định', 'size_value' => 100, 'size_unit' => 'g']],
            5 => [['color' => 'Mặc định', 'size_value' => 250, 'size_unit' => 'ml'], ['color' => 'Mặc định', 'size_value' => 500, 'size_unit' => 'ml']],
            6 => [['color' => 'Đỏ D01', 'size_value' => 3, 'size_unit' => 'g'], ['color' => 'Hồng D02', 'size_value' => 3, 'size_unit' => 'g']],
            9 => [['color' => '01 Sáng', 'size_value' => 30, 'size_unit' => 'ml'], ['color' => '02 Tự nhiên', 'size_value' => 30, 'size_unit' => 'ml']],
            10 => [['color' => 'Peach', 'size_value' => 5, 'size_unit' => 'g'], ['color' => 'Coral', 'size_value' => 5, 'size_unit' => 'g']],
            11 => [['color' => 'Mặc định', 'size_value' => 250, 'size_unit' => 'ml'], ['color' => 'Mặc định', 'size_value' => 500, 'size_unit' => 'ml']],
            12 => [['color' => 'Mặc định', 'size_value' => 250, 'size_unit' => 'ml'], ['color' => 'Mặc định', 'size_value' => 500, 'size_unit' => 'ml']],
            13 => [['color' => 'Mặc định', 'size_value' => 30, 'size_unit' => 'ml'], ['color' => 'Mặc định', 'size_value' => 50, 'size_unit' => 'ml']],
            14 => [['color' => 'Mặc định', 'size_value' => 250, 'size_unit' => 'g'], ['color' => 'Mặc định', 'size_value' => 500, 'size_unit' => 'g']],
            15 => [['color' => 'Gỗ sáng'], ['color' => 'Gỗ nâu']],
            16 => [['color' => 'Mặc định', 'size_value' => 250, 'size_unit' => 'ml'], ['color' => 'Mặc định', 'size_value' => 500, 'size_unit' => 'ml']],
            17 => [['color' => 'Mặc định', 'size_value' => 200, 'size_unit' => 'ml'], ['color' => 'Mặc định', 'size_value' => 400, 'size_unit' => 'ml']],
            18 => [['color' => 'Mặc định', 'size_value' => 200, 'size_unit' => 'g'], ['color' => 'Mặc định', 'size_value' => 400, 'size_unit' => 'g']],
            19 => [['color' => 'Mặc định', 'size_value' => 50, 'size_unit' => 'ml'], ['color' => 'Mặc định', 'size_value' => 75, 'size_unit' => 'ml']],
            20 => [['color' => 'Bộ tiêu chuẩn'], ['color' => 'Bộ mini']],
        ];

        foreach ($variants as $productId => $productVariants) {
            if (DB::table('product_variations')->where('product_id', $productId)->exists()) {
                continue;
            }

            $product = DB::table('products')->where('id', $productId)->first();
            if (!$product) {
                continue;
            }

            $firstStock = intdiv((int) $product->quantity, count($productVariants));
            foreach ($productVariants as $index => $variant) {
                DB::table('product_variations')->insert([
                    'product_id' => $productId,
                    'sku' => 'ALOHA-' . str_pad((string) $productId, 2, '0', STR_PAD_LEFT) . '-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'color' => $variant['color'],
                    'storage' => null,
                    'size_value' => $variant['size_value'] ?? null,
                    'size_unit' => $variant['size_unit'] ?? null,
                    'price' => $product->price,
                    'stock' => $index === count($productVariants) - 1 ? (int) $product->quantity - ($firstStock * $index) : $firstStock,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('product_variations')->where('sku', 'like', 'ALOHA-%')->delete();
    }
};
