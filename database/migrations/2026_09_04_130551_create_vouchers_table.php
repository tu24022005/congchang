<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã code (VD: GIAM50K)
            $table->enum('type', ['fixed', 'percent']); // Loại: Giảm tiền thẳng (fixed) hoặc Giảm % (percent)
            $table->integer('value'); // Mức giảm (VD: 50000 hoặc 10)
            $table->integer('min_order_value')->default(0); // Đơn tối thiểu để áp dụng
            $table->integer('usage_limit')->nullable(); // Giới hạn số lượt nhập
            $table->date('expires_at')->nullable(); // Hạn sử dụng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};