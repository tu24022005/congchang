<?php 
namespace App\Http\Controllers; 

use App\Models\User; 
use App\Models\Voucher;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Log; 
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use App\Services\CartService;

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
    public function showChangePasswordForm()
    {
        return view('auth.change-password', [
            'authenticatedWithGoogle' => session('authenticated_with_google', false),
        ]);
    }

    public function account(Request $request)
    {
        $user = $request->user();
        $completedSpend = $user->orders()->where('status', 'completed')->sum('total');
        $membershipTier = User::membershipTierFor($completedSpend);
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
            'user', 'completedSpend', 'membershipTier', 'addresses', 'orderStats',
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

        return redirect()->route('account.notifications');
    }

    public function updateAccount(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [...self::VALID_EMAIL_RULES, 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'personal_phone' => ['nullable', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', Rule::unique('users', 'phone')->ignore($user->id)],
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

    // Xử lý logic đổi mật khẩu
    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $authenticatedWithGoogle = $request->session()->get('authenticated_with_google', false);

        $request->validate([
            'current_password' => $authenticatedWithGoogle ? 'nullable' : 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.'
        ]);

        // Kiểm tra mật khẩu cũ có đúng không
        if (!$authenticatedWithGoogle && !Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        $user->login_attempts = 0;
        $user->login_locked_at = null;
        $user->save();
        $request->session()->forget('authenticated_with_google');

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
    // Xử lý đăng xuất người dùng 
    public function logout(Request $request) 
    { 
        Auth::logout(); 
         
        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 

        return redirect()->route('login'); 
    } 
}