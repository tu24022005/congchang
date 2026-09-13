@extends('layouts.app')
@section('title', 'Tài khoản của tôi')

@section('content')
<div class="account-page py-3 py-lg-4">
    <div class="account-page-heading mb-4">
        <div>
            <span class="account-kicker">KHÔNG GIAN CỦA BẠN</span>
            <h1 class="mb-2">Tài khoản của tôi</h1>
            <p class="text-muted mb-0">Quản lý thông tin cá nhân và bảo mật tài khoản Aloha Beauty.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-receipt me-2"></i>Đơn hàng của tôi
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="account-sidebar h-100">
                <div class="account-avatar"><i class="bi bi-person"></i></div>
                <h2>{{ $user->name }}</h2>
                <p class="text-muted mb-4">{{ $user->email }}</p>
                <div class="account-status {{ $user->hasVerifiedEmail() ? 'is-verified' : 'is-pending' }}">
                    <i class="bi {{ $user->hasVerifiedEmail() ? 'bi-patch-check-fill' : 'bi-exclamation-circle-fill' }}"></i>
                    <span>{{ $user->hasVerifiedEmail() ? 'Email đã xác thực' : 'Email chưa xác thực' }}</span>
                </div>
                <div class="account-meta-list mt-4">
                    <div><i class="bi bi-calendar3"></i><span>Thành viên từ<strong>{{ $user->created_at?->format('d/m/Y') }}</strong></span></div>
                    <div><i class="bi bi-shield-check"></i><span>Bảo mật<strong>Mật khẩu riêng tư</strong></span></div>
                </div>
                @if (!$user->hasVerifiedEmail())
                    <a href="{{ route('verification.notice') }}" class="btn btn-outline-primary w-100 rounded-pill mt-4">Xác thực email</a>
                @endif
            </div>
        </div>

        <div class="col-lg-8">
            <div class="account-panel mb-4">
                <div class="account-panel-heading">
                    <div><span class="account-panel-icon"><i class="bi bi-person-lines-fill"></i></span><div><h3>Thông tin cá nhân</h3><p class="mb-0">Cập nhật thông tin hiển thị của bạn.</p></div></div>
                </div>
                <form action="{{ route('account.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required autocomplete="name">
                            @error('name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Địa chỉ email</label>
                            <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="email">
                            @error('email')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check2 me-2"></i>Lưu thay đổi</button>
                    </div>
                </form>
            </div>

            <div class="account-panel account-security-panel">
                <div class="account-panel-heading">
                    <div><span class="account-panel-icon account-panel-icon-gold"><i class="bi bi-shield-lock-fill"></i></span><div><h3>Bảo mật tài khoản</h3><p class="mb-0">Đổi mật khẩu định kỳ để tài khoản luôn an toàn.</p></div></div>
                    <a href="{{ route('password.change') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Đổi mật khẩu</a>
                </div>
                <div class="account-security-note"><i class="bi bi-lock-fill"></i><span>Mật khẩu của bạn được mã hóa và không hiển thị cho bất kỳ ai.</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
