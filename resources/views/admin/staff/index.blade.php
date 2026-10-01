@extends('layouts.app')
@section('title', 'Phân quyền & Quản trị Nhân viên - Aloha Beauty')

@push('styles')
<link rel="stylesheet" href="{{ asset_v('css/views/admin-staff-index-blade-php.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- TIÊU ĐỀ TRANG VÀ NÚT HÀNH ĐỘNG -->
    <div class="staff-page-header mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="staff-eyebrow"><i class="bi bi-shield-lock-fill me-1"></i>QUẢN TRỊ HỆ THỐNG / PHÂN QUYỀN NỘI BỘ</span>
                <h2 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-primary"></i> Phân quyền & Quản trị Nhân viên
                </h2>
                <p class="text-muted mb-0">
                    Gán vai trò, quản lý tài khoản nhân viên, kiểm soát quyền truy cập và bảo mật đăng nhập.
                </p>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#permissionsMatrixCollapse" aria-expanded="false">
                    <i class="bi bi-diagram-3 me-1"></i> Ma trận quyền hạn
                </button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                    <i class="bi bi-clock-history me-1"></i> Lịch sử hoạt động
                </a>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createStaffModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Thêm tài khoản mới
                </button>
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

    <!-- 4 THẺ THỐNG KÊ VAI TRÒ VÀ TRÁCH NHIỆM NỘI BỘ -->
    <div class="row g-3 mb-4">
        <!-- 1. Quản trị viên -->
        <div class="col-xl-3 col-sm-6">
            <div class="role-metric-card role-card-admin h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="role-avatar role-avatar-admin">
                            <i class="bi bi-shield-shaded"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Quản trị viên</h6>
                            <small class="text-muted">Admin tối cao</small>
                        </div>
                    </div>
                    <span class="fs-4 fw-bold text-dark">{{ $counts['admin'] }}</span>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    Toàn quyền hệ thống, phân quyền, cấu hình và bảo mật.
                </div>
            </div>
        </div>

        <!-- 2. Quản lý -->
        <div class="col-xl-3 col-sm-6">
            <div class="role-metric-card role-card-manager h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="role-avatar role-avatar-manager">
                            <i class="bi bi-bar-chart-line-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Quản lý</h6>
                            <small class="text-muted">Manager</small>
                        </div>
                    </div>
                    <span class="fs-4 fw-bold text-dark">{{ $counts['manager'] }}</span>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    Dashboard, doanh thu, sản phẩm, duyệt hoàn tiền và khách hàng.
                </div>
            </div>
        </div>

        <!-- 3. Nhân viên kho -->
        <div class="col-xl-3 col-sm-6">
            <div class="role-metric-card role-card-warehouse h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="role-avatar role-avatar-warehouse">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Nhân viên kho</h6>
                            <small class="text-muted">Warehouse</small>
                        </div>
                    </div>
                    <span class="fs-4 fw-bold text-dark">{{ $counts['warehouse'] }}</span>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    Cập nhật tồn kho sản phẩm, theo dõi nhật ký xuất nhập.
                </div>
            </div>
        </div>

        <!-- 4. Nhân viên CSKH -->
        <div class="col-xl-3 col-sm-6">
            <div class="role-metric-card role-card-cs h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="role-avatar role-avatar-cs">
                            <i class="bi bi-chat-heart-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Nhân viên CSKH</h6>
                            <small class="text-muted">Customer Service</small>
                        </div>
                    </div>
                    <span class="fs-4 fw-bold text-dark">{{ $counts['cs'] }}</span>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    Xử lý trạng thái đơn hàng và Live Chat trực tiếp với khách.
                </div>
            </div>
        </div>
    </div>

    <!-- MA TRẬN PHÂN QUYỀN HỆ THỐNG (COLLAPSIBLE PERMISSIONS MATRIX) -->
    <div class="collapse mb-4" id="permissionsMatrixCollapse">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-diagram-3-fill me-2 text-primary"></i>Ma trận Phân quyền Chức năng Hệ thống
                    </h5>
                    <small class="text-muted">Chi tiết các tính năng mà từng vai trò được phép hoặc không được phép truy cập.</small>
                </div>
                <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#permissionsMatrixCollapse"></button>
            </div>
            <div class="table-responsive">
                <table class="table matrix-table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th class="text-start ps-4 view-inline-1">Chức năng / Module hệ thống</th>
                            <th class="view-inline-2">Quản trị viên (Admin)</th>
                            <th class="view-inline-2">Quản lý (Manager)</th>
                            <th class="view-inline-2">Nhân viên kho</th>
                            <th class="view-inline-2">Nhân viên CSKH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard & Thống kê kinh doanh</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-bar-chart-fill me-2 text-success"></i>Báo cáo Doanh thu & Xuất CSV</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-boxes me-2 text-info"></i>Xem Sản phẩm & Danh mục</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-plus-square-fill me-2 text-danger"></i>Tạo mới & Xóa Sản phẩm</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-pencil-square me-2 text-warning"></i>Chỉnh sửa Thông tin Sản phẩm</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-box-seam me-2 text-warning"></i>Điều chỉnh Tồn kho & Biến thể</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-journal-text me-2 text-secondary"></i>Nhật ký Xuất / Nhập kho</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-truck me-2 text-primary"></i>Xem & Xử lý Trạng thái Đơn hàng</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-arrow-counterclockwise me-2 text-danger"></i>Duyệt & Xử lý Yêu cầu Hoàn tiền</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-chat-dots-fill me-2 text-primary"></i>Live Chat Hỗ trợ Khách hàng</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-people me-2 text-secondary"></i>Quản lý Thành viên & Khách hàng</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-ticket-perforated me-2 text-danger"></i>Mã giảm giá (Vouchers) & Banners</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold text-dark"><i class="bi bi-shield-lock-fill me-2 text-dark"></i>Phân quyền & Quản lý Nhân sự</td>
                            <td><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                            <td><i class="bi bi-dash-circle text-muted fs-5"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- KHUNG TÌM KIẾM VÀ BỘ LỌC -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-5 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" class="form-control border-start-0 ps-0" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm theo tên, email hoặc số điện thoại...">
                    </div>
                </div>

                <div class="col-lg-3 col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Tất cả vai trò ({{ $counts['total'] }})</option>
                        <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Quản trị viên ({{ $counts['admin'] }})</option>
                        <option value="manager" @selected(($filters['role'] ?? '') === 'manager')>Quản lý ({{ $counts['manager'] }})</option>
                        <option value="warehouse_staff" @selected(($filters['role'] ?? '') === 'warehouse_staff')>Nhân viên kho ({{ $counts['warehouse'] }})</option>
                        <option value="customer_service" @selected(($filters['role'] ?? '') === 'customer_service')>Nhân viên CSKH ({{ $counts['cs'] }})</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>Đang hoạt động ({{ $counts['active'] }})</option>
                        <option value="locked" @selected(($filters['status'] ?? '') === 'locked')>Đang bị khóa ({{ $counts['locked'] }})</option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 rounded-pill">
                        <i class="bi bi-funnel-fill me-1"></i> Lọc
                    </button>
                    @if(!empty($filters['search']) || !empty($filters['role']) || !empty($filters['status']))
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-light border rounded-pill" title="Xóa bộ lọc">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- BẢNG DANH SÁCH NHÂN VIÊN VÀ QUYỀN HẠN -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-badge-fill me-2 text-primary"></i>Danh sách Tài khoản Nhân viên ({{ $staff->count() }})
                </h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-1">
                    <i class="bi bi-check-circle-fill me-1"></i>{{ $counts['active'] }} Hoạt động
                </span>
                @if($counts['locked'] > 0)
                    <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-1">
                        <i class="bi bi-lock-fill me-1"></i>{{ $counts['locked'] }} Đang bị khóa
                    </span>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table staff-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nhân viên</th>
                        <th>Thông tin liên hệ</th>
                        <th>Vai trò phân quyền</th>
                        <th class="text-center">Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                        @php
                            $isSelf = $member->id === Auth::id();
                            $isLocked = $member->isLocked();
                            $roleBadges = [
                                'admin' => ['label' => 'Quản trị viên', 'class' => 'badge-role-admin', 'icon' => 'bi-shield-shaded'],
                                'manager' => ['label' => 'Quản lý', 'class' => 'badge-role-manager', 'icon' => 'bi-bar-chart-line-fill'],
                                'warehouse_staff' => ['label' => 'Nhân viên kho', 'class' => 'badge-role-warehouse', 'icon' => 'bi-box-seam-fill'],
                                'customer_service' => ['label' => 'Nhân viên CSKH', 'class' => 'badge-role-cs', 'icon' => 'bi-chat-heart-fill'],
                            ];
                            $currentBadge = $roleBadges[$member->role] ?? ['label' => $member->role, 'class' => 'bg-secondary', 'icon' => 'bi-person'];
                            
                            // Tạo avatar chữ cái
                            $nameParts = explode(' ', trim($member->name));
                            $initials = count($nameParts) >= 2 
                                ? mb_strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1))
                                : mb_strtoupper(mb_substr($member->name, 0, 2));
                        @endphp
                        <tr class="{{ $isLocked ? 'table-light text-muted' : '' }}">
                            <!-- Nhân viên -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar-circle shadow-sm">
                                        @if($member->avatar_url)
                                            <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="view-inline-3">
                                        @else
                                            {{ $member->initials }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                            {{ $member->name }}
                                            @if($isSelf)
                                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0 small view-inline-4">Bạn</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">Mã NV: #{{ $member->id }}</small>
                                    </div>
                                </div>
                            </td>

                            <!-- Thông tin liên hệ -->
                            <td>
                                <div><i class="bi bi-envelope text-muted me-1"></i>{{ $member->email }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-telephone text-muted me-1"></i>{{ $member->phone ?? 'Chưa có SĐT' }}
                                </small>
                            </td>

                            <!-- Vai trò phân quyền (đổi nhanh hoặc hiển thị badge) -->
                            <td>
                                <form action="{{ route('admin.staff.update', $member) }}" method="POST" class="d-flex align-items-center gap-2 view-inline-5">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="form-select form-select-sm rounded-pill fw-semibold {{ $currentBadge['class'] }}" {{ $isSelf ? 'disabled' : '' }}>
                                        <option value="admin" @selected($member->role === 'admin')>🛡️ Quản trị viên</option>
                                        <option value="manager" @selected($member->role === 'manager')>📊 Quản lý</option>
                                        <option value="warehouse_staff" @selected($member->role === 'warehouse_staff')>📦 Nhân viên kho</option>
                                        <option value="customer_service" @selected($member->role === 'customer_service')>💬 Nhân viên CSKH</option>
                                    </select>
                                    @if(!$isSelf)
                                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-circle p-1" title="Lưu vai trò mới" class="view-inline-6">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Trạng thái -->
                            <td class="text-center">
                                @if($isLocked)
                                    <span class="badge bg-danger rounded-pill px-3 py-1">
                                        <i class="bi bi-lock-fill me-1"></i>Bị khóa
                                    </span>
                                    <div class="small text-danger mt-1 view-inline-4">
                                        {{ $member->login_locked_at?->format('d/m/Y H:i') }}
                                    </div>
                                @else
                                    <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i>Hoạt động
                                    </span>
                                @endif
                            </td>

                            <!-- Ngày tạo -->
                            <td class="text-muted small">
                                {{ $member->created_at?->format('d/m/Y H:i') }}
                            </td>

                            <!-- Thao tác -->
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <!-- Nút Chỉnh sửa thông tin -->
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold text-nowrap" data-bs-toggle="modal" data-bs-target="#editStaffModal{{ $member->id }}">
                                        <i class="bi bi-pencil-square me-1"></i>Sửa
                                    </button>

                                    <!-- Menu thao tác 3 chấm gọn gàng -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center text-secondary shadow-sm" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" class="view-inline-7" title="Thao tác khác">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 py-2 mt-1 view-inline-8">
                                            <li>
                                                <button type="button" class="dropdown-item py-2 d-flex align-items-center text-dark" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $member->id }}">
                                                    <i class="bi bi-key-fill me-2 text-warning fs-6"></i>Đổi mật khẩu
                                                </button>
                                            </li>
                                            @if(!$isSelf)
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <form action="{{ route('admin.staff.toggle-lock', $member) }}" method="POST" onsubmit="return confirm('{{ $isLocked ? 'Mở khóa tài khoản cho ' . $member->name . '?' : 'Khóa tài khoản ' . $member->name . '? Nhân viên sẽ không thể đăng nhập.' }}');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center {{ $isLocked ? 'text-success' : 'text-danger' }}">
                                                            <i class="bi {{ $isLocked ? 'bi-unlock-fill text-success' : 'bi-lock-fill text-danger' }} me-2 fs-6"></i>
                                                            {{ $isLocked ? 'Mở khóa tài khoản' : 'Khóa tài khoản' }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn tài khoản nhân viên {{ $member->name }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center text-danger">
                                                            <i class="bi bi-trash-fill text-danger me-2 fs-6"></i>Xóa tài khoản
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                <!-- MODAL SỬA THÔNG TIN NHÂN VIÊN -->
                                <div class="modal fade text-start" id="editStaffModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <form action="{{ route('admin.staff.update', $member) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header border-bottom p-4">
                                                    <h5 class="modal-title fw-bold text-dark">
                                                        <i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa nhân viên
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Họ và tên</label>
                                                        <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $member->name) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Địa chỉ Email</label>
                                                        <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $member->email) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Số điện thoại</label>
                                                        <input type="text" name="phone" class="form-control rounded-3" value="{{ old('phone', $member->phone) }}" placeholder="Ví dụ: 0912345678">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Ảnh đại diện (JPG, PNG, WEBP, tối đa 2MB)</label>
                                                        <input type="file" name="avatar" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp">
                                                        @if($member->avatar_path)
                                                            <div class="form-check mt-2">
                                                                <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="removeStaffAvatar{{ $member->id }}">
                                                                <label class="form-check-label text-danger small" for="removeStaffAvatar{{ $member->id }}">
                                                                    <i class="bi bi-trash3 me-1"></i> Xóa ảnh đại diện hiện tại
                                                                </label>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Vai trò phân quyền</label>
                                                        <select name="role" class="form-select rounded-3" {{ $isSelf ? 'disabled' : '' }} required>
                                                            <option value="admin" @selected($member->role === 'admin')>🛡️ Quản trị viên (Toàn quyền)</option>
                                                            <option value="manager" @selected($member->role === 'manager')>📊 Quản lý (Báo cáo, Doanh thu, Sản phẩm)</option>
                                                            <option value="warehouse_staff" @selected($member->role === 'warehouse_staff')>📦 Nhân viên kho (Tồn kho, Xuất nhập)</option>
                                                            <option value="customer_service" @selected($member->role === 'customer_service')>💬 Nhân viên CSKH (Đơn hàng, Live Chat)</option>
                                                        </select>
                                                        @if($isSelf)
                                                            <input type="hidden" name="role" value="{{ $member->role }}">
                                                            <small class="text-danger d-block mt-1">Không thể thay đổi vai trò của tài khoản đang đăng nhập.</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top p-3 px-4">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4">Lưu thay đổi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- MODAL ĐẶT LẠI MẬT KHẨU -->
                                <div class="modal fade text-start" id="resetPasswordModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <form action="{{ route('admin.staff.reset-password', $member) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header border-bottom p-4">
                                                    <h5 class="modal-title fw-bold text-dark">
                                                        <i class="bi bi-key-fill me-2 text-warning"></i>Đặt lại mật khẩu
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="text-muted small mb-3">
                                                        Đổi mật khẩu mới cho nhân viên <strong class="text-dark">{{ $member->name }}</strong> ({{ $member->email }}). Sau khi đổi, tài khoản sẽ được mở khóa nếu đang bị khóa.
                                                    </p>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Mật khẩu mới (tối thiểu 8 ký tự)</label>
                                                        <input type="password" name="password" class="form-control rounded-3" minlength="8" required placeholder="Nhập mật khẩu mới...">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Xác nhận lại mật khẩu mới</label>
                                                        <input type="password" name="password_confirmation" class="form-control rounded-3" minlength="8" required placeholder="Nhập lại mật khẩu mới...">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top p-3 px-4">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Cập nhật mật khẩu</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                                Không tìm thấy tài khoản nhân viên nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL THÊM NHÂN VIÊN MỚI -->
<div class="modal fade" id="createStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-person-plus-fill me-2 text-primary"></i>Thêm tài khoản nhân viên mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Họ và tên nhân viên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" value="{{ old('name') }}" placeholder="Ví dụ: Nguyễn Văn An" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email đăng nhập <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3" value="{{ old('email') }}" placeholder="nhanvien@alohabeauty.vn" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control rounded-3" value="{{ old('phone') }}" placeholder="Ví dụ: 0987654321">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Ảnh đại diện (không bắt buộc)</label>
                        <input type="file" name="avatar" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control rounded-3" minlength="8" placeholder="Tối thiểu 8 ký tự" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3" minlength="8" placeholder="Khớp mật khẩu trên" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Vai trò & Phân quyền <span class="text-danger">*</span></label>
                        <select name="role" class="form-select rounded-3" required>
                            <option value="customer_service" @selected(old('role') === 'customer_service')>💬 Nhân viên CSKH (Đơn hàng & Live Chat)</option>
                            <option value="warehouse_staff" @selected(old('role') === 'warehouse_staff')>📦 Nhân viên kho (Xem/sửa tồn kho & Nhật ký kho)</option>
                            <option value="manager" @selected(old('role') === 'manager')>📊 Quản lý (Dashboard, Doanh thu, Sản phẩm, Hoàn tiền)</option>
                            <option value="admin" @selected(old('role') === 'admin')>🛡️ Quản trị viên (Toàn quyền hệ thống)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Tạo tài khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset_v('js/views/admin-staff-index-blade-php.js') }}" defer></script>
@endpush
@endsection
