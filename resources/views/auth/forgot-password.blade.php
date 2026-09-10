@extends('layouts.app')
@section('title', 'Quên mật khẩu')

@section('content')
<div class="chill-container">
    <div class="chill-card">
        <div class="chill-header">
            <h3>Khôi phục mật khẩu</h3>
            <p>Nhập email để nhận liên kết đặt lại mật khẩu</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="email" class="chill-label">Địa chỉ Email</label>
                <input type="email" name="email" id="email" class="form-control chill-input" value="{{ old('email') }}" required autofocus placeholder="email@example.com">
            </div>
            <button type="submit" class="btn chill-btn w-100">Gửi liên kết đặt lại</button>
            <div class="text-center mt-4"><a href="{{ route('login') }}" class="chill-link"><i class="bi bi-arrow-left me-1"></i>Quay lại đăng nhập</a></div>
        </form>
    </div>
</div>
@endsection
