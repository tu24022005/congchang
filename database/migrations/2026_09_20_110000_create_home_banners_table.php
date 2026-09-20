<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();
            $table->string('badge', 100)->nullable();
            $table->string('title', 180);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_url')->nullable();
            $table->string('alt_text', 180)->nullable();
            $table->string('button_text', 80)->nullable();
            $table->string('button_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('home_banners')->insert([
            [
                'badge' => 'Bộ sưu tập mới',
                'title' => 'Beauty Essentials',
                'description' => 'Những sản phẩm làm đẹp thiết yếu giúp bạn chăm sóc làn da và tỏa sáng mỗi ngày.',
                'image_url' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?q=80&w=2000&auto=format&fit=crop',
                'alt_text' => 'Bộ sưu tập mỹ phẩm Aloha Beauty',
                'button_text' => 'Mua ngay',
                'button_url' => '/products',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'badge' => 'Được yêu thích',
                'title' => 'Skincare Ritual',
                'description' => 'Xây dựng chu trình chăm sóc da dịu lành, hiệu quả với những thành phần được chọn lọc.',
                'image_url' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?q=80&w=2000&auto=format&fit=crop',
                'alt_text' => 'Chăm sóc da',
                'button_text' => 'Khám phá ngay',
                'button_url' => '/products?category=1',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'badge' => 'Ưu đãi hôm nay',
                'title' => 'Self-care Moment',
                'description' => 'Tận hưởng những phút giây chăm sóc bản thân với sản phẩm lành tính và tiện dụng.',
                'image_url' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?q=80&w=2000&auto=format&fit=crop',
                'alt_text' => 'Chăm sóc cá nhân',
                'button_text' => 'Săn ưu đãi',
                'button_url' => '/products',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'badge' => 'Hair care edit',
                'title' => 'Tóc mềm, mood xinh',
                'description' => 'Khám phá routine chăm sóc tóc nhẹ nhàng, thơm dịu và phù hợp cho mỗi ngày.',
                'image_url' => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?q=80&w=2000&auto=format&fit=crop',
                'alt_text' => 'Chăm sóc tóc',
                'button_text' => 'Xem chăm sóc tóc',
                'button_url' => '/products?category=3',
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'badge' => 'Routine mới mỗi ngày',
                'title' => 'Chăm mình thật dịu',
                'description' => 'Chọn những món nhỏ xinh để biến vài phút skincare thành khoảng thời gian dành riêng cho bạn.',
                'image_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?q=80&w=2000&auto=format&fit=crop',
                'alt_text' => 'Routine chăm sóc da',
                'button_text' => 'Khám phá bộ sưu tập',
                'button_url' => '/products',
                'sort_order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_banners');
    }
};
