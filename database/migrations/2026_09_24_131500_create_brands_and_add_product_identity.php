<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        $now = now();
        DB::table('brands')->insert([
            ['name' => 'CeraVe', 'slug' => 'cerave', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'La Roche-Posay', 'slug' => 'la-roche-posay', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'The Ordinary', 'slug' => 'the-ordinary', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cocoon', 'slug' => 'cocoon', 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::table('products', function (Blueprint $table) {
            $table->string('product_code', 50)->nullable()->unique()->after('id');
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropUnique(['product_code']);
            $table->dropColumn(['product_code', 'brand_id']);
        });
        Schema::dropIfExists('brands');
    }
};
