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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // Khóa ngoại liên kết với bảng orders[cite: 1]
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Khóa ngoại liên kết với bảng products[cite: 1]
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            $table->integer('quantity');
            
            // Đã nâng sức chứa lên (20, 2) để tránh lỗi Database khi giá sản phẩm lớn
            $table->decimal('price', 20, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};