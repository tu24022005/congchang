<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('shop.seo.default_title'))</title>
    <meta name="description" content="@yield('meta_description', config('shop.seo.default_description'))">
    <meta name="theme-color" content="#f88379" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0f172a" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph / Facebook / Zalo -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:title" content="@yield('og_title', View::getSection('title') ? View::getSection('title') : config('shop.seo.default_title'))">
    <meta property="og:description" content="@yield('og_description', View::getSection('meta_description') ? View::getSection('meta_description') : config('shop.seo.default_description'))">
    <meta property="og:image" content="@yield('og_image', asset(config('shop.seo.default_og_image')))">
    <meta property="og:site_name" content="{{ config('shop.seo.site_name') }}">
    <meta property="og:locale" content="vi_VN">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', View::getSection('title') ? View::getSection('title') : config('shop.seo.default_title'))">
    <meta name="twitter:description" content="@yield('og_description', View::getSection('meta_description') ? View::getSection('meta_description') : config('shop.seo.default_description'))">
    <meta name="twitter:image" content="@yield('og_image', asset(config('shop.seo.default_og_image')))">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @stack('head')
    <link href="{{ asset_v('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset_v('css/animations.css') }}" rel="stylesheet">
    <link href="{{ asset_v('css/layout.css') }}" rel="stylesheet">
    @stack('styles')
    @yield('structured_data')
    @stack('schema')
    
</head>
<body class="{{ request()->routeIs('login', 'register', 'password.request', 'password.reset', 'verification.notice') ? 'auth-page' : '' }}" data-user-id="{{ Auth::id() ?? 'null' }}" data-newsletter-url="{{ route('newsletter.subscribe') }}">
    <script src="{{ asset_v('js/layout.js') }}" defer></script>
    <!-- PRELOADER MỞ WEB (BRAND ENTRANCE) -->
    <div id="app-preloader" class="app-preloader" aria-hidden="true">
        <div class="preloader-card">
            <div class="preloader-bloom-wrap">
                <div class="preloader-bloom-aura"></div>
                <div class="preloader-bloom-icon">
                    <i class="bi bi-flower1"></i>
                </div>
            </div>
            <div class="preloader-brand-title">BeatyCare <span class="preloader-sparkle">🌸</span></div>
            <p class="preloader-tagline">Vẻ đẹp rạng ngời &bull; Chăm sóc tự nhiên</p>
            <div class="preloader-progress-track">
                <div class="preloader-progress-fill"></div>
            </div>
        </div>
    </div>

    <!-- DUAL PROGRESS & SCROLL BAR -->
    <div id="top-progress-bar"></div>

    <!-- THANH ĐIỀU HƯỚNG GỌN GÀNG -->
    <nav class="navbar navbar-expand-lg glass-navbar shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold logo-hover" href="{{ url('/') }}">
                <i class="bi bi-flower1"></i> BeatyCare 🌸
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- CỤM BÊN TRÁI: DÀNH CHO KHÁCH -->
                <ul class="navbar-nav me-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link text-nowrap fw-semibold {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            Trang chủ
                        </a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link text-nowrap fw-semibold {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            Sản phẩm
                        </a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link text-nowrap fw-semibold {{ request()->routeIs('posts.*') ? 'active' : '' }}" href="{{ route('posts.index') }}">
                            Blog làm đẹp
                        </a>
                    </li>
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle text-nowrap fw-semibold {{ request()->routeIs('pages.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">Về BeatyCare</a>
                        <ul class="dropdown-menu border-0 shadow-sm">
                            <li><a class="dropdown-item" href="{{ route('pages.about') }}">Giới thiệu</a></li>
                            <li><a class="dropdown-item" href="{{ route('pages.contact') }}">Liên hệ</a></li>
                            <li><a class="dropdown-item" href="{{ route('pages.policies') }}">Đổi trả & vận chuyển</a></li>
                            <li><a class="dropdown-item" href="{{ route('pages.faq') }}">Câu hỏi thường gặp</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- THANH TÌM KIẾM TRUNG TÂM CO GỢI Ý (LIVE SEARCH) -->
                <form action="{{ route('products.index') }}" method="GET" class="d-flex mx-lg-3 my-3 my-lg-0 flex-grow-1 justify-content-center position-relative live-search-form" role="search">
                    <div class="input-group live-search-group">
                        <button type="submit" class="input-group-text bg-transparent border-0 text-dark ps-3 pe-2" aria-label="Tìm kiếm">
                            <i class="bi bi-search fw-bold search-icon"></i>
                        </button>
                        <input type="text" name="search" id="live-search-input" class="form-control border-0 shadow-none bg-transparent px-2 live-search-input" placeholder="Tìm kiếm mỹ phẩm, chăm sóc da..." value="{{ request('search') }}" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="search-suggestions" aria-haspopup="listbox">
                    </div>
                    
                    <!-- Khung Dropdown chứa kết quả gợi ý -->
                    <div id="search-suggestions" class="position-absolute w-100 bg-white shadow-lg rounded-4 d-none search-suggestions" role="listbox" aria-label="Gợi ý tìm kiếm">
                        <!-- Kết quả JS sẽ đổ vào đây -->
                    </div>
                </form>

                <!-- CỤM BÊN PHẢI: TÀI KHOẢN & GIỎ HÀNG -->
                <ul class="navbar-nav ms-auto align-items-center">
                    @php
                        $isStaffRole = Auth::check() && in_array(Auth::user()->role, ['admin', 'manager', 'warehouse_staff', 'customer_service'], true);
                        if (Auth::check()) {
                            $cartCount = (int) Auth::user()->cartItems()->sum('quantity');
                            $cartDropdownItems = Auth::user()->cartItems()->with('product')->latest()->take(3)->get()->map(function ($item) {
                                return (object)[
                                    'name' => $item->product->name ?? 'Sản phẩm',
                                    'price' => (float)$item->price,
                                    'quantity' => (int)$item->quantity,
                                    'image' => $item->product->image ?? '',
                                ];
                            });
                        } else {
                            $sessionCart = session()->get('cart', []);
                            $cartCount = (int) collect($sessionCart)->sum('quantity');
                            $cartDropdownItems = collect($sessionCart)->take(3)->map(function ($item) {
                                return (object)[
                                    'name' => $item['name'] ?? 'Sản phẩm',
                                    'price' => (float)($item['price'] ?? 0),
                                    'quantity' => (int)($item['quantity'] ?? 1),
                                    'image' => $item['image'] ?? '',
                                ];
                            });
                        }
                    @endphp

                    @if(!$isStaffRole)
                        <!-- GIỎ HÀNG CHUNG (CẢ KHÁCH VÀ THÀNH VIÊN) - OFFCANVAS DRAWER -->
                        <li class="nav-item me-3">
                            <a class="nav-link text-nowrap position-relative fw-semibold d-flex align-items-center" href="{{ route('cart.index') }}" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvasDrawer" role="button" aria-controls="cartOffcanvasDrawer" aria-label="Mở giỏ hàng">
                                <i class="bi bi-cart3 fs-5 me-1"></i> Giỏ hàng
                                <span id="global-cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count-badge {{ $cartCount > 0 ? '' : 'd-none' }}">{{ $cartCount }}</span>
                            </a>
                        </li>
                    @endif

                    @guest
                        <li class="nav-item me-2"><a class="nav-link text-nowrap fw-semibold" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập</a></li>
                        <li class="nav-item"><a class="nav-link text-nowrap fw-semibold" href="{{ route('register') }}"><i class="bi bi-person-plus me-1"></i> Đăng ký</a></li>
                    @else
                        <!-- MENU QUẢN TRỊ (CHỈ HIỂN THỊ VỚI ADMIN) -->
                        @if(in_array(Auth::user()->role, ['admin', 'manager', 'warehouse_staff', 'customer_service'], true))
                            <li class="nav-item dropdown me-4">
                                <a class="nav-link dropdown-toggle text-danger fw-bold text-nowrap" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-shield-lock fs-5 me-1"></i> Quản trị
                                </a>
                                <ul class="dropdown-menu admin-nav-menu border-0 shadow-sm">
                                    @if(in_array(Auth::user()->role, ['admin', 'manager'], true))
                                        <li><div class="admin-menu-label">TỔNG QUAN</div><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 text-primary"></i><span>Dashboard</span></a></li>
                                    @endif
                                    @if(in_array(Auth::user()->role, ['admin', 'manager', 'customer_service'], true))
                                        <li><div class="admin-menu-label">BÁN HÀNG</div><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i class="bi bi-truck text-danger"></i><span>Đơn hàng</span></a></li>
                                    @endif
                                    @if(in_array(Auth::user()->role, ['admin', 'manager'], true))
                                        <li><a class="dropdown-item" href="{{ route('admin.refunds.index') }}"><i class="bi bi-cash-coin text-warning"></i><span>Hoàn tiền</span></a></li>
                                    @endif
                                    @if(in_array(Auth::user()->role, ['admin', 'manager', 'warehouse_staff'], true))
                                        <li><div class="admin-menu-label">SẢN PHẨM & KHO</div><a class="dropdown-item" href="{{ route('admin.products.index') }}"><i class="bi bi-box-seam text-success"></i><span>Sản phẩm & tồn kho</span></a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.inventory-logs.index') }}"><i class="bi bi-clock-history text-primary"></i><span>Lịch sử nhập xuất</span></a></li>
                                    @endif
                                    @if(in_array(Auth::user()->role, ['admin', 'manager'], true))
                                        <li><div class="admin-menu-label">KHÁCH HÀNG & NỘI DUNG</div><a class="dropdown-item" href="{{ route('admin.customers.index') }}"><i class="bi bi-people text-primary"></i><span>Khách hàng</span></a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags text-warning"></i><span>Danh mục</span></a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.vouchers.index') }}"><i class="bi bi-ticket-perforated text-success"></i><span>Voucher</span></a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.posts.index') }}"><i class="bi bi-newspaper text-primary"></i><span>Blog</span></a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.newsletter.index') }}"><i class="bi bi-envelope-paper text-danger"></i><span>Bản tin đăng ký</span></a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.home-banners.index') }}"><i class="bi bi-images text-primary"></i><span>Banner trang chủ</span></a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.index') }}"><i class="bi bi-bar-chart-line text-info"></i><span>Báo cáo</span></a></li>
                                    @endif
                                    @if(in_array(Auth::user()->role, ['admin', 'customer_service'], true))
                                        <li><div class="admin-menu-label">HỖ TRỢ</div><a class="dropdown-item" href="{{ route('admin.chat.index') }}"><i class="bi bi-chat-dots text-info"></i><span>Chat CSKH</span></a></li>
                                    @endif
                                    @if(Auth::user()->role === 'admin')
                                        <li><div class="admin-menu-label">HỆ THỐNG</div><a class="dropdown-item" href="{{ route('admin.staff.index') }}"><i class="bi bi-shield-lock text-danger"></i><span>Nhân viên & phân quyền</span></a></li>
                                    @endif
                                    @if(in_array(Auth::user()->role, ['admin', 'manager'], true))
                                        <li><a class="dropdown-item" href="{{ route('admin.activity-logs.index') }}"><i class="bi bi-clock-history text-secondary"></i><span>Nhật ký hệ thống</span></a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @php
                            $isStaffAccount = in_array(Auth::user()->role, ['admin', 'manager', 'warehouse_staff', 'customer_service'], true);
                            $unreadAccountNotifications = Auth::user()->unreadNotifications()->latest()->limit(5)->get();
                            $notificationIndexRoute = $isStaffAccount ? route('admin.notifications.index') : route('account.notifications');
                        @endphp
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link position-relative fw-semibold" href="{{ $notificationIndexRoute }}"
                               data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo">
                                <i class="bi bi-bell-fill fs-5 text-warning {{ Auth::user()->unreadNotifications()->exists() ? 'bell-ring' : '' }}"></i>
                                @if(Auth::user()->unreadNotifications()->exists())
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-pulse">
                                        {{ Auth::user()->unreadNotifications()->count() > 99 ? '99+' : Auth::user()->unreadNotifications()->count() }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm layout-inline-1">
                                <li><h6 class="dropdown-header">{{ $isStaffAccount ? 'Thông báo quản trị' : 'Thông báo của bạn' }}</h6></li>
                                @forelse($unreadAccountNotifications as $notification)
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ $isStaffAccount ? route('admin.notifications.read', $notification->id) : route('account.notifications.read', $notification->id) }}">
                                            <strong class="d-block small">{{ $notification->data['title'] ?? 'Thông báo mới' }}</strong>
                                            <span class="text-muted small">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 70) }}</span>
                                        </a>
                                    </li>
                                @empty
                                    <li><span class="dropdown-item-text small text-muted">Chưa có thông báo mới.</span></li>
                                @endforelse
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center small" href="{{ $notificationIndexRoute }}">Xem tất cả thông báo</a></li>
                            </ul>
                        </li>



                        <!-- USER PROFILE -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle active text-nowrap fw-bold" href="#" data-bs-toggle="dropdown">
                                @if(Auth::user()->avatar_path)
                                    <img src="{{ Storage::url(Auth::user()->avatar_path) }}" alt="Ảnh đại diện" class="rounded-circle me-1 layout-inline-2">
                                @else
                                    <i class="bi bi-person-circle fs-5 me-1 text-primary"></i>
                                @endif
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                <li><a class="dropdown-item fw-bold py-2" href="{{ route('account') }}"><i class="bi bi-person-vcard text-primary me-2"></i> Tài khoản của tôi</a></li>
                                <li><a class="dropdown-item fw-bold py-2" href="{{ route('orders.index') }}"><i class="bi bi-receipt text-dark me-2"></i> Đơn hàng của tôi</a></li>
                                <li><a class="dropdown-item fw-bold py-2" href="{{ route('wishlist.index') }}"><i class="bi bi-heart text-danger me-2"></i> Sản phẩm yêu thích</a></li>
                                <li><a class="dropdown-item fw-bold py-2" href="{{ route('refunds.index') }}"><i class="bi bi-arrow-counterclockwise text-success me-2"></i> Tiền hoàn của tôi</a></li>
                                @if(Auth::user()->role !== 'admin')
                                    <li><a class="dropdown-item fw-bold py-2" href="{{ route('loyalty.index') }}"><i class="bi bi-stars text-warning me-2"></i> Điểm thành viên</a></li>
                                @endif
                                <li><a class="dropdown-item fw-bold py-2" href="{{ route('password.change') }}"><i class="bi bi-key text-warning me-2"></i> Đổi mật khẩu</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold py-2 border-0 bg-transparent w-100 text-start">
                                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest

                    <!-- THEME TOGGLE (NAVBAR) -->
                    <li class="nav-item ms-2 d-flex align-items-center">
                        <button type="button" id="global-theme-toggle" class="btn rounded-circle shadow-sm layout-inline-3" aria-label="Sáng/Tối" title="Chuyển chế độ Sáng / Tối">
                            ☀️
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @if(!request()->is('admin*') && !request()->routeIs('login', 'register', 'password.*', 'verification.*', 'cart.*', 'checkout', 'orders.*'))
        <div class="home-side-promotions" aria-label="Ưu đãi nổi bật">
            <aside class="home-side-rail home-side-rail-left">
                <a href="{{ route('products.index') }}" class="side-promo-card side-promo-pink" aria-label="Xem sản phẩm skincare giảm 20 phần trăm">
                    <span>SKINCARE</span><strong>GIẢM 20%</strong><small>Cho đơn từ 399K</small><b>NHẬN NGAY <i class="bi bi-arrow-up-right"></i></b>
                </a>
                <a href="{{ route('products.index') }}" class="side-promo-card side-promo-teal" aria-label="Mua sắm sản phẩm được miễn phí vận chuyển">
                    <span>BEATYCARE 🌸</span><strong>FREESHIP</strong><small>Toàn quốc từ 299K</small><b>MUA SẮM <i class="bi bi-arrow-up-right"></i></b>
                </a>
            </aside>
            <aside class="home-side-rail home-side-rail-right">
                <a href="{{ route('products.index') }}" class="side-promo-card side-promo-pink" aria-label="Xem deal mỹ phẩm hôm nay">
                    <span>DEAL HÔM NAY</span><strong>SALE 30%</strong><small>Mỹ phẩm chọn lọc</small><b>XEM DEAL <i class="bi bi-arrow-up-right"></i></b>
                </a>
                @auth
                    <a href="{{ route('account.vouchers') }}" class="side-promo-card side-promo-teal" aria-label="Khám phá voucher và quà tặng">
                @else
                    <a href="{{ route('login') }}" class="side-promo-card side-promo-teal" aria-label="Đăng nhập để khám phá voucher và quà tặng">
                @endauth
                    <span>QUÀ XINH</span><strong>TẶNG QUÀ</strong><small>Đơn càng lớn, quà càng xinh</small><b>KHÁM PHÁ <i class="bi bi-arrow-up-right"></i></b>
                </a>
            </aside>
        </div>
    @endif

    <!-- NỘI DUNG CHÍNH -->
    <main class="container py-4 flex-grow-1" id="main-content">
        @yield('content')
    </main>

    <!-- CHÂN TRANG (FOOTER) -->
    <footer class="glass-footer mt-5">
        <div class="container">
            <div class="footer-promise-row">
                <div class="footer-promise"><i class="bi bi-patch-check-fill"></i><div><strong>Chính hãng chọn lọc</strong><small>Nguồn gốc rõ ràng</small></div></div>
                <div class="footer-promise"><i class="bi bi-box-seam-fill"></i><div><strong>Đóng gói cẩn thận</strong><small>Giao hàng toàn quốc</small></div></div>
                <div class="footer-promise"><i class="bi bi-headset"></i><div><strong>Tư vấn tận tâm</strong><small>Hỗ trợ 08:00 - 22:00</small></div></div>
            </div>

            <div class="row gy-5 footer-main-row reveal-up">
                <div class="col-xl-4 col-lg-5 col-md-6">
                    <div class="footer-brand-lockup"><span class="footer-brand-mark"><i class="bi bi-flower1"></i></span><div><strong>Aloha Beauty</strong><small>Beauty made personal</small></div></div>
                    <p class="footer-about">Mỹ phẩm và sản phẩm chăm sóc cá nhân được chọn lọc để bạn tự tin xây dựng khoảnh khắc self-care của riêng mình.</p>
                    <div class="footer-contact-list">
                        <a href="https://maps.google.com/?q=Cầu+Giấy,+Hà+Nội" target="_blank"><i class="bi bi-geo-alt-fill"></i><span>Cầu Giấy, Hà Nội</span></a>
                        <a href="tel:0338054668"><i class="bi bi-telephone-fill"></i><span>0338 054 668 <small>• Hotline tư vấn</small></span></a>
                        <a href="mailto:contact@phungthanhtuc.com"><i class="bi bi-envelope-fill"></i><span>contact@phungthanhtuc.com</span></a>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-xl-2">
                    <h6 class="footer-column-title">Mua sắm</h6>
                    <ul class="list-unstyled footer-menu">
                        <li><a href="{{ route('products.index') }}">Tất cả sản phẩm</a></li>
                        <li><a href="{{ route('products.index', ['category' => 1]) }}">Chăm sóc da</a></li>
                        <li><a href="{{ route('products.index', ['category' => 2]) }}">Trang điểm</a></li>
                        <li><a href="{{ route('products.index', ['category' => 3]) }}">Chăm sóc tóc</a></li>
                        <li><a href="{{ route('products.index', ['category' => 4]) }}">Chăm sóc cá nhân</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3 col-xl-2">
                    <h6 class="footer-column-title">Hỗ trợ</h6>
                    <ul class="list-unstyled footer-menu">
                        <li><a href="{{ route('cart.index') }}">Giỏ hàng của bạn</a></li>
                        <li><a href="{{ route('orders.track') }}">Theo dõi đơn hàng</a></li>
                        <li><a href="{{ route('pages.policies') }}">Đổi trả & hoàn tiền</a></li>
                        <li><a href="{{ route('pages.policies') }}">Giao hàng & thanh toán</a></li>
                        <li><a href="{{ route('pages.contact') }}">Liên hệ Aloha</a></li>
                        <li><a href="{{ route('pages.faq') }}">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>

                <div class="col-xl-4 col-lg-12 col-md-6">
                    <div class="footer-connect-panel">
                        <span class="footer-panel-kicker">STAY IN THE GLOW</span>
                        <h6>Đừng bỏ lỡ những ưu đãi xinh xắn</h6>
                        <p class="small text-muted mb-3">Nhận ngay voucher ưu đãi 10% và cập nhật bí quyết chăm sóc da từ chuyên gia Aloha Beauty.</p>
                        
                        <!-- Form Đăng ký bản tin với Honeypot chống Bot (Prompt 3.6) -->
                        <form id="footer-newsletter-form" class="mb-3" onsubmit="handleNewsletterSubscribe(event)">
                            @csrf
                            <input type="text" name="hp_email" class="layout-inline-4" tabindex="-1" autocomplete="off">
                            <div class="input-group">
                                <input type="email" id="newsletter-email" name="email" class="form-control rounded-start-pill border-0 ps-3" placeholder="Nhập email của bạn..." required>
                                <button class="btn btn-primary rounded-end-pill px-3" type="submit" id="btn-newsletter-submit" title="Đăng ký">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </form>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <a href="https://www.facebook.com/aimachan205/" target="_blank" class="footer-social footer-social-facebook" title="Facebook"><i class="bi bi-facebook social-icon-spin"></i><span>Facebook</span></a>
                            <a href="https://www.youtube.com/@VanTu-vp6yn" target="_blank" class="footer-social footer-social-youtube" title="YouTube"><i class="bi bi-youtube social-icon-spin"></i><span>YouTube</span></a>
                            <a href="https://twitter.com/" target="_blank" class="footer-social footer-social-x" title="X"><i class="bi bi-twitter-x social-icon-spin"></i><span>X</span></a>
                        </div>
                        <div class="footer-payment-line"><span>Thanh toán an toàn</span><div><i class="bi bi-credit-card-2-front"></i><i class="bi bi-cash-coin"></i><i class="bi bi-qr-code-scan"></i></div></div>
                    </div>
                </div>
            </div>

            <div id="footer-policies" class="footer-policy-strip"><span><i class="bi bi-shield-check me-1"></i> Bảo mật thông tin khách hàng</span><span><i class="bi bi-arrow-repeat me-1"></i> Hỗ trợ đổi trả minh bạch</span><span><i class="bi bi-heart-fill me-1"></i> Chăm sóc bạn bằng sự tử tế</span></div>
            <div class="footer-bottom"><span>&copy; {{ date('Y') }} Aloha Beauty. All rights reserved.</span><span>Made with care in Hà Nội, Việt Nam.</span></div>
        </div>
    </footer>

    <div class="modal fade" id="supportPolicyModal" tabindex="-1" aria-labelledby="supportPolicyTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="supportPolicyTitle">Chính sách hỗ trợ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body pt-0" id="supportPolicyContent"></div>
                <div class="modal-footer border-0"><a href="tel:0338054668" class="btn btn-primary rounded-pill"><i class="bi bi-telephone me-1"></i>Gọi tư vấn</a></div>
            </div>
        </div>
    </div>

    

            <audio id="ting-sound" src="https://actions.google.com/sounds/v1/communications/incoming_message.ogg" preload="auto" class="d-none"></audio>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <!-- SCRIPT TÌM KIẾM TRỰC TIẾP (LIVE SEARCH) HOẠT ĐỘNG TOÀN CỤC -->
    

    <!-- ============================================================== -->
    <!-- LOGIC TỰ ĐỘNG PHÂN LUỒNG CHAT DỰA TRÊN ROLE CỦA USER ĐĂNG NHẬP -->
    <!-- ============================================================== -->
    @auth
        @if(Auth::user()->role === 'admin')
            @if(!request()->routeIs('admin.chat.index') && !request()->routeIs('welcome'))
                <a class="storefront-admin-chat-dock" href="{{ route('admin.chat.index') }}" title="Mở trung tâm chat khách hàng">
                    <i class="bi bi-chat-square-text-fill"></i><span>Chat hỗ trợ</span><b id="storefront-chat-badge" class="storefront-chat-badge d-none">0</b>
                </a>
                
                
            @endif
        @else
            <!-- KHUNG CHAT MÀU XANH DÀNH CHO KHÁCH HÀNG -->
            <div id="chat-widget-button" class="shadow-lg chat-widget-button chat-widget-button-user">
                <i class="bi bi-chat-dots-fill text-white"></i><span>Hỗ trợ</span>
                <span id="chat-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white d-none chat-badge">0</span>
            </div>

            <!-- ĐÃ THÊM CSS FIX LỖI KHUẤT NÚT CHO KHÁCH HÀNG Ở ĐÂY -->
            

            <div id="chat-widget-window" class="shadow-lg border-0 d-none chat-widget-window">
                <div class="p-3 text-white d-flex justify-content-between align-items-center chat-header-user">
                    <div><h6 class="mb-0 fw-bold"><i class="bi bi-headset me-2"></i>Hỗ trợ trực tuyến</h6><small id="support-presence" class="opacity-75">Đang kiểm tra trạng thái...</small></div>
                    <i class="bi bi-x-lg chat-close-button" id="close-chat"></i>
                </div>
                
                <div id="chat-messages" class="p-3 flex-grow-1 chat-messages"></div>
                
                <div class="p-3 border-top chat-input-area">
                    <div class="d-flex gap-1 mb-2 chat-topic-suggestions">
                        <button type="button" class="btn btn-sm btn-light border rounded-pill chat-topic" data-topic="Tôi muốn được tư vấn sản phẩm phù hợp">Tư vấn sản phẩm</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill chat-topic" data-topic="Tôi muốn kiểm tra tình trạng đơn hàng">Kiểm tra đơn hàng</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill chat-topic" data-topic="Tôi cần hỗ trợ đổi trả sản phẩm">Đổi trả</button>
                    </div>
                    <div id="customer-attachment-preview" class="small text-muted mb-2"></div>
                    <div class="input-group">
                        <label class="btn btn-light border rounded-circle me-1" title="Gửi ảnh"><i class="bi bi-image"></i><input type="file" id="customer-attachment" accept="image/*" hidden></label>
                        <button type="button" class="btn btn-light border rounded-circle me-1" id="customer-emoji" title="Thêm biểu tượng"><i class="bi bi-emoji-smile"></i></button>
                        <input type="text" id="btn-input" class="form-control rounded-pill me-2 border-0 shadow-sm" placeholder="Nhập tin nhắn...">
                        <button class="btn btn-primary rounded-circle shadow-sm chat-send-button" id="btn-chat"><i class="bi bi-send-fill"></i></button>
                    </div>
                </div>
            </div>

            
        @endif
    @endauth
    @auth
        
    @endauth
    @stack('scripts')
    @include('components.product-advisor')
    
    <!-- GLOBAL UI ELEMENTS: CIRCULAR PROGRESS BACK TO TOP & TOAST -->
    <button id="back-to-top" class="back-to-top-btn" aria-label="Lên đầu trang" title="Cuộn lên đầu trang">
        <svg class="progress-ring" width="48" height="48" viewBox="0 0 48 48">
            <circle class="progress-ring-bg" stroke="rgba(255, 107, 129, 0.2)" stroke-width="3" fill="transparent" r="20" cx="24" cy="24" />
            <circle id="scroll-progress-circle" class="progress-ring-circle" stroke="url(#progress-ring-gradient)" stroke-width="3" stroke-linecap="round" fill="transparent" r="20" cx="24" cy="24" />
            <defs>
                <linearGradient id="progress-ring-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#ff6b81" />
                    <stop offset="100%" stop-color="#a18cd1" />
                </linearGradient>
            </defs>
        </svg>
        <i class="bi bi-arrow-up"></i>
    </button>
    <div id="toast-container"></div>
    
    @include('components.mini-cart-drawer')
    @include('components.quick-view-modal')
    @include('components.compare-floating-dock')

    <script src="{{ asset_v('js/animations.js') }}" defer></script>
    <script src="{{ asset_v('js/interactions.js') }}" defer></script>
    <script src="{{ asset_v('js/inline-assets.js') }}" defer></script>
    <script src="{{ asset_v('js/cart-drawer.js') }}" defer></script>
    
    <!-- Toggles moved to navbar -->
    
</body>
</html>