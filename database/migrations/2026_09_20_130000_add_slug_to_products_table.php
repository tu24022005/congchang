<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Product;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        Product::query()->orderBy('id')->each(function (Product $product): void {
            $base = Str::slug($product->name) ?: 'san-pham';
            $slug = $base;
            $suffix = 2;

            while (Product::where('slug', $slug)->where('id', '<>', $product->id)->exists()) {
                $slug = $base . '-' . $suffix++;
            }

            $product->updateQuietly(['slug' => $slug]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
