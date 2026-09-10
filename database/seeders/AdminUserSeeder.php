<?php 
namespace Database\Seeders; 
use Illuminate\Database\Seeder;

use App\Models\User; 
use Illuminate\Support\Facades\Hash; 
class AdminUserSeeder extends Seeder 
{ 
 public function run(): void 
 { 
 // Sử dụng updateOrCreate để tránh lỗi trùng lặp khi chạy lệnh seed nhiều lần[cite: 1] 
 User::updateOrCreate( 
 ['email' => 'nguyenvantu24022005@gmail.com'],  
 [ 
 'name' => 'Admin User', 
 'password' => Hash::make('1'),  
 'role' => 'admin', 
 'email_verified_at' => now(), // Đã xác thực sẵn email cho tài khoản admin 
 ] 
 ); 
 } 
}

