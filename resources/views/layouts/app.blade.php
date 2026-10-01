<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
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
    @stack('styles')
    @yield('structured_data')
    @stack('schema')
    <script>
        if (localStorage.getItem('beatycare-theme') === 'dark') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
</head>
<body class="{{ request()->routeIs('login', 'register', 'password.request', 'password.reset', 'verification.notice') ? 'auth-page' : '' }}">
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
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm" style="min-width: 300px;">
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

                        <!-- GIỎ HÀNG CHUNG: không hiển thị trong khu vực quản trị -->
                        @if(!in_array(Auth::user()->role, ['admin', 'manager', 'warehouse_staff', 'customer_service'], true))
                            <li class="nav-item me-4 dropdown cart-dropdown">
                                <a class="nav-link text-nowrap position-relative fw-semibold" href="{{ route('cart.index') }}">
                                    <i class="bi bi-cart3 fs-5 me-1"></i> Giỏ hàng
                                    @php
                                        $cartCount = Auth::user()->cartItems()->sum('quantity');
                                        $cartItems = Auth::user()->cartItems()->with('product')->latest()->take(3)->get();
                                    @endphp
                                    @if($cartCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count-badge">{{ $cartCount }}</span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm p-3 mini-cart-dropdown" style="min-width: 320px;">
                                    <li><h6 class="dropdown-header px-0 text-dark fw-bold">Giỏ hàng của bạn</h6></li>
                                    @if($cartCount > 0)
                                        @foreach($cartItems as $item)
                                            <li class="d-flex align-items-center mb-3">
                                                <img src="{{ asset('storage/' . ($item->product->image ?? '')) }}" class="rounded me-3 object-fit-cover" width="50" height="50" alt="{{ $item->product->name ?? 'Sản phẩm' }}" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                                                <div class="flex-grow-1">
                                                    <div class="small fw-semibold text-truncate" style="max-width: 180px;">{{ $item->product->name ?? 'Sản phẩm' }}</div>
                                                    <div class="small text-muted">{{ number_format($item->price, 0, ',', '.') }} đ x {{ $item->quantity }}</div>
                                                </div>
                                            </li>
                                        @endforeach
                                        @if($cartCount > 3)
                                            <li class="text-center small text-muted mb-2">Và {{ $cartCount - 3 }} sản phẩm khác...</li>
                                        @endif
                                        <li><a href="{{ route('cart.index') }}" class="btn btn-primary w-100 btn-sm btn-nhan-ngay">Xem giỏ hàng</a></li>
                                    @else
                                        <li><span class="small text-muted">Chưa có sản phẩm nào.</span></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        <!-- USER PROFILE -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle active text-nowrap fw-bold" href="#" data-bs-toggle="dropdown">
                                @if(Auth::user()->avatar_path)
                                    <img src="{{ Storage::url(Auth::user()->avatar_path) }}" alt="Ảnh đại diện" class="rounded-circle me-1" style="width:32px;height:32px;object-fit:cover;">
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
                        <button type="button" id="global-theme-toggle" class="btn rounded-circle shadow-sm" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(150,150,150,0.3); font-size: 1.1rem; background: rgba(255,255,255,0.8); color: #333; transition: all 0.3s;" aria-label="Sáng/Tối" title="Chuyển chế độ Sáng / Tối">
                            ☀️
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @if(!request()->is('admin*') && !request()->routeIs('login', 'register', 'password.*', 'verification.*'))
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
                        <li><a href="{{ route('orders.index') }}">Theo dõi đơn hàng</a></li>
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
                        <p>Follow Aloha Beauty để cập nhật sản phẩm mới và tips chăm sóc bản thân.</p>
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

    <script>
        document.getElementById('supportPolicyModal')?.addEventListener('show.bs.modal', function (event) {
            const policy = event.relatedTarget?.dataset.policy;
            const title = document.getElementById('supportPolicyTitle');
            const content = document.getElementById('supportPolicyContent');
            if (policy === 'returns') {
                title.textContent = 'Đổi trả & hoàn tiền';
                content.innerHTML = '<p class="text-muted">Aloha Beauty hỗ trợ đổi trả trong 7 ngày nếu sản phẩm bị lỗi, giao sai hoặc hư hỏng khi nhận.</p><ul class="text-muted ps-3"><li>Giữ nguyên sản phẩm, hộp và phụ kiện.</li><li>Gửi ảnh/video tình trạng sản phẩm qua chat hỗ trợ.</li><li>Thời gian xử lý: 2-3 ngày làm việc sau khi nhận đủ thông tin.</li><li>Hoàn tiền về phương thức thanh toán ban đầu sau khi xác nhận.</li></ul>';
            } else {
                title.textContent = 'Giao hàng & thanh toán';
                content.innerHTML = '<p class="text-muted">Aloha Beauty giao hàng toàn quốc và đóng gói cẩn thận để sản phẩm đến bạn an toàn.</p><ul class="text-muted ps-3"><li>Thời gian dự kiến: 2-5 ngày làm việc.</li><li>Thanh toán COD khi nhận hàng.</li><li>Thanh toán chuyển khoản nhanh qua PayOS.</li><li>Địa chỉ giao hàng có thể được ghim chính xác trên bản đồ lúc đặt hàng.</li></ul>';
            }
        });
    </script>

            <audio id="ting-sound" src="https://actions.google.com/sounds/v1/communications/incoming_message.ogg" preload="auto" class="d-none"></audio>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <!-- SCRIPT TÌM KIẾM TRỰC TIẾP (LIVE SEARCH) HOẠT ĐỘNG TOÀN CỤC -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('live-search-input');
        const suggestionsBox = document.getElementById('search-suggestions');

        if (!searchInput || !suggestionsBox) return;

        let debounceTimer = null;
        let abortController = null;
        let selectedIndex = -1;

        // Hàm escape HTML an toàn chống XSS
        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Lấy danh sách tất cả các item có thể tương tác bằng bàn phím
        function getFocusableItems() {
            return Array.from(suggestionsBox.querySelectorAll('.search-nav-item'));
        }

        function setAriaExpanded(expanded) {
            searchInput.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            if (!expanded) {
                searchInput.removeAttribute('aria-activedescendant');
            }
        }

        function hideSuggestions() {
            if (abortController) {
                abortController.abort();
                abortController = null;
            }
            clearTimeout(debounceTimer);
            suggestionsBox.classList.add('d-none');
            setAriaExpanded(false);
            selectedIndex = -1;
            removeActiveState();
        }

        function removeActiveState() {
            const items = getFocusableItems();
            items.forEach(el => el.classList.remove('active'));
        }

        function updateActiveItem(index) {
            const items = getFocusableItems();
            if (items.length === 0) return;

            items.forEach((item, i) => {
                if (i === index) {
                    item.classList.add('active');
                    item.scrollIntoView({ block: 'nearest' });
                    if (item.id) {
                        searchInput.setAttribute('aria-activedescendant', item.id);
                    }
                } else {
                    item.classList.remove('active');
                }
            });
        }

        // Đóng gợi ý khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                hideSuggestions();
            }
        });

        // Mở lại gợi ý nếu input có sẵn nội dung và focus vào
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 1 && suggestionsBox.innerHTML.trim() !== '') {
                suggestionsBox.classList.remove('d-none');
                setAriaExpanded(true);
            }
        });

        // Bắt sự kiện phím điều hướng (ArrowDown, ArrowUp, Enter, Escape)
        searchInput.addEventListener('keydown', function(e) {
            const items = getFocusableItems();
            const isOpen = !suggestionsBox.classList.contains('d-none') && items.length > 0;

            if (e.key === 'Escape') {
                if (!suggestionsBox.classList.contains('d-none')) {
                    e.preventDefault();
                    hideSuggestions();
                }
                return;
            }

            if (!isOpen) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) >= items.length ? 0 : (selectedIndex + 1);
                updateActiveItem(selectedIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1) < 0 ? (items.length - 1) : (selectedIndex - 1);
                updateActiveItem(selectedIndex);
            } else if (e.key === 'Enter') {
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    e.preventDefault();
                    items[selectedIndex].click();
                }
            }
        });

        // Xử lý tìm kiếm với Debounce 250ms & AbortController
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 1) {
                hideSuggestions();
                suggestionsBox.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                fetch(`/search-suggestions?query=${encodeURIComponent(query)}`, {
                    signal: abortController.signal,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    selectedIndex = -1;
                    const hasProducts = Array.isArray(data.products) && data.products.length > 0;
                    const hasCategories = Array.isArray(data.categories) && data.categories.length > 0;
                    const hasKeywords = Array.isArray(data.keywords) && data.keywords.length > 0;

                    if (hasProducts || hasCategories || hasKeywords) {
                        let html = '';
                        let itemIndex = 0;

                        if (hasProducts) {
                            html += '<div class="search-suggestion-group" role="group" aria-label="Sản phẩm"><div class="search-suggestion-title">Sản phẩm</div><ul class="list-unstyled mb-0">';
                            data.products.forEach(item => {
                                const itemId = `search-item-${itemIndex++}`;
                                const safeName = escapeHtml(item.name);
                                const safePrice = escapeHtml(item.formatted_price);
                                const safeUrl = escapeHtml(item.detail_url);
                                const imgHtml = item.image_url 
                                    ? `<img src="${escapeHtml(item.image_url)}" class="me-3 rounded search-result-image" alt="${safeName}">`
                                    : `<div class="d-flex align-items-center justify-content-center bg-light rounded me-3 search-result-image"><i class="bi bi-box text-muted"></i></div>`;
                                
                                html += `
                                <li>
                                    <a href="${safeUrl}" id="${itemId}" role="option" class="d-flex align-items-center px-3 py-2 text-decoration-none text-dark search-item-hover search-nav-item">
                                        ${imgHtml}
                                        <div>
                                            <div class="fw-bold fs-6 text-truncate search-result-name">${safeName}</div>
                                            <div class="text-danger small fw-semibold">${safePrice}</div>
                                        </div>
                                    </a>
                                </li>`;
                            });
                            html += '</ul></div>';
                        }

                        if (hasCategories) {
                            html += '<div class="search-suggestion-group" role="group" aria-label="Danh mục"><div class="search-suggestion-title">Danh mục</div><ul class="list-unstyled mb-0">';
                            data.categories.forEach(item => {
                                const itemId = `search-item-${itemIndex++}`;
                                const safeName = escapeHtml(item.name);
                                const safeUrl = escapeHtml(item.url);
                                const count = Number(item.count) || 0;
                                html += `<li><a href="${safeUrl}" id="${itemId}" role="option" class="search-related-link search-nav-item"><i class="bi bi-grid-3x3-gap me-2"></i><span>${safeName}</span><small>${count} sản phẩm</small></a></li>`;
                            });
                            html += '</ul></div>';
                        }

                        if (hasKeywords) {
                            html += '<div class="search-suggestion-group" role="group" aria-label="Từ khóa liên quan"><div class="search-suggestion-title">Từ khóa liên quan</div><div class="search-related-keywords">';
                            data.keywords.forEach(keyword => {
                                const itemId = `search-item-${itemIndex++}`;
                                const safeKeyword = escapeHtml(keyword);
                                const searchParam = encodeURIComponent(keyword);
                                html += `<a href="/products?search=${searchParam}" id="${itemId}" role="option" class="search-keyword-chip search-nav-item">${safeKeyword}</a>`;
                            });
                            html += '</div></div>';
                        }

                        suggestionsBox.innerHTML = html;
                        suggestionsBox.classList.remove('d-none');
                        setAriaExpanded(true);

                        // Đồng bộ chuột hover với selectedIndex
                        getFocusableItems().forEach((el, idx) => {
                            el.addEventListener('mouseenter', () => {
                                selectedIndex = idx;
                                updateActiveItem(selectedIndex);
                            });
                        });
                    } else {
                        suggestionsBox.innerHTML = '<div class="p-3 text-center text-muted small" role="status"><i class="bi bi-emoji-frown me-1"></i> Không tìm thấy sản phẩm</div>';
                        suggestionsBox.classList.remove('d-none');
                        setAriaExpanded(true);
                    }
                })
                .catch(error => {
                    if (error.name === 'AbortError') return;
                    suggestionsBox.innerHTML = '<div class="p-3 text-center text-danger small" role="alert"><i class="bi bi-exclamation-circle me-1"></i> Không thể tìm kiếm, thử lại sau</div>';
                    suggestionsBox.classList.remove('d-none');
                    setAriaExpanded(true);
                });
            }, 250);
        });
    });
    </script>

    <!-- ============================================================== -->
    <!-- LOGIC TỰ ĐỘNG PHÂN LUỒNG CHAT DỰA TRÊN ROLE CỦA USER ĐĂNG NHẬP -->
    <!-- ============================================================== -->
    @auth
        @if(Auth::user()->role === 'admin')
            @if(!request()->routeIs('admin.chat.index') && !request()->routeIs('welcome'))
                <a class="storefront-admin-chat-dock" href="{{ route('admin.chat.index') }}" title="Mở trung tâm chat khách hàng">
                    <i class="bi bi-chat-square-text-fill"></i><span>Chat hỗ trợ</span><b id="storefront-chat-badge" class="storefront-chat-badge d-none">0</b>
                </a>
                <style>
                    /* ĐÃ SỬA Z-INDEX CHO ADMIN Ở ĐÂY LÊN 99999 */
                    .storefront-admin-chat-dock { position: fixed; z-index: 10001 !important; right: 1rem; bottom: 1rem; min-height: 44px; box-sizing: border-box; display: inline-flex; align-items: center; gap: .55rem; padding: .58rem .82rem; color: #fff; text-decoration: none; background: linear-gradient(135deg, #183b56, #1686a0); border-radius: 999px; box-shadow: 0 8px 22px rgba(24,59,86,.24); transition: transform .2s, box-shadow .2s; }
                    .storefront-admin-chat-dock:hover { color: #fff; transform: translateY(-3px); box-shadow: 0 12px 28px rgba(24,59,86,.32); }
                    @media (max-width: 1100px) { .storefront-admin-chat-dock { right: .75rem; bottom: .75rem; width: 44px; height: 44px; padding: 0; justify-content: center; } .storefront-admin-chat-dock span { display: none; } .storefront-admin-chat-dock i { margin: 0; } }
                    .storefront-admin-chat-dock i { font-size: 1.15rem; }
                    .storefront-admin-chat-dock span { font-size: .78rem; font-weight: 800; }
                    .storefront-admin-chat-dock .storefront-chat-badge { position: absolute; top: -7px; left: -7px; min-width: 20px; padding: .2rem .35rem; color: #fff; background: #e63950; border: 2px solid #fff; border-radius: 999px; font-size: .65rem; text-align: center; }
                </style>
                <script>
                    (function () {
                        const badge = document.getElementById('storefront-chat-badge');
                        const refreshChatBadge = () => fetch('/chat/users').then(response => response.json()).then(users => {
                            const total = users.reduce((sum, user) => sum + Number(user.unread_messages_count || 0), 0);
                            badge.textContent = total > 99 ? '99+' : total;
                            badge.classList.toggle('d-none', total === 0);
                        }).catch(() => {});
                        refreshChatBadge();
                        setInterval(refreshChatBadge, 15000);
                    })();
                </script>
            @endif
        @else
            <!-- KHUNG CHAT MÀU XANH DÀNH CHO KHÁCH HÀNG -->
            <div id="chat-widget-button" class="shadow-lg chat-widget-button chat-widget-button-user">
                <i class="bi bi-chat-dots-fill text-white"></i><span>Hỗ trợ</span>
                <span id="chat-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white d-none chat-badge">0</span>
            </div>

            <!-- ĐÃ THÊM CSS FIX LỖI KHUẤT NÚT CHO KHÁCH HÀNG Ở ĐÂY -->
            <style>
                .chat-widget-button {
                    position: fixed !important;
                    right: 24px !important;
                    bottom: 24px !important;
                    z-index: 99999 !important;
                }
                @media (max-width: 576px) {
                    .chat-widget-button {
                        right: 16px !important;
                        bottom: 16px !important;
                    }
                }
                .chat-widget-window {
                    z-index: 999999 !important;
                }
            </style>

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

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const chatBtn = document.getElementById('chat-widget-button');
                const chatWin = document.getElementById('chat-widget-window');
                const chatBadge = document.getElementById('chat-badge');
                const tingSound = document.getElementById('ting-sound');
                
                let currentUserId = {{ Auth::id() ?? 'null' }};
                let unreadCount = 0;
                const customerAttachment = document.getElementById('customer-attachment');
                const customerAttachmentPreview = document.getElementById('customer-attachment-preview');

                chatBtn.addEventListener('click', () => {
                    chatWin.classList.toggle('d-none');
                    if(!chatWin.classList.contains('d-none')) { 
                        loadMessages(); 
                        updateSupportPresence();
                        chatBtn.style.transform = 'scale(0)'; 
                        unreadCount = 0;
                        chatBadge.classList.add('d-none');
                        chatBadge.innerText = '0';
                    }
                });
                
                document.getElementById('close-chat').addEventListener('click', () => {
                    chatWin.classList.add('d-none'); chatBtn.style.transform = 'scale(1)';
                });

                function loadMessages() {
                    fetch('/chat/messages').then(res => res.json()).then(data => {
                        document.getElementById('chat-messages').innerHTML = '';
                        if (data.length === 0) appendMessage({ is_admin: 1, message: 'Xin chào! Aloha Beauty có thể hỗ trợ bạn điều gì hôm nay?' });
                        data.forEach(msg => appendMessage(msg));
                        scrollToBottom();
                    });
                }

                function updateSupportPresence() {
                    fetch('/chat/presence').then(res => res.json()).then(status => {
                        const target = document.getElementById('support-presence');
                        if (!target) return;
                        target.innerHTML = status.online ? '<i class="bi bi-circle-fill text-success me-1"></i>Đang online' : (status.last_seen_minutes === null ? 'Chưa hoạt động' : 'Hoạt động ' + status.last_seen_minutes + ' phút trước');
                    });
                }

                function appendMessage(msg) {
                    let isMine = msg.is_admin == 0;
                    let attachment = msg.attachment_url ? `<img src="${msg.attachment_url}" alt="Ảnh đính kèm" style="max-width:180px;max-height:120px;border-radius:10px;display:block;margin-top:6px">` : '';
                    let html = `<div class="w-100 mb-2 chat-message-row">
                                    ${!isMine ? '<small class="d-block text-muted mb-1 chat-sender-label">Admin Shop</small>' : ''}
                                    <div class="msg-bubble ${isMine ? 'msg-mine' : 'msg-other'}">${msg.message || ''}${attachment}</div>
                                </div>`;
                    document.getElementById('chat-messages').insertAdjacentHTML('beforeend', html);
                    scrollToBottom();
                }

                function scrollToBottom() { let box = document.getElementById('chat-messages'); box.scrollTop = box.scrollHeight; }

                function sendMessage() {
                    let text = document.getElementById('btn-input').value.trim();
                    if(!text && !customerAttachment.files.length) return;
                    document.getElementById('btn-input').value = '';
                    const formData = new FormData();
                    formData.append('message', text);
                    if (customerAttachment.files[0]) formData.append('attachment', customerAttachment.files[0]);
                    fetch('/chat/message', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    }).then(response => response.json()).then(sent => { appendMessage({...sent.message, is_admin: 0, message: text}); });
                    customerAttachment.value = '';
                    customerAttachmentPreview.innerHTML = '';
                }

                customerAttachment.addEventListener('change', () => {
                    const file = customerAttachment.files[0];
                    customerAttachmentPreview.textContent = file ? 'Đã chọn: ' + file.name : '';
                });
                document.querySelectorAll('.chat-topic').forEach(button => button.addEventListener('click', () => {
                    document.getElementById('btn-input').value = button.dataset.topic;
                    document.getElementById('btn-input').focus();
                }));
                document.getElementById('customer-emoji').addEventListener('click', () => {
                    document.getElementById('btn-input').value += ' 😊';
                    document.getElementById('btn-input').focus();
                });

                document.getElementById('btn-chat').addEventListener('click', sendMessage);
                document.getElementById('btn-input').addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });

                var pusher = new Pusher('c7b756312af017cea0f9', { cluster: 'ap1' });
                pusher.subscribe('chat-channel').bind('message.sent', function(data) {
                    if(data.message.user_id == currentUserId && data.message.is_admin == 1) {
                        tingSound.currentTime = 0;
                        tingSound.play().catch(e => console.log('Âm thanh chờ tương tác người dùng: ', e));
                        if(!chatWin.classList.contains('d-none')) {
                            appendMessage(data.message);
                        } else {
                            appendMessage(data.message); 
                            
                            unreadCount++;
                            chatBadge.innerText = unreadCount > 99 ? '99+' : unreadCount;
                            chatBadge.classList.remove('d-none');
                            
                            chatBtn.classList.add('animate__animated', 'animate__tada');
                            setTimeout(() => chatBtn.classList.remove('animate__animated', 'animate__tada'), 1000);
                            
                            if ('Notification' in window && Notification.permission === 'granted') {
                                new Notification('Aloha Beauty có tin nhắn mới', {body: data.message.message});
                            }
                        }
                    }
                });
                const heartbeat = () => fetch('/chat/heartbeat', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
                heartbeat();
                setInterval(heartbeat, 60000);
                setInterval(updateSupportPresence, 60000);
            });
            </script>
        @endif
    @endauth
    @auth
        <script>
            (function () {
                const sendPresence = () => fetch('/chat/heartbeat', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).catch(() => {});
                sendPresence();
                setInterval(sendPresence, 60000);
            })();
        </script>
    @endauth
    @stack('scripts')
    @include('components.product-advisor')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('input[type="password"]').forEach(function (input) {
                if (input.parentElement.querySelector('.password-toggle')) return;

                const container = input.parentElement;
                container.classList.add('password-field');

                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'password-toggle';
                button.setAttribute('aria-label', 'Hiện mật khẩu');
                button.innerHTML = '<i class="bi bi-eye"></i>';
                const positionToggle = function () {
                    button.style.top = (input.offsetTop + (input.offsetHeight / 2)) + 'px';
                };
                positionToggle();
                button.addEventListener('click', function () {
                    const visible = input.type === 'text';
                    input.type = visible ? 'password' : 'text';
                    button.setAttribute('aria-label', visible ? 'Hiện mật khẩu' : 'Ẩn mật khẩu');
                    button.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
                });
                container.appendChild(button);
                window.addEventListener('resize', positionToggle);
            });
        });
    </script>
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
    
    <script src="{{ asset_v('js/animations.js') }}" defer></script>
    <script src="{{ asset_v('js/interactions.js') }}" defer></script>
    
    <!-- Toggles moved to navbar -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('global-theme-toggle');
            const body = document.body;
            
            // Khôi phục trạng thái
            const savedTheme = localStorage.getItem('beatycare-theme');
            if (savedTheme === 'dark') {
                body.classList.add('dark-mode');
                document.documentElement.classList.add('dark-mode');
                if (toggleBtn) {
                    toggleBtn.innerHTML = '🌙';
                    toggleBtn.style.background = 'rgba(30, 30, 30, 0.9)';
                    toggleBtn.style.color = 'white';
                }
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    body.classList.toggle('dark-mode');
                    document.documentElement.classList.toggle('dark-mode');
                    const isDark = body.classList.contains('dark-mode');
                    toggleBtn.innerHTML = isDark ? '🌙' : '☀️';
                    localStorage.setItem('beatycare-theme', isDark ? 'dark' : 'light');
                    
                    if (isDark) {
                        toggleBtn.style.background = 'rgba(30, 30, 30, 0.9)';
                        toggleBtn.style.color = 'white';
                    } else {
                        toggleBtn.style.background = 'rgba(255, 255, 255, 0.8)';
                        toggleBtn.style.color = '#333';
                    }
                });
            }
        });
    </script>
</body>
</html>