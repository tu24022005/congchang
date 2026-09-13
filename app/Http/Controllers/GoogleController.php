<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // Chuyển hướng người dùng sang trang đăng nhập Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Xử lý dữ liệu Google trả về
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Tìm user xem email này đã từng đăng ký chưa
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Nếu có rồi thì cập nhật ID và cho đăng nhập luôn
                $user->update(['google_id' => $googleUser->id]);
                Auth::login($user);
            } else {
                // Nếu chưa có, tự động tạo tài khoản mới bằng thông tin Google
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(16)), // Mật khẩu ngẫu nhiên
                    'role' => 'user' // Mặc định là Khách hàng
                ]);
                Auth::login($newUser);
            }

            if (!Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Lỗi đăng nhập Google: ' . $e->getMessage());
        }
    }
}