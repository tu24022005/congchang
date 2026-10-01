@extends('layouts.app')
@section('title', 'Tài khoản của tôi')

@section('content')
<link rel="stylesheet" href="{{ asset_v('css/views/account-index-blade-php.css') }}">

<div class="container py-4 acc-page">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-3">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== HERO PROFILE CARD ===== --}}
    <div class="acc-hero mb-4">
        {{-- Avatar --}}
        <div class="acc-avatar-wrap">
            @if($user->avatar_path)
                <img src="{{ Storage::url($user->avatar_path) }}" class="acc-avatar" alt="{{ $user->name }}"
                     onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
            @else
                <div class="acc-avatar-placeholder"><i class="bi bi-person"></i></div>
            @endif
            @if($user->hasVerifiedEmail())
                <div class="acc-verified-dot"><i class="bi bi-check"></i></div>
            @endif
        </div>

        {{-- Info --}}
        <div class="acc-hero-info flex-grow-1">
            <h1 class="acc-name">{{ $user->name }}</h1>
            <div class="acc-email"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</div>
            <div class="acc-hero-meta">
                <span class="acc-hero-badge">
                    <i class="bi bi-award-fill"></i> {{ $membershipTier['name'] }}
                </span>
                <span class="acc-hero-badge">
                    <i class="bi bi-stars"></i> {{ number_format($user->loyalty_points) }} điểm
                </span>
                @if($user->phone)
                    <span class="acc-hero-badge">
                        <i class="bi bi-telephone"></i> {{ $user->phone }}
                    </span>
                @endif
                <span class="acc-hero-badge">
                    <i class="bi bi-calendar3"></i> Thành viên từ {{ $user->created_at?->format('d/m/Y') }}
                </span>
            </div>
            <div class="acc-hero-actions">
                <a href="{{ route('orders.index') }}" class="acc-hero-btn">
                    <i class="bi bi-receipt"></i> Đơn hàng
                </a>
                <a href="{{ route('loyalty.index') }}" class="acc-hero-btn">
                    <i class="bi bi-gift"></i> Đổi điểm
                </a>
                @if($unreadNotificationCount > 0)
                    <a href="{{ route('account.notifications') }}" class="acc-hero-btn">
                        <i class="bi bi-bell-fill"></i> {{ $unreadNotificationCount }} thông báo
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== ĐƠN HÀNG MINI ===== --}}
    <div class="acc-order-mini mb-3">
        <div class="acc-order-mini-head">
            <div class="title">
                <i class="bi bi-box-seam-fill text-danger"></i>
                Đơn hàng của tôi
                @if($orderStats['processing'] + $orderStats['shipping'] > 0)
                    <span class="badge bg-danger-subtle text-danger rounded-pill view-inline-1">
                        {{ $orderStats['processing'] + $orderStats['shipping'] }} đang xử lý
                    </span>
                @endif
            </div>
            <a href="{{ route('orders.index') }}" class="see-all">Xem tất cả <i class="bi bi-chevron-right small"></i></a>
        </div>
        <div class="row g-2">
            <div class="col-6 col-md-3">
                <a href="{{ route('orders.index', ['status' => 'processing']) }}" class="order-step">
                    <div class="step-icon text-warning">
                        <i class="bi bi-hourglass-split"></i>
                        @if($orderStats['processing'] > 0)
                            <span class="badge bg-warning text-dark">{{ $orderStats['processing'] }}</span>
                        @endif
                    </div>
                    <span class="step-name">Chờ xác nhận</span>
                    <span class="step-count">{{ $orderStats['processing'] }} đơn</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('orders.index', ['status' => 'shipping']) }}" class="order-step">
                    <div class="step-icon text-primary">
                        <i class="bi bi-truck"></i>
                        @if($orderStats['shipping'] > 0)
                            <span class="badge bg-primary">{{ $orderStats['shipping'] }}</span>
                        @endif
                    </div>
                    <span class="step-name">Đang giao</span>
                    <span class="step-count">{{ $orderStats['shipping'] }} đơn</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('orders.index', ['status' => 'paid']) }}" class="order-step">
                    <div class="step-icon text-success">
                        <i class="bi bi-check2-circle"></i>
                        @if($orderStats['completed'] > 0)
                            <span class="badge bg-success">{{ $orderStats['completed'] }}</span>
                        @endif
                    </div>
                    <span class="step-name">Hoàn thành</span>
                    <span class="step-count">{{ $orderStats['completed'] }} đơn</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" class="order-step">
                    <div class="step-icon text-secondary">
                        <i class="bi bi-x-circle"></i>
                        @if($orderStats['cancelled'] > 0)
                            <span class="badge bg-secondary">{{ $orderStats['cancelled'] }}</span>
                        @endif
                    </div>
                    <span class="step-name">Đã hủy</span>
                    <span class="step-count">{{ $orderStats['cancelled'] }} đơn</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ===== LƯỚI TIỆN ÍCH ===== --}}
    <div class="acc-services mb-4">
        <a href="#personal-info" class="acc-svc svc-blue">
            <div class="svc-icon"><i class="bi bi-person-circle"></i></div>
            <div class="svc-name">Cá nhân</div>
            <div class="svc-sub">{{ Str::limit($user->name, 14) }}</div>
        </a>
        <a href="{{ route('loyalty.index') }}" class="acc-svc svc-yellow">
            <div class="svc-icon"><i class="bi bi-star-fill"></i></div>
            <div class="svc-name">Điểm thưởng</div>
            <div class="svc-sub">{{ number_format($user->loyalty_points) }} điểm</div>
        </a>
        <a href="{{ route('account.vouchers') }}" class="acc-svc svc-red">
            @if($voucherCount > 0)<span class="svc-badge">{{ $voucherCount }}</span>@endif
            <div class="svc-icon"><i class="bi bi-ticket-perforated-fill"></i></div>
            <div class="svc-name">Voucher</div>
            <div class="svc-sub">{{ $voucherCount > 0 ? $voucherCount.' khả dụng' : 'Kho ưu đãi' }}</div>
        </a>
        <a href="{{ route('wishlist.index') }}" class="acc-svc svc-pink">
            @if($wishlistCount > 0)<span class="svc-badge">{{ $wishlistCount }}</span>@endif
            <div class="svc-icon"><i class="bi bi-heart-fill"></i></div>
            <div class="svc-name">Yêu thích</div>
            <div class="svc-sub">{{ $wishlistCount > 0 ? $wishlistCount.' sản phẩm' : 'Đã lưu' }}</div>
        </a>
        <a href="#shipping-addresses" class="acc-svc svc-teal">
            <div class="svc-icon"><i class="bi bi-geo-alt-fill"></i></div>
            <div class="svc-name">Sổ địa chỉ</div>
            <div class="svc-sub">{{ $addresses->count() }} địa chỉ</div>
        </a>
        <a href="{{ route('password.change') }}" class="acc-svc svc-green">
            <div class="svc-icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="svc-name">Bảo mật</div>
            <div class="svc-sub">Đổi mật khẩu</div>
        </a>
    </div>

    {{-- ===== NỘI DUNG CHÍNH ===== --}}
    <div class="row g-4">

        {{-- CỘT TRÁI: ĐIỂM THƯỞNG + THÔNG BÁO --}}
        <div class="col-lg-4">

            {{-- POINTS CARD --}}
            <div class="acc-points-card mb-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="view-inline-2">Điểm & hạng thành viên</span>
                    <i class="bi bi-stars fs-5"></i>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <div class="acc-points-num">{{ number_format($user->loyalty_points) }}</div>
                        <div class="acc-points-label">điểm tích lũy</div>
                    </div>
                    <span class="acc-tier-badge">{{ $membershipTier['name'] }}</span>
                </div>
                <div class="acc-points-progress">
                    <div class="progress">
                        <div class="progress-bar" data-inline-width="{{ $tierProgress }}" class="inline-dynamic-width"></div>
                    </div>
                </div>
                <div class="acc-points-hint">
                    @if($nextTier)
                        @php
                            $val = $effectiveValue ?? $completedSpend;
                            $rem = max(0, $nextTier['threshold'] - $val);
                        @endphp
                        Còn <strong>{{ number_format($rem, 0, ',', '.') }} đ</strong> để lên hạng <strong>{{ $nextTier['name'] }}</strong>
                    @else
                        <i class="bi bi-patch-check-fill"></i> Bạn đang ở hạng cao nhất!
                    @endif
                </div>
                <div class="acc-points-actions">
                    <a href="{{ route('loyalty.index') }}" class="acc-points-btn"><i class="bi bi-gift"></i> Đổi điểm</a>
                    <a href="{{ route('loyalty.index') }}" class="acc-points-btn"><i class="bi bi-clock-history"></i> Lịch sử</a>
                </div>
            </div>

            {{-- INFO NHANH --}}
            <div class="acc-panel">
                <div class="acc-panel-head">
                    <div class="ph-left">
                        <div class="ph-icon ph-icon-teal"><i class="bi bi-info-circle-fill"></i></div>
                        <div>
                            <div class="ph-title">Thông tin nhanh</div>
                            <div class="ph-sub">Trạng thái tài khoản</div>
                        </div>
                    </div>
                </div>
                <div class="acc-panel-body pt-3">
                    <div class="d-flex flex-column gap-2 view-inline-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted"><i class="bi bi-envelope me-2"></i>Email</span>
                            @if($user->hasVerifiedEmail())
                                <span class="badge bg-success-subtle text-success rounded-pill"><i class="bi bi-patch-check-fill me-1"></i>Đã xác thực</span>
                            @else
                                <a href="{{ route('verification.notice') }}" class="badge bg-warning-subtle text-warning rounded-pill text-decoration-none">Xác thực ngay</a>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted"><i class="bi bi-telephone me-2"></i>Điện thoại</span>
                            <span class="fw-semibold">{{ $user->phone ?: '—' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted"><i class="bi bi-calendar3 me-2"></i>Tham gia</span>
                            <span class="fw-semibold">{{ $user->created_at?->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted"><i class="bi bi-bag-check me-2"></i>Doanh số</span>
                            <span class="fw-semibold text-danger">{{ number_format($completedSpend, 0, ',', '.') }} đ</span>
                        </div>
                    </div>
                    <a href="{{ route('password.change') }}" class="btn btn-outline-secondary w-100 rounded-pill mt-3 btn-sm">
                        <i class="bi bi-shield-lock me-1"></i>Đổi mật khẩu
                    </a>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: FORM + ĐỊA CHỈ --}}
        <div class="col-lg-8">

            {{-- PANEL THÔNG TIN CÁ NHÂN --}}
            <div class="acc-panel mb-4" id="personal-info">
                <div class="acc-panel-head">
                    <div class="ph-left">
                        <div class="ph-icon ph-icon-purple"><i class="bi bi-person-lines-fill"></i></div>
                        <div>
                            <div class="ph-title">Thông tin cá nhân</div>
                            <div class="ph-sub">Cập nhật thông tin hiển thị của bạn</div>
                        </div>
                    </div>
                </div>
                <div class="acc-panel-body">
                    <form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="avatar" class="form-label">Ảnh đại diện</label>
                                <input id="avatar" type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text">JPG, PNG hoặc WEBP · Tối đa 2MB</div>
                                @error('avatar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Họ và tên</label>
                                <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required autocomplete="name">
                                @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Địa chỉ email</label>
                                <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="email">
                                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="personal_phone" class="form-label">Số điện thoại</label>
                                <input id="personal_phone" type="tel" name="personal_phone" class="form-control"
                                       value="{{ old('personal_phone', $user->phone) }}"
                                       pattern="^(0|\+84)(3|5|7|8|9)[0-9]{8}$" autocomplete="tel">
                                <div class="form-text">Dùng cho tài khoản, không tự thay số người nhận hàng.</div>
                                @error('personal_phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill px-5">
                                <i class="bi bi-check2 me-2"></i>Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- PANEL SỔ ĐỊA CHỈ --}}
            <div class="acc-panel" id="shipping-addresses">
                <div class="acc-panel-head">
                    <div class="ph-left">
                        <div class="ph-icon ph-icon-teal"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <div class="ph-title">Sổ địa chỉ giao hàng</div>
                            <div class="ph-sub">{{ $addresses->count() }} địa chỉ đã lưu</div>
                        </div>
                    </div>
                </div>
                <div class="acc-panel-body">

                    {{-- Danh sách địa chỉ --}}
                    @forelse($addresses as $address)
                        <div class="addr-card {{ $address->is_default ? 'is-default' : '' }}">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="addr-label">
                                        {{ $address->label }}
                                        @if($address->is_default)
                                            <span class="badge bg-purple-subtle text-purple ms-1 view-inline-4">Mặc định</span>
                                        @endif
                                    </div>
                                    <div class="addr-detail">
                                        <i class="bi bi-person me-1"></i>{{ $address->recipient_name }}
                                        <span class="mx-1">·</span>
                                        <i class="bi bi-telephone me-1"></i>{{ $address->phone }}
                                    </div>
                                    <div class="addr-detail mt-1">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $address->address }}
                                    </div>
                                </div>
                                <div class="addr-actions flex-shrink-0">
                                    @unless($address->is_default)
                                        <form method="POST" action="{{ route('addresses.default', $address) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-outline-primary view-inline-5">
                                                <i class="bi bi-pin-angle"></i> Mặc định
                                            </button>
                                        </form>
                                    @endunless
                                    <button class="btn btn-outline-secondary view-inline-5"
                                            data-bs-toggle="collapse" data-bs-target="#edit-addr-{{ $address->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('addresses.destroy', $address) }}"
                                          class="d-inline" onsubmit="return confirm('Xóa địa chỉ này?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger view-inline-5">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Form sửa collapse --}}
                            <div class="collapse mt-3" id="edit-addr-{{ $address->id }}">
                                <form method="POST" action="{{ route('addresses.update', $address) }}" class="row g-2">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="address_form" value="update">
                                    <input type="hidden" name="address_id" value="{{ $address->id }}">
                                    <div class="col-md-3">
                                        <input name="label" class="form-control form-control-sm" placeholder="Nhãn" value="{{ old('address_id') == $address->id ? old('label') : $address->label }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input name="recipient_name" class="form-control form-control-sm" placeholder="Tên người nhận" value="{{ old('address_id') == $address->id ? old('recipient_name') : $address->recipient_name }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input name="recipient_phone" type="tel" class="form-control form-control-sm" placeholder="SĐT" value="{{ old('address_id') == $address->id ? old('recipient_phone') : $address->phone }}" required>
                                    </div>
                                    <div class="col-md-9">
                                        <input name="address" class="form-control form-control-sm" placeholder="Địa chỉ chi tiết" value="{{ old('address_id') == $address->id ? old('address') : $address->address }}" required>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-center gap-2">
                                        <div class="form-check mb-0">
                                            <input type="checkbox" name="is_default" value="1" class="form-check-input" id="def-{{ $address->id }}"
                                                   @checked(old('address_id') == $address->id ? old('is_default') : $address->is_default)>
                                            <label class="form-check-label" for="def-{{ $address->id }}" class="view-inline-6">Mặc định</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm rounded-pill px-3">
                                            <i class="bi bi-save me-1"></i>Lưu
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 view-inline-7">
                            <i class="bi bi-geo me-1"></i>Bạn chưa có địa chỉ nào. Thêm địa chỉ để thanh toán nhanh hơn!
                        </p>
                    @endforelse

                    {{-- Form thêm địa chỉ mới --}}
                    <div class="addr-add-form mt-3">
                        <div class="fw-bold mb-2 view-inline-7"><i class="bi bi-plus-circle me-1 text-primary"></i>Thêm địa chỉ mới</div>
                        <form method="POST" action="{{ route('addresses.store') }}" class="row g-2">
                            @csrf
                            <input type="hidden" name="address_form" value="create">
                            <div class="col-md-3">
                                <input name="label" class="form-control form-control-sm" value="{{ old('address_form') === 'create' ? old('label') : '' }}" placeholder="Nhãn: Nhà riêng" required>
                            </div>
                            <div class="col-md-3">
                                <input name="recipient_name" class="form-control form-control-sm" value="{{ old('address_form') === 'create' ? old('recipient_name') : '' }}" placeholder="Tên người nhận" required>
                            </div>
                            <div class="col-md-3">
                                <input name="recipient_phone" type="tel" class="form-control form-control-sm" value="{{ old('address_form') === 'create' ? old('recipient_phone') : '' }}" placeholder="SĐT người nhận" autocomplete="shipping tel" required>
                            </div>
                            <div class="col-md-9">
                                <input name="address" class="form-control form-control-sm" value="{{ old('address_form') === 'create' ? old('address') : '' }}" placeholder="Địa chỉ chi tiết" required>
                            </div>
                            <div class="col-md-3 d-flex align-items-center gap-2">
                                <div class="form-check mb-0">
                                    <input type="checkbox" name="is_default" value="1" class="form-check-input" id="new-default"
                                           @checked(old('address_form') === 'create' && old('is_default'))>
                                    <label class="form-check-label" for="new-default" class="view-inline-6">Mặc định</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="bi bi-save me-1"></i>Lưu địa chỉ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection