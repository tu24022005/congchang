@extends('layouts.app')

@section('title', 'Thêm tài khoản khách hàng')

@section('content')
<div class="container py-4">
    <a href="{{ route('admin.customers.index') }}" class="btn btn-light border rounded-pill mb-3">← Quay lại tài khoản khách hàng</a>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h2 class="fw-bold mb-1">Thêm tài khoản khách hàng</h2>
            <p class="text-muted mb-4">Tạo tài khoản đăng nhập trực tiếp cho khách hàng.</p>

            @if ($errors->any())
                <div class="alert alert-danger rounded-4">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.customers.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Họ và tên</label>
                        <input id="name" name="name" class="form-control" value="{{ old('name') }}" required autocomplete="name">
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Số điện thoại <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                        <input id="phone" name="phone" class="form-control" value="{{ old('phone') }}" pattern="^(0|\+84)(3|5|7|8|9)[0-9]{8}$" autocomplete="tel">
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" pattern="^[^@\s]+@[^@\s]+\.[A-Za-z]{2,}$" required autocomplete="email">
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                        <input id="password" type="password" name="password" class="form-control" minlength="8" required autocomplete="new-password">
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" minlength="8" required autocomplete="new-password">
                    </div>
                </div>
                <div class="alert alert-info border-0 rounded-4 mt-4 mb-0">
                    <i class="bi bi-info-circle me-2"></i>Tài khoản tạo từ trang quản trị được xác thực email sẵn và có thể đăng nhập ngay.
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary rounded-pill px-4"><i class="bi bi-person-plus me-2"></i>Tạo tài khoản</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-light border rounded-pill px-4">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
