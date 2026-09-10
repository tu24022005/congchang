<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_images') || !Schema::hasTable('products')) {
            return;
        }

        $products = DB::table('products')
            ->where('image', 'like', 'products/beauty-%.%')
            ->get(['id', 'image']);

        foreach ($products as $product) {
            if (!preg_match('/beauty-(\d+)\./', $product->image, $matches)) {
                continue;
            }

            $imageNumber = (int) $matches[1];
            $groupStart = intdiv($imageNumber - 1, 5) * 5 + 1;
            $gallery = range($groupStart, $groupStart + 4);

            DB::table('product_images')->where('product_id', $product->id)->delete();

            $now = now();
            $images = collect($gallery)
                ->reject(fn (int $number) => $number === $imageNumber)
                ->map(function (int $number) use ($product, $now) {
                    return [
                        'product_id' => $product->id,
                        'image_path' => 'products/beauty-' . str_pad((string) $number, 2, '0', STR_PAD_LEFT) . '.jpg',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })
                ->all();

            DB::table('product_images')->insert($images);
        }
    }

    public function down(): void
    {
        $productIds = DB::table('products')
            ->where('image', 'like', 'products/beauty-%.%')
            ->pluck('id');

        DB::table('product_images')->whereIn('product_id', $productIds)->delete();
    }
};
