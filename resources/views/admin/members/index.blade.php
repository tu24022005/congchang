@extends('layouts.app')
@section('title', 'Quản lý Tài khoản Khách hàng - Aloha Beauty')

@push('styles')
<link rel="stylesheet" href="{{ asset_v('css/views/admin-members-index-blade-php.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- TIÊU ĐỀ TRANG VÀ NÚT HÀNH ĐỘNG -->
    <div class="customer-header mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="customer-eyebrow"><i class="bi bi-person-heart me-1"></i>ALOHA BEAUTY / CRM & QUẢN TRỊ KHÁCH HÀNG</span>
                <h2 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-primary"></i> Quản lý Tài khoản Khách hàng
                </h2>
                <p class="text-muted mb-0">
                    Theo dõi hồ sơ khách hàng, phân hạng thành viên, tích lũy điểm thưởng và hỗ trợ khách hàng mua sắm.
                </p>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.customers.export') }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i> Xuất CSV Khách hàng
                </a>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-person-plus-fill me-1"></i> Thêm tài khoản mới
                </a>
            </div>
        </div>
    </div>

    <!-- THÔNG BÁO FLASH MESSAGE -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 bg-success-subtle text-success-emphasis" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 bg-danger-subtle text-danger-emphasis" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 bg-danger-subtle text-danger-emphasis" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-2"></i>Đã có lỗi xảy ra:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 4 THẺ CHỈ SỐ KPI KHÁCH HÀNG (CRM METRICS) -->
    <div class="row g-3 mb-4">
        <!-- 1. Tổng khách hàng -->
        <div class="col-xl-3 col-sm-6">
            <div class="customer-kpi-card card-border-teal h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon-square kpi-icon-teal">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block">Tổng khách hàng</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ number_format($counts['total']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    <span class="text-success fw-semibold"><i class="bi bi-arrow-up-right me-1"></i>+{{ $counts['new_this_month'] }}</span> khách mới đăng ký trong tháng này
                </div>
            </div>
        </div>

        <!-- 2. Khách hàng đã phát sinh đơn -->
        <div class="col-xl-3 col-sm-6">
            <div class="customer-kpi-card card-border-indigo h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon-square kpi-icon-indigo">
                            <i class="bi bi-bag-check"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block">Đã từng mua hàng</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ number_format($counts['buyers']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    Tỷ lệ chuyển đổi mua hàng: <strong class="text-indigo">{{ $counts['conversion_rate'] }}%</strong>
                </div>
            </div>
        </div>

        <!-- 3. Doanh thu từ khách hàng -->
        <div class="col-xl-3 col-sm-6">
            <div class="customer-kpi-card card-border-emerald h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon-square kpi-icon-emerald">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block">Doanh số hoàn thành</span>
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($counts['total_revenue'], 0, ',', '.') }} đ</h4>
                        </div>
                    </div>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    Chi tiêu trung bình: <strong>{{ number_format($counts['avg_spend'], 0, ',', '.') }} đ</strong>/khách mua
                </div>
            </div>
        </div>

        <!-- 4. Điểm tích lũy & Tài khoản khóa -->
        <div class="col-xl-3 col-sm-6">
            <div class="customer-kpi-card card-border-amber h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon-square kpi-icon-amber">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block">Điểm thưởng lưu hành</span>
                            <h4 class="fw-bold mb-0 text-warning text-dark">{{ number_format($counts['total_points']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="small text-muted border-top pt-2 mt-2 d-flex justify-content-between">
                    <span>Đang hoạt động: <strong class="text-success">{{ $counts['active'] }}</strong></span>
                    <span>Bị khóa: <strong class="{{ $counts['locked'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $counts['locked'] }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- KHUNG BỘ LỌC VÀ TÌM KIẾM NÂNG CAO -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2 align-items-center">
                
                <!-- Tìm kiếm -->
                <div class="col-lg-3 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" class="form-control border-start-0 ps-0" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm theo tên, email hoặc SĐT...">
                    </div>
                </div>

                <!-- Lọc theo Hạng thành viên -->
                <div class="col-lg-2 col-md-3">
                    <select name="tier" class="form-select">
                        <option value="">Tất cả hạng thành viên</option>
                        <option value="diamond" @selected(($filters['tier'] ?? '') === 'diamond')>💎 Kim cương (&ge; 20M)</option>
                        <option value="platinum" @selected(($filters['tier'] ?? '') === 'platinum')>🥈 Bạch kim (&ge; 10M)</option>
                        <option value="gold" @selected(($filters['tier'] ?? '') === 'gold')>🥇 Vàng (&ge; 5M)</option>
                        <option value="silver" @selected(($filters['tier'] ?? '') === 'silver')>🥉 Bạc (&ge; 2M)</option>
                        <option value="member" @selected(($filters['tier'] ?? '') === 'member')>🌸 Thành viên (&ge; 1M)</option>
                        <option value="new" @selected(($filters['tier'] ?? '') === 'new')>🌱 Mới tham gia (&lt; 1M)</option>
                    </select>
                </div>

                <!-- Lọc theo Lịch sử mua hàng -->
                <div class="col-lg-2 col-md-3">
                    <select name="buyer_status" class="form-select">
                        <option value="">Tất cả lịch sử mua</option>
                        <option value="has_orders" @selected(($filters['buyer_status'] ?? '') === 'has_orders')>Đã từng mua (có đơn)</option>
                        <option value="no_orders" @selected(($filters['buyer_status'] ?? '') === 'no_orders')>Chưa từng mua (0 đơn)</option>
                    </select>
                </div>

                <!-- Lọc theo Trạng thái tài khoản -->
                <div class="col-lg-2 col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>Đang hoạt động</option>
                        <option value="locked" @selected(($filters['status'] ?? '') === 'locked')>Đang bị khóa</option>
                    </select>
                </div>

                <!-- Sắp xếp -->
                <div class="col-lg-2 col-md-4">
                    <select name="sort" class="form-select">
                        <option value="latest" @selected(($filters['sort'] ?? '') === 'latest')>Mới tham gia nhất</option>
                        <option value="spend_desc" @selected(($filters['sort'] ?? '') === 'spend_desc')>Chi tiêu nhiều nhất</option>
                        <option value="orders_desc" @selected(($filters['sort'] ?? '') === 'orders_desc')>Số đơn nhiều nhất</option>
                        <option value="points_desc" @selected(($filters['sort'] ?? '') === 'points_desc')>Điểm thưởng cao nhất</option>
                        <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>Tên A &rarr; Z</option>
                    </select>
                </div>

                <!-- Nút thao tác -->
                <div class="col-lg-1 col-md-4 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill" title="Áp dụng bộ lọc">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                    @if(!empty($filters['search']) || !empty($filters['tier']) || !empty($filters['buyer_status']) || !empty($filters['status']) || ($filters['sort'] ?? '') !== 'latest')
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-light border rounded-pill" title="Xóa bộ lọc">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- BẢNG DANH SÁCH KHÁCH HÀNG -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-people-fill me-2 text-primary"></i>Danh sách Tài khoản Khách hàng ({{ $members->total() }})
            </h5>
            <small class="text-muted">Hiển thị trang {{ $members->currentPage() }} / {{ $members->lastPage() }}</small>
        </div>

        <div class="table-responsive">
            <table class="table customer-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Khách hàng</th>
                        <th>Thông tin liên hệ</th>
                        <th>Hạng thành viên</th>
                        <th class="text-end">Doanh số hoàn thành</th>
                        <th class="text-center">Điểm hiện có</th>
                        <th class="text-center">Số đơn</th>
                        <th>Ngày tham gia</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        @php
                            $spend = (float) ($member->completed_spend ?? 0);
                            $tier = \App\Models\User::membershipTierFor($spend, (int) $member->loyalty_points);
                            $isLocked = $member->isLocked();
                            
                            $tierBadgeClasses = [
                                'Kim cương' => 'badge-tier-diamond',
                                'Bạch kim' => 'badge-tier-platinum',
                                'Vàng' => 'badge-tier-gold',
                                'Bạc' => 'badge-tier-silver',
                                'Thành viên' => 'badge-tier-member',
                                'Mới tham gia' => 'badge-tier-new',
                            ];
                            $tierBadgeClass = $tierBadgeClasses[$tier['name']] ?? 'badge-tier-new';

                            // Initials Avatar
                            $nameParts = explode(' ', trim($member->name));
                            $initials = count($nameParts) >= 2 
                                ? mb_strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1))
                                : mb_strtoupper(mb_substr($member->name, 0, 2));
                        @endphp
                        <tr class="{{ $isLocked ? 'table-light text-muted' : '' }}">
                            <!-- Khách hàng -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="customer-avatar-circle overflow-hidden shadow-sm">
                                        @if($member->avatar_url)
                                            <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="view-inline-1">
                                        @else
                                            {{ $member->initials }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                            <a href="{{ route('admin.customers.show', $member) }}" class="text-decoration-none text-dark hover-primary">
                                                {{ $member->name }}
                                            </a>
                                            @if($isLocked)
                                                <span class="badge bg-danger rounded-pill px-2 py-0 small view-inline-2">Bị khóa</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">Mã KH: #{{ $member->id }}</small>
                                    </div>
                                </div>
                            </td>

                            <!-- Thông tin liên hệ -->
                            <td>
                                <div><i class="bi bi-envelope text-muted me-1"></i>{{ $member->email }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-telephone text-muted me-1"></i>{{ $member->phone ?? 'Chưa cập nhật' }}
                                </small>
                            </td>

                            <!-- Hạng thành viên -->
                            <td>
                                <span class="badge rounded-pill px-3 py-1 {{ $tierBadgeClass }}">
                                    {{ $tier['name'] }}
                                </span>
                            </td>

                            <!-- Doanh số mua hàng thành công -->
                            <td class="text-end fw-bold text-success">
                                {{ number_format($spend, 0, ',', '.') }} đ
                            </td>

                            <!-- Điểm tích lũy -->
                            <td class="text-center">
                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1 fw-bold">
                                    <i class="bi bi-award-fill me-1 text-warning"></i>{{ number_format($member->loyalty_points) }}
                                </span>
                            </td>

                            <!-- Số đơn hàng -->
                            <td class="text-center">
                                <span class="badge {{ $member->orders_count > 0 ? 'bg-primary-subtle text-primary' : 'bg-light text-muted border' }} rounded-pill px-3 py-1">
                                    {{ $member->orders_count }} đơn
                                </span>
                            </td>

                            <!-- Ngày tham gia -->
                            <td class="text-muted small">
                                {{ $member->created_at?->format('d/m/Y') }}
                            </td>

                            <!-- Thao tác hành động gọn gàng -->
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <!-- Nút xem nhanh -->
                                    <a href="{{ route('admin.customers.show', $member) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold text-nowrap">
                                        <i class="bi bi-eye me-1"></i>Xem
                                    </a>

                                    <!-- Menu thao tác 3 chấm gọn gàng -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center text-secondary shadow-sm" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" class="view-inline-3" title="Thao tác khác">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 py-2 mt-1 view-inline-4">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center text-dark" href="{{ route('admin.customers.edit', $member) }}">
                                                    <i class="bi bi-pencil me-2 text-primary fs-6"></i>Chỉnh sửa thông tin
                                                </a>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item py-2 d-flex align-items-center text-dark" data-bs-toggle="modal" data-bs-target="#adjustPointsModal{{ $member->id }}">
                                                    <i class="bi bi-award me-2 text-warning fs-6"></i>Cộng / Trừ điểm thưởng
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item py-2 d-flex align-items-center text-dark" data-bs-toggle="modal" data-bs-target="#giveVoucherModal{{ $member->id }}">
                                                    <i class="bi bi-ticket-perforated me-2 text-info fs-6"></i>Tặng mã giảm giá
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item py-2 d-flex align-items-center text-dark" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $member->id }}">
                                                    <i class="bi bi-key me-2 text-secondary fs-6"></i>Đặt lại mật khẩu
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form action="{{ route('admin.customers.toggle-lock', $member) }}" method="POST" onsubmit="return confirm('{{ $isLocked ? 'Mở khóa tài khoản cho ' . $member->name . '?' : 'Khóa tài khoản ' . $member->name . '? Khách hàng sẽ không thể đăng nhập.' }}');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item py-2 d-flex align-items-center {{ $isLocked ? 'text-success' : 'text-danger' }}">
                                                        <i class="bi {{ $isLocked ? 'bi-unlock-fill text-success' : 'bi-lock-fill text-danger' }} me-2 fs-6"></i>
                                                        {{ $isLocked ? 'Mở khóa tài khoản' : 'Khóa tài khoản' }}
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.customers.destroy', $member) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản khách hàng {{ $member->name }}? Hành động này không thể hoàn tác.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 d-flex align-items-center text-danger">
                                                        <i class="bi bi-trash-fill text-danger me-2 fs-6"></i>Xóa tài khoản
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- MODAL 1: ĐIỀU CHỈNH ĐIỂM TÍCH LŨY -->
                                <div class="modal fade text-start" id="adjustPointsModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <form action="{{ route('admin.customers.adjust-points', $member) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-bottom p-4">
                                                    <h5 class="modal-title fw-bold text-dark">
                                                        <i class="bi bi-award-fill me-2 text-warning"></i>Điều chỉnh điểm thưởng: {{ $member->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="p-3 bg-light rounded-3 mb-3 border d-flex justify-content-between align-items-center">
                                                        <span>Điểm hiện tại của khách:</span>
                                                        <strong class="fs-5 text-warning">{{ number_format($member->loyalty_points) }} điểm</strong>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Loại điều chỉnh</label>
                                                        <select name="type" class="form-select rounded-3" required>
                                                            <option value="add">➕ Cộng thêm điểm thưởng (CSKH, Sinh nhật, Đền bù)</option>
                                                            <option value="subtract">➖ Trừ bớt điểm (Hết hạn, Hoàn hủy, Vi phạm)</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Số điểm điều chỉnh</label>
                                                        <input type="number" name="points" class="form-control rounded-3" min="1" max="1000000" placeholder="Ví dụ: 50" required>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold text-muted">Lý do điều chỉnh (ghi vào lịch sử điểm)</label>
                                                        <input type="text" name="description" class="form-control rounded-3" placeholder="Ví dụ: Tặng điểm tri ân khách hàng thân thiết" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top p-3 px-4">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Xác nhận điều chỉnh</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- MODAL 2: TẶNG MÃ GIẢM GIÁ (VOUCHER) -->
                                <div class="modal fade text-start" id="giveVoucherModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <form action="{{ route('admin.customers.give-voucher', $member) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-bottom p-4">
                                                    <h5 class="modal-title fw-bold text-dark">
                                                        <i class="bi bi-ticket-perforated-fill me-2 text-info"></i>Tặng Voucher cho {{ $member->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="text-muted small mb-3">
                                                        Chọn mã giảm giá đang hoạt động để gửi trực tiếp vào ví voucher của khách hàng.
                                                    </p>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Chọn mã giảm giá</label>
                                                        <select name="voucher_id" class="form-select rounded-3" required>
                                                            <option value="">-- Chọn một voucher --</option>
                                                            @foreach($availableVouchers as $vc)
                                                                <option value="{{ $vc->id }}">
                                                                    [{{ $vc->code }}] - 
                                                                    @if($vc->type === 'percent')
                                                                        Giảm {{ $vc->value }}%
                                                                    @elseif($vc->type === 'fixed')
                                                                        Giảm {{ number_format($vc->value, 0, ',', '.') }} đ
                                                                    @elseif($vc->type === 'free_shipping')
                                                                        Miễn phí vận chuyển
                                                                    @else
                                                                        {{ $vc->value }}
                                                                    @endif
                                                                    (Hạn: {{ $vc->expires_at ? $vc->expires_at->format('d/m/Y') : 'Vĩnh viễn' }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top p-3 px-4">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-info rounded-pill px-4 text-white fw-bold">Tặng Voucher ngay</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- MODAL 3: ĐẶT LẠI MẬT KHẨU -->
                                <div class="modal fade text-start" id="resetPasswordModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <form action="{{ route('admin.customers.reset-password', $member) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header border-bottom p-4">
                                                    <h5 class="modal-title fw-bold text-dark">
                                                        <i class="bi bi-key-fill me-2 text-secondary"></i>Đặt lại mật khẩu cho {{ $member->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="text-muted small mb-3">
                                                        Cấp mật khẩu mới cho tài khoản email <strong class="text-dark">{{ $member->email }}</strong>. Tài khoản sẽ được mở khóa nếu đang bị khóa.
                                                    </p>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Mật khẩu mới (tối thiểu 8 ký tự)</label>
                                                        <input type="password" name="password" class="form-control rounded-3" minlength="8" required placeholder="Nhập mật khẩu mới...">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Xác nhận lại mật khẩu</label>
                                                        <input type="password" name="password_confirmation" class="form-control rounded-3" minlength="8" required placeholder="Nhập lại mật khẩu mới...">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top p-3 px-4">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4">Cập nhật mật khẩu</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                                Không tìm thấy khách hàng nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $members->links() }}
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script src="{{ asset_v('js/views/admin-members-index-blade-php.js') }}" defer></script>
@endpush
@endsection
