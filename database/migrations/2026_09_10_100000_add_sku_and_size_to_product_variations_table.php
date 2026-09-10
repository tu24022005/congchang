<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique()->after('product_id');
            $table->decimal('size_value', 10, 2)->nullable()->after('storage');
            $table->string('size_unit', 20)->nullable()->after('size_value');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('variation_id')->nullable()->after('product_id')->constrained('product_variations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['variation_id']);
            $table->dropColumn('variation_id');
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn(['sku', 'size_value', 'size_unit']);
        });
    }
};
