@extends('layouts.app') 
@section('title', 'Aloha - Đăng nhập') 
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-login-shell chill-container">
    <div class="chill-card">
        
        <div class="chill-header">
            <h3>Aloha!</h3>
            <p>Chào mừng bạn trở lại bên bờ biển Aloha 🌴</p>
        </div>

        {{-- Thông báo --}}
        @if (session('success')) 
         <div class="alert alert-success auth-success-alert">
         {{ session('success') }} 
         </div> 
        @endif 
        
        @if (session('error')) 
         <div class="alert alert-danger auth-danger-alert">
         {{ session('error') }} 
         </div> 
        @endif 

        <form action="{{ route('login') }}" method="POST"> 
            @csrf 
            
            <div class="mb-4"> 
                <label for="email" class="chill-label">Địa chỉ Email</label> 
                <input type="email" name="email" id="email" class="form-control chill-input" value="{{ old('email') }}" required placeholder="Nhập email của bạn..." autocomplete="username">
                @error('email') 
                <span class="text-danger small mt-2 d-block">{{ $message }}</span> 
                @enderror 
            </div> 
            
            <div class="mb-4"> 
                <label for="password" class="chill-label">Mật khẩu</label> 
                <input type="password" name="password" id="password" class="form-control chill-input" required minlength="8" placeholder="Nhập mật khẩu..." autocomplete="current-password">
                @error('password') 
                <span class="text-danger small mt-2 d-block">{{ $message }}</span> 
                @enderror 
            </div> 

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input shadow-none" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label chill-checkbox-label" for="remember">
                        Ghi nhớ tôi
                    </label>
                </div>
                <a href="{{ route('password.request') }}" class="chill-link">Quên mật khẩu?</a>
            </div>
            
            <div class="d-grid mt-2">
                <button type="submit" class="btn chill-btn">Bắt Đầu Khám Phá</button> 
            </div>

            <div class="text-center mt-3 mb-3">
                <span class="text-muted auth-divider-label">hoặc đăng nhập bằng</span>
            </div>

            <a href="{{ route('google.login') }}" class="btn btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center fw-bold shadow-sm mb-3 auth-google-button">
                <i class="bi bi-google fs-5 me-2"></i> Google
            </a>

            <div class="text-center mt-4 pt-3 auth-register-divider">
                <span class="auth-register-label">Chưa có tài khoản?</span>
                <a href="{{ route('register') }}" class="chill-link auth-register-link">Đăng ký ngay</a>
            </div>
        </form>
    </div>
</div>
@endsection