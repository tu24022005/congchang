@extends('layouts.app')
@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="chill-container">
    <div class="chill-card">
        <div class="chill-header">
            <h3>Đặt lại mật khẩu</h3>
            <p>Tạo mật khẩu mới cho tài khoản của bạn</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.store') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-3">
                <label for="email" class="chill-label">Địa chỉ Email</label>
                <input type="email" name="email" id="email" class="form-control chill-input" value="{{ old('email', $email) }}" pattern="^[^@\s]+@[^@\s]+\.[A-Za-z]{2,}$" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="chill-label">Mật khẩu mới</label>
                <input type="password" name="password" id="password" class="form-control chill-input" minlength="8" required autocomplete="new-password">
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="chill-label">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control chill-input" minlength="8" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn chill-btn w-100">Lưu mật khẩu mới</button>
        </form>
    </div>
</div>
@endsection
