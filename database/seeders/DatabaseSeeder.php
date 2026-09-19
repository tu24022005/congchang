<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    // Bỏ comment dòng dưới nếu bạn muốn tắt các event của Model khi seed dữ liệu
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(BlogSeeder::class);
        $this->call(AdditionalProductSeeder::class);
        $this->call(ProductReviewSeeder::class);

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Admin Manager',
                'password' => Hash::make('12345678'),
            ]
        );
    }
}
