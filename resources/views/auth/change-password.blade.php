@extends('layouts.app')
@section('title', 'Đổi mật khẩu')

@section('content')
<div class="account-page password-page py-3 py-lg-4">
    <div class="account-page-heading mb-4">
        <div>
            <span class="account-kicker">BẢO MẬT TÀI KHOẢN</span>
            <h1 class="mb-2">Đổi mật khẩu</h1>
            <p class="text-muted mb-0">Tạo một mật khẩu mới mạnh hơn để bảo vệ không gian riêng của bạn.</p>
        </div>
        <a href="{{ route('account') }}" class="btn btn-outline-primary rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i>Về tài khoản</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="account-panel password-panel">
                <div class="password-panel-intro">
                    <span class="account-panel-icon account-panel-icon-gold"><i class="bi bi-key-fill"></i></span>
                    <div><h2>Thông tin mật khẩu</h2><p class="mb-0">Bạn sẽ cần nhập mật khẩu hiện tại để xác nhận thay đổi.</p></div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success border-0 rounded-4"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4"><i class="bi bi-exclamation-circle me-2"></i>Vui lòng kiểm tra lại thông tin bên dưới.</div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                        <input id="current_password" type="password" name="current_password" class="form-control" required autocomplete="current-password">
                        @error('current_password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input id="new_password" type="password" name="new_password" class="form-control" required minlength="8" autocomplete="new-password">
                            @error('new_password')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <input id="new_password_confirmation" type="password" name="new_password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="password-hint mt-3"><i class="bi bi-info-circle"></i><span>Mật khẩu mới cần có ít nhất 8 ký tự và không nên trùng với mật khẩu cũ.</span></div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('account') }}" class="btn btn-light rounded-pill px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-shield-check me-2"></i>Cập nhật mật khẩu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection