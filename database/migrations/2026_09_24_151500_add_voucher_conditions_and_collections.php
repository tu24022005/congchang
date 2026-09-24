<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('applies_to')->default('all')->after('scope');
            $table->foreignId('category_id')->nullable()->after('applies_to')->constrained('categories')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->after('category_id')->constrained('products')->nullOnDelete();
        });

        Schema::create('voucher_user', function (Blueprint $table) {
            $table->foreignId('voucher_id')->constrained('vouchers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['voucher_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_user');
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn(['applies_to', 'category_id', 'product_id']);
        });
    }
};
