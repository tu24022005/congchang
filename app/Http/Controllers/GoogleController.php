<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\CartService;
use Illuminate\Support\Carbon;

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
            
            // Ưu tiên liên kết Google hiện có, sau đó mới ghép theo email.
            $user = User::where('google_id', $googleUser->id)->first()
                ?? User::where('email', $googleUser->email)->first();

            if ($user) {
                // Một Google ID không được phép liên kết với hai tài khoản website.
                $linkedUser = User::where('google_id', $googleUser->id)
                    ->where('id', '!=', $user->getKey())
                    ->exists();
                if ($linkedUser) {
                    throw new \RuntimeException('Google account is already linked to another user.');
                }

                $user->forceFill([
                    'google_id' => $googleUser->id,
                    'email_verified_at' => $user->email_verified_at ?? Carbon::now(),
                ])->save();
                Auth::login($user);
            } else {
                // Nếu chưa có, tự động tạo tài khoản mới bằng thông tin Google
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(16)), // Mật khẩu ngẫu nhiên
                    'role' => 'customer',
                    'email_verified_at' => Carbon::now(),
                ]);
                Auth::login($newUser);
            }

            session()->put('authenticated_with_google', true);
            app(CartService::class)->mergeSession(Auth::user());
            return redirect()->intended('/');
        } catch (\Exception $e) {
            Log::error('Google login failed.', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return redirect('/login')->with('error', 'Đăng nhập Google thất bại. Vui lòng thử lại.');
        }
    }
}