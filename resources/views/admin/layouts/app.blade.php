<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - BeatyCare 🌸')</title>

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset_v('css/admin-layout.css') }}" rel="stylesheet">
    
</head>
<body class="admin-body">
    <script src="{{ asset_v('js/admin-layout.js') }}" defer></script>

    <!-- Thanh điều hướng Navbar -->
    <nav class="navbar navbar-expand-lg glass-navbar shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <i class="bi bi-flower1"></i> BeatyCare 🌸
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                
                <!-- Menu bên trái -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Trang chủ</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Sản phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Kho (Admin)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Danh mục (Admin)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.posts.*') || request()->routeIs('admin.post-categories.*') ? 'active' : '' }}" href="{{ route('admin.posts.index') }}">Blog (Admin)</a>
                        </li>
                    @endauth
                </ul>

                <!-- Menu bên phải -->
                <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
                        </li>
                    @else
                        <!-- Nút Quản lý Đơn hàng -->
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item me-3">
                                <a class="nav-link text-danger fw-bold" href="{{ route('admin.orders.index') }}">
                                    <i class="bi bi-truck me-1"></i> Đi đơn cho khách
                                </a>
                            </li>
                        @else
                            <li class="nav-item me-3">
                                <a class="nav-link fw-bold admin-order-link" href="{{ route('orders.index') }}">
                                    <i class="bi bi-receipt me-1"></i> Đơn hàng
                                </a>
                            </li>
                        @endif

                        @if(in_array(Auth::user()->role, ['admin', 'manager', 'warehouse_staff'], true))
                            <li class="nav-item dropdown me-2">
                                <a class="nav-link position-relative" href="{{ route('admin.notifications.index') }}"
                                   id="admin-notifications-dropdown" role="button" data-bs-toggle="dropdown"
                                   aria-expanded="false" title="Thông báo từ khách hàng">
                                    <i class="bi bi-bell-fill fs-5"></i>
                                    @if(Auth::user()->unreadNotifications()->exists())
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ Auth::user()->unreadNotifications()->count() > 99 ? '99+' : Auth::user()->unreadNotifications()->count() }}
                                        </span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="admin-notifications-dropdown">
                                    <li><h6 class="dropdown-header">Thông báo khách hàng</h6></li>
                                    @forelse(Auth::user()->unreadNotifications()->latest()->limit(5)->get() as $notification)
                                        <li>
                                            <a class="dropdown-item small py-2" href="{{ route('admin.notifications.read', $notification->id) }}">
                                                @php
                                                    $notificationLabels = ['order' => 'Khách đặt đơn', 'cancelled' => 'Khách huỷ đơn', 'refund' => 'Tiền hoàn', 'review' => 'Đánh giá', 'stock' => 'Báo có hàng'];
                                                    $notificationCategory = $notification->data['category'] ?? 'general';
                                                @endphp
                                                <span class="badge bg-light text-primary border mb-1">{{ $notificationLabels[$notificationCategory] ?? 'Khác' }}</span>
                                                <strong class="d-block">{{ $notification->data['title'] ?? 'Thông báo mới' }}</strong>
                                                <span class="text-muted">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 60) }}</span>
                                            </a>
                                        </li>
                                    @empty
                                        <li><span class="dropdown-item-text small text-muted">Không có thông báo mới.</span></li>
                                    @endforelse
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-center" href="{{ route('admin.notifications.index') }}">Xem tất cả thông báo</a></li>
                                </ul>
                            </li>
                        @endif

                        <!-- Menu User Xổ xuống -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle active fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm admin-user-menu">
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Nội dung chính -->
    <main class="container-fluid admin-main py-4">
        @yield('content')
    </main>

    @if(!request()->routeIs('admin.chat.index') && in_array(Auth::user()->role, ['admin', 'customer_service'], true))
        
        <a class="admin-chat-dock" href="{{ route('admin.chat.index') }}" title="Mở chat hỗ trợ khách hàng">
            <i class="bi bi-chat-square-text-fill"></i><span>Chat hỗ trợ</span><b id="admin-chat-dock-badge" class="admin-chat-badge d-none">0</b>
        </a>
        
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset_v('js/inline-assets.js') }}" defer></script>
</body>
</html>