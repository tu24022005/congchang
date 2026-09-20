<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\OrderController as AdminOrderController; 
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\InventoryLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\OrderController; 
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\StockAlertController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\PostCategoryController as AdminPostCategoryController;

// ==================================================
// 1. TRANG CHỦ
// ==================================================
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/blog', [PostController::class, 'index'])->name('posts.index');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::get('/policies', [PageController::class, 'policies'])->name('pages.policies');
Route::get('/faq', [PageController::class, 'faq'])->name('pages.faq');

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
    // Sản phẩm & Danh mục
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware('role:admin,manager')->name('dashboard');
    Route::get('/dashboard/chart-data', [\App\Http\Controllers\Admin\DashboardController::class, 'getChartData'])->middleware('role:admin,manager')->name('chart.data');
    Route::get('/products', [ProductController::class, 'index'])->middleware('role:admin,manager,warehouse_staff')->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->middleware('role:admin')->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->middleware('role:admin')->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->middleware('role:admin,manager,warehouse_staff')->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('role:admin,manager')->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('role:admin,manager')->name('products.update');
    Route::patch('/products/{product}/stock', [ProductController::class, 'updateStock'])->middleware('role:admin,warehouse_staff')->name('products.stock');
    Route::patch('/products/{product}/variation-stock', [ProductController::class, 'updateVariationStock'])->middleware('role:admin,warehouse_staff')->name('products.variation-stock');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('role:admin')->name('products.destroy');
    Route::resource('categories', CategoryController::class)->middleware('role:admin,manager');
    Route::get('/members', [\App\Http\Controllers\Admin\MemberController::class, 'index'])->middleware('role:admin,manager')->name('members.index');
    Route::get('/members/{user}', [\App\Http\Controllers\Admin\MemberController::class, 'show'])->middleware('role:admin,manager')->name('members.show');
    Route::get('/members/{user}/edit', [\App\Http\Controllers\Admin\MemberController::class, 'edit'])->middleware('role:admin,manager')->name('members.edit');
    Route::put('/members/{user}', [\App\Http\Controllers\Admin\MemberController::class, 'update'])->middleware('role:admin,manager')->name('members.update');
    Route::delete('/members/{user}', [\App\Http\Controllers\Admin\MemberController::class, 'destroy'])->middleware('role:admin,manager')->name('members.destroy');
    Route::get('/customers', [\App\Http\Controllers\Admin\MemberController::class, 'index'])->middleware('role:admin,manager')->name('customers.index');
    Route::get('/customers/{user}', [\App\Http\Controllers\Admin\MemberController::class, 'show'])->middleware('role:admin,manager')->name('customers.show');
    Route::get('/customers/{user}/edit', [\App\Http\Controllers\Admin\MemberController::class, 'edit'])->middleware('role:admin,manager')->name('customers.edit');
    Route::put('/customers/{user}', [\App\Http\Controllers\Admin\MemberController::class, 'update'])->middleware('role:admin,manager')->name('customers.update');
    Route::delete('/customers/{user}', [\App\Http\Controllers\Admin\MemberController::class, 'destroy'])->middleware('role:admin,manager')->name('customers.destroy');
    Route::get('/staff', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->middleware('role:admin')->name('staff.index');
    Route::post('/staff', [\App\Http\Controllers\Admin\StaffController::class, 'store'])->middleware('role:admin')->name('staff.store');
    Route::patch('/staff/{user}', [\App\Http\Controllers\Admin\StaffController::class, 'update'])->middleware('role:admin')->name('staff.update');
    Route::delete('/staff/{user}', [\App\Http\Controllers\Admin\StaffController::class, 'destroy'])->middleware('role:admin')->name('staff.destroy');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->middleware('role:admin,manager')->name('activity-logs.index');
    Route::get('/inventory-logs', [InventoryLogController::class, 'index'])->middleware('role:admin,manager,warehouse_staff')->name('inventory-logs.index');
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->middleware('role:admin,manager,warehouse_staff')->name('notifications.index');
    Route::get('/notifications/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'read'])->middleware('role:admin,manager,warehouse_staff')->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'readAll'])->middleware('role:admin,manager,warehouse_staff')->name('notifications.read-all');
    
    // Đơn hàng (CHÍNH LÀ DÒNG ĐANG BỊ THIẾU GÂY LỖI)
    Route::get('/orders', [AdminOrderController::class, 'index'])->middleware('role:admin,manager,customer_service')->name('orders.index'); 
    Route::get('/refund-requests', [AdminOrderController::class, 'refundRequests'])->middleware('role:admin,manager')->name('refunds.index');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'updateStatus'])->middleware('role:admin,manager')->name('orders.updateStatus'); 
    Route::post('/orders/{order}/refund', [AdminOrderController::class, 'refund'])->middleware('role:admin,manager')->name('orders.refund'); 
    Route::post('/orders/{order}/refund/approve', [AdminOrderController::class, 'approveRefund'])->middleware('role:admin,manager')->name('orders.refund.approve');
    Route::post('/orders/{order}/refund/reject', [AdminOrderController::class, 'rejectRefund'])->middleware('role:admin,manager')->name('orders.refund.reject');
    
    // Báo cáo & Voucher
    Route::get('/reports', [ReportController::class, 'index'])->middleware('role:admin,manager')->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->middleware('role:admin,manager')->name('reports.export');
    Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class)->middleware('role:admin,manager');
    Route::resource('home-banners', \App\Http\Controllers\Admin\HomeBannerController::class)
        ->parameters(['home-banners' => 'homeBanner'])
        ->except(['show'])
        ->middleware('role:admin,manager');
    Route::resource('posts', AdminPostController::class)->except(['show'])->middleware('role:admin,manager');
    Route::resource('post-categories', AdminPostCategoryController::class)
        ->parameters(['post-categories' => 'postCategory'])
        ->only(['index', 'store', 'update', 'destroy'])->middleware('role:admin,manager');
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'adminIndex'])->middleware('role:admin,customer_service')->name('chat.index');
    
    // Dữ liệu biểu đồ
});

// ==================================================
// 6. KHU VỰC NGƯỜI DÙNG 
// ==================================================
// Các trang catalog có thể xem công khai để hỗ trợ SEO và khách vãng lai.
Route::get('/products', [ProductController::class, 'userIndex'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show_normal'])->name('products.show');
Route::get('/categories', [CategoryController::class, 'indexNormal'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'showNormal'])->name('categories.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Tài khoản người dùng
    Route::get('/account', [AuthController::class, 'account'])->name('account');
    Route::get('/refunds', [OrderController::class, 'refunds'])->name('refunds.index');
    Route::get('/loyalty-points', [\App\Http\Controllers\LoyaltyController::class, 'index'])->name('loyalty.index');
    Route::post('/loyalty-points/redeem', [\App\Http\Controllers\LoyaltyController::class, 'redeem'])->name('loyalty.redeem');
    Route::put('/account', [AuthController::class, 'updateAccount'])->name('account.update');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
    Route::post('/products/{product}/stock-alert', [StockAlertController::class, 'store'])->name('products.stock-alert.store');
    Route::delete('/products/{product}/stock-alert', [StockAlertController::class, 'destroy'])->name('products.stock-alert.destroy');
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/change-password', [AuthController::class, 'updatePassword'])->middleware('throttle:5,1')->name('password.update');
    
    // Đánh giá sản phẩm yêu cầu đăng nhập và xác thực email.
    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('products.reviews.store');
    Route::patch('/products/{product}/reviews/{review}', [ProductReviewController::class, 'update'])->name('products.reviews.update');
    
    // Giỏ hàng
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.apply_voucher');
    Route::post('/cart/apply-vouchers', [CartController::class, 'applyVouchers'])->name('cart.apply_vouchers');
    
    // Thanh toán (Checkout)
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');

    // Chat Real-time
    Route::get('/chat/messages', [App\Http\Controllers\ChatController::class, 'fetchMessages']);
    Route::get('/admin/chat/history', [App\Http\Controllers\ChatController::class, 'history'])->middleware(['admin', 'role:admin,customer_service'])->name('admin.chat.history');
    Route::post('/chat/message', [App\Http\Controllers\ChatController::class, 'sendMessage']);
    Route::get('/chat/users', [App\Http\Controllers\ChatController::class, 'fetchUsers']);
    Route::post('/chat/heartbeat', [App\Http\Controllers\ChatController::class, 'heartbeat']);
    Route::get('/chat/presence', [App\Http\Controllers\ChatController::class, 'presence']);

Route::get('/search-suggestions', [App\Http\Controllers\ProductController::class, 'suggestions'])->name('products.suggestions');
    // Đơn hàng của người dùng
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('orders.confirm_received');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update_status');

    Route::post('/reviews', [App\Http\Controllers\ProductController::class, 'storeReview'])->name('reviews.store');
});