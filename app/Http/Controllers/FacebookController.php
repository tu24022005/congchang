<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        if (!config('services.facebook.client_id') || !config('services.facebook.client_secret') || !config('services.facebook.redirect')) {
            Log::warning('Facebook login is not configured.');

            return redirect()->route('login')->with('error', 'Đăng nhập Facebook chưa được cấu hình. Vui lòng liên hệ quản trị viên.');
        }

        return Socialite::driver('facebook')
            ->scopes(['email'])
            ->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            $email = $facebookUser->getEmail();

            if (!$email) {
                return redirect()->route('login')->with('error', 'Facebook không cung cấp email. Vui lòng cho phép ứng dụng truy cập email hoặc đăng nhập bằng phương thức khác.');
            }

            $user = User::where('facebook_id', $facebookUser->getId())->first()
                ?? User::where('email', $email)->first();

            if ($user) {
                $user->forceFill([
                    'facebook_id' => $facebookUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ])->save();
            } else {
                $user = User::create([
                    'name' => $facebookUser->getName() ?: 'Khách hàng Facebook',
                    'email' => $email,
                    'facebook_id' => $facebookUser->getId(),
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'customer',
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);
            request()->session()->regenerate();
            app(CartService::class)->mergeSession($user);

            $roleHome = [
                'admin' => 'admin.dashboard',
                'manager' => 'admin.dashboard',
                'warehouse_staff' => 'admin.products.index',
                'customer_service' => 'admin.orders.index',
            ][$user->role] ?? null;

            return redirect()->intended($roleHome ? route($roleHome) : route('welcome'));
        } catch (\Throwable $e) {
            Log::error('Facebook login failed.', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return redirect()->route('login')->with('error', 'Đăng nhập Facebook thất bại. Vui lòng thử lại.');
        }
    }
}
