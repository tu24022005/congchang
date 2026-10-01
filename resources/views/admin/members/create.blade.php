@extends('layouts.app')

@section('title', 'Thêm tài khoản khách hàng - Aloha Beauty')

@push('styles')
<link rel="stylesheet" href="{{ asset_v('css/admin-members-create.css') }}">
@endpush

@section('content')
<div class="container py-4">
    <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-3">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách khách hàng
    </a>

    <div class="create-customer-card p-4 p-lg-5">
        <h2 class="fw-bold mb-1 text-dark">Thêm tài khoản khách hàng mới</h2>
        <p class="text-muted mb-4">Tạo tài khoản đăng nhập trực tiếp cho khách hàng trong hệ thống Aloha Beauty CRM.</p>

        @if ($errors->any())
            <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4 bg-danger-subtle text-danger-emphasis">
                <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Vui lòng kiểm tra lại thông tin:</h6>
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.customers.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- KHỐI ẢNH ĐẠI DIỆN -->
            <div class="p-3 bg-light rounded-4 border mb-4">
                <div class="d-flex flex-column flex-sm-row align-items-center gap-4">
                    <div class="avatar-create-box position-relative" id="avatarPreviewBox">
                        <i class="bi bi-person-fill" id="avatarDefaultIcon"></i>
                        <img id="avatarPreviewImg" src="" alt="Preview" class="avatar-create-preview d-none">
                    </div>
                    <div class="flex-grow-1 w-100 text-center text-sm-start">
                        <label for="avatarInput" class="form-label fw-bold text-dark mb-1">
                            <i class="bi bi-camera me-1 text-primary"></i>Ảnh đại diện (không bắt buộc)
                        </label>
                        <p class="small text-muted mb-2">Chấp nhận JPG, PNG, WEBP. Dung lượng tối đa 2MB. Nếu để trống hệ thống sẽ dùng chữ cái viết tắt.</p>
                        <input id="avatarInput" type="file" name="avatar" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp">
                        @error('avatar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                    <input id="name" name="name" class="form-control rounded-3" value="{{ old('name') }}" required autocomplete="name" placeholder="Nguyễn Văn A">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">Số điện thoại <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                    <input id="phone" name="phone" class="form-control rounded-3" value="{{ old('phone') }}" pattern="^(0|\+84)(3|5|7|8|9)[0-9]{8}$" autocomplete="tel" placeholder="0987654321">
                </div>
                <div class="col-12">
                    <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input id="email" type="email" name="email" class="form-control rounded-3" value="{{ old('email') }}" pattern="^[^@\s]+@[^@\s]+\.[A-Za-z]{2,}$" required autocomplete="email" placeholder="example@gmail.com">
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                    <input id="password" type="password" name="password" class="form-control rounded-3" minlength="8" required autocomplete="new-password" placeholder="Tối thiểu 8 ký tự...">
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control rounded-3" minlength="8" required autocomplete="new-password" placeholder="Nhập lại mật khẩu...">
                </div>
            </div>

            <div class="alert alert-info border-0 rounded-4 mt-4 mb-0 bg-info-subtle text-info-emphasis">
                <i class="bi bi-info-circle me-2"></i>Tài khoản tạo từ trang quản trị được tự động kích hoạt và xác thực email, khách hàng có thể đăng nhập ngay lập tức.
            </div>

            <div class="d-flex align-items-center gap-3 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-person-plus-fill me-1"></i> Tạo tài khoản khách hàng
                </button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-light border rounded-pill px-4">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset_v('js/admin-members-create.js') }}" defer></script>
@endpush
