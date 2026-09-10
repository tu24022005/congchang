<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    // Bỏ comment dòng dưới nếu bạn muốn tắt các event của Model khi seed dữ liệu
    // use WithoutModelEvents; 

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tạo tài khoản Admin (giữ nguyên cấu trúc của bạn, bổ sung mật khẩu)
        User::factory()->create([
            'name' => 'Admin Manager',
            'email' => 'test@example.com',
            'password' => Hash::make('12345678'), // Mật khẩu mặc định là 12345678
        ]);

        $now = Carbon::now();

        // 2. Tạo danh mục mỹ phẩm và chăm sóc cá nhân
        DB::table('categories')->insert([
            ['name' => 'Chăm sóc da', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Trang điểm', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chăm sóc cá nhân', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Tạo sản phẩm mỹ phẩm mẫu
        DB::table('products')->insert([
            [
                'category_id' => 1,
                'name' => 'Serum cấp ẩm Hyaluronic Aloha',
                'price' => 289000,
                'description' => 'Serum cấp ẩm dịu nhẹ giúp làn da căng mịn và tươi sáng.',
                'image' => 'products/beauty-01.jpg',
                'stock' => 50,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 2,
                'name' => 'Son tint dưỡng môi Berry Glow',
                'price' => 219000,
                'description' => 'Son tint màu trong trẻo, lâu trôi và bổ sung độ ẩm cho môi.',
                'image' => 'products/beauty-02.jpg',
                'stock' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => 3,
                'name' => 'Sữa tắm hoa anh đào Soft Bloom',
                'price' => 179000,
                'description' => 'Sữa tắm làm sạch dịu nhẹ với hương hoa thư giãn và lưu hương mềm mại.',
                'image' => 'products/beauty-03.jpg',
                'stock' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}