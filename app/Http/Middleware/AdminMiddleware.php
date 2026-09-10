<?php 
namespace App\Http\Middleware; 
use Closure;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
class AdminMiddleware 
{ 
 public function handle(Request $request, Closure $next) 
 { 
 // Kiểm tra 1: Nếu chưa đăng nhập thì đẩy về trang login 
 if (!Auth::check()) { 
 return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.'); 
 } 
 // Kiểm tra 2: Đã đăng nhập nhưng role CÓ PHẢI là admin không? (Dùng ===) 
 if (Auth::user()->role === 'admin') { 
 return $next($request); // Cho phép đi tiếp 
 } 
  
 // Nếu đã đăng nhập nhưng không phải admin, đẩy về trang chủ kèm thông báo 
 return redirect()->route('welcome')->with('error', 'Bạn không có quyền truy cập vào khu vực quản trị.'); 
 } 
}

