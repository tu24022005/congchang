@extends('layouts.app')

@section('title', 'Sửa tài khoản khách hàng')

@section('content')
<div class="container py-4">
    <a href="{{ route('admin.customers.show', $user) }}" class="btn btn-light border rounded-pill mb-3">← Quay lại tài khoản</a>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h2 class="fw-bold mb-1">Sửa tài khoản khách hàng</h2>
            <p class="text-muted mb-4">Cập nhật thông tin đăng nhập của {{ $user->name }}.</p>
            <form method="POST" action="{{ route('admin.customers.update', $user) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="name">Họ và tên</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="email">Email</label>
                    <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" pattern="^[^@\s]+@[^@\s]+\.[A-Za-z]{2,}$" required>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="phone">Số điện thoại <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                    <input id="phone" type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" pattern="^(0|\+84)(3|5|7|8|9)[0-9]{8}$" autocomplete="tel">
                    @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold" for="password">Mật khẩu mới <span class="text-muted fw-normal">(để trống nếu không đổi)</span></label>
                    <input id="password" type="password" name="password" class="form-control" minlength="8" autocomplete="new-password">
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold" for="password_confirmation">Xác nhận mật khẩu mới</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" minlength="8" autocomplete="new-password">
                </div>
                <button class="btn btn-primary rounded-pill px-4">Lưu thay đổi</button>
                <a href="{{ route('admin.customers.show', $user) }}" class="btn btn-light border rounded-pill px-4">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection
