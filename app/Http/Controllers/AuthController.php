<?php 
namespace App\Http\Controllers; 

use App\Models\User; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Log; 
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller 
{ 
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
            'email' => 'required|string|email|max:255|unique:users', 
            'password' => 'required|string|min:8|confirmed', 
        ]); 

        try { 
            Log::info('Registering user with email: ' . $request->email); 
            
            // 1. Tạo tài khoản người dùng mới
            $user = User::create([ 
                'name' => $request->name, 
                'email' => $request->email, 
                'password' => Hash::make($request->password), 
                'role' => 'customer', 
            ]); 

            // 2. Kích hoạt sự kiện đăng ký và gửi email xác thực
            event(new Registered($user));

            Log::info('User registered successfully: ' . $request->email); 
            
            // 3. Đăng nhập luôn cho người dùng sau khi đăng ký
            Auth::login($user);

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
        $request->validate(['email' => 'required|email']);

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
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => null])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công. Bạn có thể đăng nhập ngay.')
            : back()->withErrors(['email' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
    }

    // Xử lý đăng nhập người dùng 
    public function login(Request $request) 
    {
        $request->validate([ 
            'email' => 'required|string|email', 
            'password' => 'required|string', 
        ]); 

        if (Auth::attempt($request->only('email', 'password'))) { 
            $request->session()->regenerate(); 

            if (Auth::user()->role === 'admin') { 
                return redirect()->intended(route('admin.dashboard')); 
            } 
            return redirect()->intended(route('welcome')); 
        } 

        // Đã Việt hóa thông báo lỗi sai tài khoản/mật khẩu
        return back()->withErrors([ 
            'email' => 'Email hoặc mật khẩu không chính xác.', 
        ])->onlyInput('email'); 
    } 
// Hiển thị form đổi mật khẩu
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    // Xử lý logic đổi mật khẩu
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.'
        ]);

        // Kiểm tra mật khẩu cũ có đúng không
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        // Cập nhật mật khẩu mới
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

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