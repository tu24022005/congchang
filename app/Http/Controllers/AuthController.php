<?php 
namespace App\Http\Controllers; 

use App\Models\User; 
use App\Models\Voucher;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use App\Services\CartService;
use App\Services\ActivityLogService;
use App\Notifications\ChangePasswordOtpNotification;

class AuthController extends Controller 
{ 
    private const VALID_EMAIL_RULES = ['required', 'string', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[A-Za-z]{2,}$/D'];

    // Hiển thị form đăng ký 
    public function showRegistrationForm() 
    { 
        return view('auth.register'); 
    } 

    // Xử lý đăng ký người dùng 
    public function register(Request $request) 
    { 
        $request->validate([ 
            'name' => 'required|string|max:255',
            'email' => [...self::VALID_EMAIL_RULES, 'max:255', 'unique:users'],
            'phone' => ['required', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', 'unique:users,phone'],
            'password' => 'required|string|min:8|confirmed', 
            'terms' => 'accepted',
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại Việt Nam không hợp lệ.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'terms.accepted' => 'Bạn cần đồng ý với điều khoản sử dụng.',
        ]); 

        try { 
            Log::info('Registering user with email: ' . $request->email); 
            
            // 1. Tạo tài khoản người dùng mới
            $user = User::create([ 
                'name' => $request->name, 
                'email' => $request->email, 
                'phone' => $request->phone,
                'password' => Hash::make($request->password), 
                'role' => 'customer', 
            ]); 

            // 2. Kích hoạt sự kiện đăng ký và gửi email xác thực
            event(new Registered($user));

            Log::info('User registered successfully: ' . $request->email); 
            
            // 3. Đăng nhập luôn cho người dùng sau khi đăng ký
            Auth::login($user);
            app(CartService::class)->mergeSession($user);

            // 4. Chuyển hướng đến trang thông báo xác thực email
            return redirect()->route('verification.notice');
            
        } catch (\Exception $e) { 
            Log::error('Registration failed: ' . $e->getMessage()); 
            // Đã Việt hóa thông báo lỗi đăng ký
            return back()->with('error', 'Đăng ký thất bại. Vui lòng thử lại.'); 
        } 
    } 

    // Hiển thị form đăng nhập 
    public function showLoginForm() 
    { 
        return view('auth.login'); 
    } 

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => self::VALID_EMAIL_RULES]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Đã gửi link đặt lại mật khẩu. Hãy kiểm tra email của bạn.')
            : back()->withErrors(['email' => 'Không tìm thấy tài khoản với email này.']);
    }

    public function showResetPasswordForm(string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request()->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => self::VALID_EMAIL_RULES,
            'password' => 'required|min:8|confirmed',
        ], [
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => null,
                'login_attempts' => 0,
                'login_locked_at' => null,
            ])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công. Bạn có thể đăng nhập ngay.')
            : back()->withErrors(['email' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
    }

    // Xử lý đăng nhập người dùng 
    public function login(Request $request) 
    {
        $request->validate([ 
            'email' => self::VALID_EMAIL_RULES,
            'password' => 'required|string|min:8',
        ]); 

        $user = User::where('email', $request->input('email'))->first();
        if ($user?->login_locked_at) {
            return redirect()->route('password.request', ['email' => $user->email])
                ->withErrors(['email' => 'Tài khoản đã bị khóa sau 5 lần đăng nhập sai. Vui lòng đặt lại mật khẩu để mở khóa.']);
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->forget('authenticated_with_google');
            $user = Auth::user();
            $user->forceFill(['login_attempts' => 0, 'login_locked_at' => null])->save();
            $request->session()->regenerate(); 
            app(CartService::class)->mergeSession($user);

            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            $roleHome = [
                'admin' => 'admin.dashboard',
                'manager' => 'admin.dashboard',
                'warehouse_staff' => 'admin.products.index',
                'customer_service' => 'admin.orders.index',
            ][$user->role] ?? null;
            if ($roleHome) {
                return redirect()->intended(route($roleHome)); 
            }
            return redirect()->intended(route('welcome')); 
        } 

        if ($user) {
            $user->increment('login_attempts');
            $user->refresh();
            if ($user->login_attempts >= 5) {
                $user->forceFill(['login_locked_at' => now()])->save();

                return redirect()->route('password.request', ['email' => $user->email])
                    ->withErrors(['email' => 'Bạn đã nhập sai mật khẩu 5 lần. Vui lòng đặt lại mật khẩu để mở khóa tài khoản.']);
            }
        }

        $message = $user?->google_id
            ? 'Mật khẩu Google không dùng để đăng nhập trực tiếp trên website. Hãy chọn “Google”, hoặc đăng nhập Google rồi đặt mật khẩu website trong mục tài khoản.'
            : 'Email hoặc mật khẩu không chính xác. Còn ' . (5 - ($user?->login_attempts ?? 0)) . ' lần thử.';

        return back()->withErrors([
            'email' => $message,
        ])->onlyInput('email');
    } 
    // Hiển thị form đổi mật khẩu
    public function showChangePasswordForm(Request $request)
    {
        $user = $request->user() ?? Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('auth.change-password', [
            'authenticatedWithGoogle' => session('authenticated_with_google', false),
            'user' => $user,
            'maskedEmail' => $this->maskEmail($user->email ?? ''),
        ]);
    }

    public function account(Request $request)
    {
        $user = $request->user();
        $completedSpend = (float) $user->orders()->whereIn('status', ['paid', 'completed'])->sum('total');
        $effectiveValue = max($completedSpend, (float) ($user->loyalty_points * 10000));
        $membershipTier = User::membershipTierFor($completedSpend, (int) $user->loyalty_points);
        $membershipTiers = [
            ['name' => 'Mới tham gia', 'threshold' => 0, 'points' => 0],
            ['name' => 'Thành viên', 'threshold' => 1000000, 'points' => 100],
            ['name' => 'Bạc', 'threshold' => 2000000, 'points' => 200],
            ['name' => 'Vàng', 'threshold' => 5000000, 'points' => 500],
            ['name' => 'Bạch kim', 'threshold' => 10000000, 'points' => 1000],
            ['name' => 'Kim cương', 'threshold' => 20000000, 'points' => 2000],
        ];
        $nextTier = collect($membershipTiers)->first(fn (array $tier) => $effectiveValue < $tier['threshold']);
        $previousThreshold = collect($membershipTiers)
            ->last(fn (array $tier) => $effectiveValue >= $tier['threshold'])['threshold'] ?? 0;
        $tierProgress = $nextTier
            ? min(100, max(0, (($effectiveValue - $previousThreshold) / max(1, $nextTier['threshold'] - $previousThreshold)) * 100))
            : 100;
        $addresses = $user->addresses()->orderByDesc('is_default')->latest('id')->get();
        $orderStats = [
            'processing' => $user->orders()->whereIn('status', ['processing', 'confirmed', 'packing'])->count(),
            'shipping' => $user->orders()->where('status', 'shipping')->count(),
            'completed' => $user->orders()->where('status', 'completed')->count(),
            'cancelled' => $user->orders()->whereIn('status', ['cancelled', 'refund_pending', 'refunded'])->count(),
        ];
        $wishlistCount = $user->wishlistProducts()->count();
        $voucherCount = Voucher::where('user_id', $user->id)->where(function ($query) {
            $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today());
        })->where(function ($query) {
            $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
        })->count();
        $unreadNotificationCount = $user->unreadNotifications()->count();

        return view('account.index', compact(
            'user', 'completedSpend', 'effectiveValue', 'membershipTier', 'nextTier', 'tierProgress', 'addresses', 'orderStats',
            'wishlistCount', 'voucherCount', 'unreadNotificationCount'
        ));
    }

    public function vouchers(Request $request)
    {
        $vouchers = Voucher::with(['category', 'product'])
            ->where(function ($query) use ($request) {
                $query->where('user_id', $request->user()->id)
                    ->orWhereHas('collectedByUsers', fn ($users) => $users->whereKey($request->user()->id));
            })->latest()->get();
        $availableVouchers = Voucher::with(['category', 'product'])
            ->where('scope', 'platform')
            ->whereNull('user_id')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->whereDoesntHave('collectedByUsers', fn ($users) => $users->whereKey($request->user()->id))
            ->latest()->get();

        return view('account.vouchers', compact('vouchers', 'availableVouchers'));
    }

    public function collectVoucher(Request $request, Voucher $voucher)
    {
        abort_unless($voucher->scope === 'platform' && !$voucher->user_id, 404);
        if (!$voucher->isAvailable()) {
            return back()->with('error', 'Voucher này đã hết hạn hoặc hết lượt.');
        }
        $request->user()->collectedVouchers()->syncWithoutDetaching([$voucher->id]);
        return back()->with('success', 'Đã thu thập voucher ' . $voucher->code . '.');
    }

    public function notifications(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(15);

        return view('account.notifications', compact('notifications'));
    }

    public function readNotification(Request $request, string $notification)
    {
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $item->markAsRead();

        return redirect()->to($item->data['url'] ?? route('account.notifications'));
    }

    public function updateAccount(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [...self::VALID_EMAIL_RULES, 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'personal_phone' => ['nullable', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'personal_phone.regex' => 'Số điện thoại cá nhân Việt Nam không hợp lệ.',
            'personal_phone.unique' => 'Số điện thoại cá nhân này đã được sử dụng.',
        ]);

        $emailChanged = $validated['email'] !== $user->email;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['personal_phone'] ?? null;

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();

            return redirect()->route('verification.notice')->with('success', 'Email đã được cập nhật. Vui lòng xác thực email mới.');
        }

        return back()->with('success', 'Thông tin tài khoản đã được cập nhật.');
    }

    // Gửi mã OTP xác thực đổi mật khẩu qua email
    public function sendChangePasswordOtp(Request $request)
    {
        $user = $request->user() ?? Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập lại để tiếp tục.'], 401);
        }

        // Chống spam: Giới hạn gửi lại sau tối thiểu 60 giây
        $lastSent = session('change_password_otp_last_sent');
        if ($lastSent && now()->diffInSeconds($lastSent) < 60) {
            $remaining = 60 - now()->diffInSeconds($lastSent);
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đợi ' . $remaining . ' giây trước khi yêu cầu gửi lại mã OTP.'
            ], 429);
        }

        // Tạo mã OTP 6 chữ số ngẫu nhiên
        $otp = (string) random_int(100000, 999999);
        $expiresMinutes = 10;

        session([
            'change_password_otp' => $otp,
            'change_password_otp_expires_at' => now()->addMinutes($expiresMinutes)->timestamp,
            'change_password_otp_last_sent' => now(),
        ]);

        Cache::put('change_password_otp_' . $user->id, [
            'code' => $otp,
            'expires_at' => now()->addMinutes($expiresMinutes)->timestamp,
        ], now()->addMinutes($expiresMinutes));

        try {
            $user->notifyNow(new ChangePasswordOtpNotification($otp, $expiresMinutes));
        } catch (\Throwable $e) {
            Log::error('Lỗi gửi email OTP đổi mật khẩu: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi email OTP lúc này. Vui lòng kiểm tra lại cấu hình thư.'
            ], 500);
        }

        $maskedEmail = $this->maskEmail($user->email ?? '');

        return response()->json([
            'success' => true,
            'message' => 'Mã OTP 6 chữ số đã được gửi đến email ' . $maskedEmail . '. Mã có hiệu lực trong 10 phút.',
            'masked_email' => $maskedEmail,
        ]);
    }

    // Xử lý logic đổi mật khẩu kèm xác thực OTP
    public function updatePassword(Request $request)
    {
        $user = $request->user() ?? Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $authenticatedWithGoogle = $request->session()->get('authenticated_with_google', false);

        $request->validate([
            'current_password' => $authenticatedWithGoogle ? 'nullable' : 'required',
            'new_password' => 'required|min:8|confirmed',
            'otp' => 'required|digits:6',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'otp.required' => 'Vui lòng nhập mã OTP đã nhận qua email.',
            'otp.digits' => 'Mã xác thực OTP phải gồm 6 chữ số.',
        ]);

        // Kiểm tra mật khẩu cũ có đúng không
        if (!$authenticatedWithGoogle && !Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])->withInput();
        }

        // Kiểm tra mã OTP
        $sessionOtp = session('change_password_otp');
        $sessionExpiresAt = session('change_password_otp_expires_at');
        $cached = Cache::get('change_password_otp_' . $user->id);

        $validOtp = $sessionOtp ?? ($cached['code'] ?? null);
        $expiresAt = $sessionExpiresAt ?? ($cached['expires_at'] ?? 0);

        if (!$validOtp || now()->timestamp > $expiresAt) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn hoặc chưa được tạo. Vui lòng bấm "Gửi mã OTP" để nhận mã mới.'])->withInput();
        }

        if ((string) $request->otp !== (string) $validOtp) {
            return back()->withErrors(['otp' => 'Mã OTP không chính xác. Vui lòng kiểm tra lại email.'])->withInput();
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        $user->login_attempts = 0;
        $user->login_locked_at = null;
        $user->save();

        // Xóa OTP khỏi session và cache
        session()->forget(['change_password_otp', 'change_password_otp_expires_at', 'change_password_otp_last_sent', 'authenticated_with_google']);
        Cache::forget('change_password_otp_' . $user->id);

        ActivityLogService::record(
            'auth.password_changed',
            'Tài khoản ' . $user->name . ' đã đổi mật khẩu thành công qua xác thực OTP email.',
            $user
        );

        return back()->with('success', 'Đổi mật khẩu thành công! Mật khẩu mới của bạn đã có hiệu lực.');
    }

    protected function maskEmail(?string $email): string
    {
        if (empty($email)) return '';
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;
        $name = $parts[0];
        $domain = $parts[1];
        $len = mb_strlen($name);
        if ($len <= 2) {
            $maskedName = mb_substr($name, 0, 1) . '*';
        } else {
            $maskedName = mb_substr($name, 0, 2) . str_repeat('*', min(5, max(1, $len - 3))) . mb_substr($name, -1);
        }
        return $maskedName . '@' . $domain;
    }
    // Xử lý đăng xuất người dùng 
    public function logout(Request $request) 
    { 
        if (Auth::check()) {
            Auth::logout(); 
             
            $request->session()->invalidate(); 
            $request->session()->regenerateToken(); 

            return redirect()->route('login')->with('success', 'Đăng xuất thành công!'); 
        }

        return redirect()->route('login'); 
    } 
}