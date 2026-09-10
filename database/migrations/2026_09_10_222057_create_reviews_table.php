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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ai đánh giá
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Đánh giá sản phẩm nào
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Từ đơn hàng nào
            $table->tinyInteger('rating'); // Số sao (1-5)
            $table->text('comment')->nullable(); // Nội dung nhận xét (có thể bỏ trống)
            $table->timestamps(); // Thời gian đánh giá
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
