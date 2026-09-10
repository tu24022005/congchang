<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            // Khóa ngoại liên kết với bảng products (xóa sản phẩm thì tự xóa luôn ảnh)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('image_path'); // Đường dẫn lưu ảnh
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};