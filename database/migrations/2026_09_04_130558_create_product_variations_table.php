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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Khóa ngoại liên kết bảng products
            $table->string('color')->nullable(); // Màu sắc (VD: Đen, Titan)
            $table->string('storage')->nullable(); // Dung lượng (VD: 256GB, 1TB)
            $table->integer('price'); // Giá trị thực tế của phiên bản này
            $table->integer('stock')->default(0); // Số lượng tồn kho
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};