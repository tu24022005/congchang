<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_images') || !Schema::hasTable('products')) {
            return;
        }

        $productIds = DB::table('products')
            ->where('image', 'products/beauty-01.jpg')
            ->pluck('id');

        foreach ($productIds as $productId) {
            DB::table('product_images')->where('product_id', $productId)->delete();

            $now = now();
            $images = collect(range(2, 8))->map(function (int $number) use ($productId, $now) {
                return [
                    'product_id' => $productId,
                    'image_path' => 'products/beauty-' . str_pad((string) $number, 2, '0', STR_PAD_LEFT) . '.jpg',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->all();

            DB::table('product_images')->insert($images);
        }
    }

    public function down(): void
    {
        $productIds = DB::table('products')
            ->where('image', 'products/beauty-01.jpg')
            ->pluck('id');

        DB::table('product_images')
            ->whereIn('product_id', $productIds)
            ->where('image_path', 'like', 'products/beauty-%')
            ->delete();
    }
};