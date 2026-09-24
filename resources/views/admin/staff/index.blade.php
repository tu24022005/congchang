@extends('layouts.app')
@section('title', 'Phân quyền nhân viên')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
        <div><span class="admin-eyebrow"><i class="bi bi-shield-lock me-1"></i> QUẢN TRỊ HỆ THỐNG</span><h2 class="fw-bold mb-1">Phân quyền nhân viên</h2><p class="text-muted mb-0">Gán vai trò và giới hạn chức năng cho từng tài khoản.</p></div>
        <div class="d-flex gap-2"><a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-clock-history me-1"></i> Lịch sử hoạt động</a><button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createStaffModal"><i class="bi bi-person-plus me-1"></i> Thêm tài khoản</button></div>
    </div>
    @if(session('success'))<div class="alert alert-success rounded-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger rounded-4">{{ session('error') }}</div>@endif
    <div class="card border-0 shadow-sm rounded-4 mb-4"><div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-3"><strong>Quản trị viên</strong><small class="d-block text-muted">Toàn quyền hệ thống</small></div>
            <div class="col-md-3"><strong>Quản lý</strong><small class="d-block text-muted">Báo cáo, doanh thu, thành viên</small></div>
            <div class="col-md-3"><strong>Nhân viên kho</strong><small class="d-block text-muted">Xem/sửa sản phẩm và tồn kho</small></div>
            <div class="col-md-3"><strong>Nhân viên CSKH</strong><small class="d-block text-muted">Đơn hàng và chat khách hàng</small></div>
        </div>
    </div></div>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-6">
                    <label for="staff-search" class="visually-hidden">Tìm nhân viên</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input id="staff-search" type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm theo tên hoặc email nhân viên...">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label for="staff-role" class="visually-hidden">Lọc theo vai trò</label>
                    <select id="staff-role" name="role" class="form-select">
                        <option value="">Tất cả vai trò</option>
                        @foreach(['admin' => 'Quản trị viên', 'manager' => 'Quản lý', 'warehouse_staff' => 'Nhân viên kho', 'customer_service' => 'Nhân viên CSKH'] as $role => $label)
                            <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i> Lọc</button>
                    @if(($filters['search'] ?? '') !== '' || ($filters['role'] ?? '') !== '')
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary" title="Xóa bộ lọc"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <div class="card border-0 shadow-sm rounded-4"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-dark"><tr><th>Nhân viên</th><th>Email</th><th>Vai trò</th><th>Ngày tạo</th><th></th></tr></thead><tbody>
        @forelse($staff as $member)<tr><td class="fw-semibold">{{ $member->name }}</td><td>{{ $member->email }}</td><td><form action="{{ route('admin.staff.update', $member) }}" method="POST" class="d-flex gap-2">@csrf @method('PATCH')<select name="role" class="form-select form-select-sm">@foreach(['admin' => 'Quản trị viên', 'manager' => 'Quản lý', 'warehouse_staff' => 'Nhân viên kho', 'customer_service' => 'Nhân viên CSKH'] as $role => $label)<option value="{{ $role }}" @selected($member->role === $role)>{{ $label }}</option>@endforeach</select><button class="btn btn-sm btn-primary" {{ $member->id === Auth::id() ? 'disabled' : '' }}>Lưu</button></form></td><td>{{ $member->created_at?->format('d/m/Y') }}</td><td class="text-end">@if($member->id !== Auth::id())<form action="{{ route('admin.staff.destroy', $member) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xoá tài khoản này?');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Xoá tài khoản"><i class="bi bi-trash"></i></button></form>@else<span class="text-muted small">Tài khoản hiện tại</span>@endif</td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">Chưa có tài khoản nhân viên.</td></tr>@endforelse
    </tbody></table></div></div>
</div>

<div class="modal fade" id="createStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 rounded-4">
        <form action="{{ route('admin.staff.store') }}" method="POST">
            @csrf
            <div class="modal-header"><h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Thêm tài khoản</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Họ và tên</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
                <div class="row g-3"><div class="col-md-6"><label class="form-label">Mật khẩu</label><input type="password" name="password" class="form-control" minlength="8" required></div><div class="col-md-6"><label class="form-label">Nhập lại mật khẩu</label><input type="password" name="password_confirmation" class="form-control" minlength="8" required></div></div>
                <div class="mt-3"><label class="form-label">Vai trò</label><select name="role" class="form-select" required>@foreach(['manager' => 'Quản lý', 'warehouse_staff' => 'Nhân viên kho', 'customer_service' => 'Nhân viên CSKH'] as $role => $label)<option value="{{ $role }}" @selected(old('role', 'warehouse_staff') === $role)>{{ $label }}</option>@endforeach</select></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary rounded-pill px-4">Tạo tài khoản</button></div>
        </form>
    </div></div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.modal').forEach(function (modal) {
            document.body.appendChild(modal);
        });
    });
</script>
@endsection
