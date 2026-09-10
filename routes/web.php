<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\OrderController as AdminOrderController; 
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\OrderController; 
use App\Http\Controllers\CheckoutController; 

// ==================================================
// 1. TRANG CHỦ
// ==================================================
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// ==================================================
// ĐĂNG NHẬP GOOGLE (ĐỂ NGOÀI CÙNG ĐỂ AI CŨNG BẤM ĐƯỢC)
// ==================================================
Route::get('/auth/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleController::class, 'handleGoogleCallback']);

// ==================================================
// 2. ROUTE KHÁCH (CHƯA ĐĂNG NHẬP)
// ==================================================
Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.store');
});

// ==================================================
// 3. ĐĂNG XUẤT
// ==================================================
Route::post('logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ==================================================
// 4. XÁC THỰC EMAIL
// ==================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('welcome')->with('success', 'Cảm ơn bạn đã đăng ký và xác thực thành công!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// ==================================================
// ==================================================
// 5. KHU VỰC ADMIN
// ==================================================
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Sản phẩm & Danh mục
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    
    // Đơn hàng (CHÍNH LÀ DÒNG ĐANG BỊ THIẾU GÂY LỖI)
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index'); 
    Route::patch('/orders/{order}', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus'); 
    
    // Báo cáo & Voucher
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class);
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'adminIndex'])->name('chat.index');
    
    // Dữ liệu biểu đồ
    Route::get('/dashboard/chart-data', [\App\Http\Controllers\Admin\DashboardController::class, 'getChartData'])->name('chart.data');
});

// ==================================================
// 6. KHU VỰC NGƯỜI DÙNG 
// ==================================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Sản phẩm & Danh mục
    Route::get('/products', [ProductController::class, 'userIndex'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show_normal'])->name('products.show');
    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('products.reviews.store');
    Route::patch('/products/{product}/reviews/{review}', [ProductReviewController::class, 'update'])->name('products.reviews.update');
    Route::get('/categories', [CategoryController::class, 'indexNormal'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'showNormal'])->name('categories.show');
    
    // Giỏ hàng
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.apply_voucher');
    
    // Thanh toán (Checkout)
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/vpbank-qr', [CheckoutController::class, 'showMomoQr'])->name('checkout.vpbank');

    // Chat Real-time
    Route::get('/chat/messages', [App\Http\Controllers\ChatController::class, 'fetchMessages']);
    Route::post('/chat/message', [App\Http\Controllers\ChatController::class, 'sendMessage']);
    Route::get('/chat/users', [App\Http\Controllers\ChatController::class, 'fetchUsers']);
    Route::post('/chat/heartbeat', [App\Http\Controllers\ChatController::class, 'heartbeat']);
    Route::get('/chat/presence', [App\Http\Controllers\ChatController::class, 'presence']);

Route::get('/search-suggestions', [App\Http\Controllers\ProductController::class, 'suggestions'])->name('products.suggestions');
Route::post('/orders/{id}/confirm-receipt', [App\Http\Controllers\OrderController::class, 'confirmReceipt'])->name('orders.confirm_receipt');
    // Đơn hàng của người dùng
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('orders.confirm_received');
    Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update_status');

    Route::post('/reviews', [App\Http\Controllers\ProductController::class, 'storeReview'])->name('reviews.store');
});