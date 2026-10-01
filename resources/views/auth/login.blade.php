@extends('layouts.app') 
@section('title', 'BeatyCare 🌸 - Đăng nhập') 
@push('styles')
    <link rel="stylesheet" href="{{ asset_v('css/auth.css') }}">
    <style>
        /* DARK OVERLAY - LỚP PHỦ ĐEN KHI BẬT DARK MODE (VỪA ĐỦ ĐỂ THẤY BÃI BIỂN) */
        .dark-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: 1; /* trên ảnh nền bãi biển, dưới form đăng nhập */
            pointer-events: none;
        }
        body.dark-mode .dark-overlay {
            opacity: 1;
        }

        /* CARD DARK MODE */
        .chill-card, .chill-input, .chill-label, .chill-btn, .chill-link, .auth-google-button,
        .chill-header h3, .chill-header p, .form-check-label, .auth-divider-label, .auth-register-label {
            transition: all 0.5s ease;
        }
        
        body.dark-mode .chill-card {
            background: rgba(20, 25, 35, 0.85) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6) !important;
        }
        body.dark-mode .chill-header h3,
        body.dark-mode .chill-header p,
        body.dark-mode .form-check-label,
        body.dark-mode .auth-divider-label,
        body.dark-mode .auth-register-label {
            color: #f1f5f9 !important;
        }
        body.dark-mode .chill-input {
            background: rgba(0, 0, 0, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
        }
        body.dark-mode .chill-input:-webkit-autofill,
        body.dark-mode .chill-input:-webkit-autofill:hover, 
        body.dark-mode .chill-input:-webkit-autofill:focus, 
        body.dark-mode .chill-input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px rgba(20, 25, 35, 1) inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        body.dark-mode .form-floating > .chill-input:focus::placeholder {
            color: rgba(255,255,255,0.5) !important;
        }
        body.dark-mode .chill-input:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 0.25rem rgba(96, 165, 250, 0.25) !important;
        }
        body.dark-mode .chill-label {
            color: #94a3b8 !important;
        }
        body.dark-mode .chill-link {
            color: #93c5fd !important;
        }
        body.dark-mode .chill-link:hover {
            color: #bfdbfe !important;
        }
        body.dark-mode .btn.chill-btn {
            background: linear-gradient(90deg, #3b82f6, #2563eb) !important;
            color: #fff !important;
            border: none !important;
        }
        body.dark-mode .auth-google-button {
            background: rgba(255,255,255,0.05);
            color: #f87171 !important;
            border-color: #f87171;
        }
        body.dark-mode .auth-google-button:hover {
            background: #f87171;
            color: #fff !important;
        }

        /* --- 2. FAIRY LIGHTS --- */
        .fairy-lights-container {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100vw;
            height: 70px;
            pointer-events: none;
            z-index: 10;
            overflow: hidden;
        }
        .light-wire {
            position: absolute;
            top: -55px;
            left: -5%;
            width: 110%;
            height: 80px;
            border-bottom: 2px solid rgba(0, 0, 0, 0.12);
            border-radius: 50%;
            transition: border-color 0.5s ease;
        }
        body.dark-mode .light-wire {
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }
        .light-bulb {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 0 5px 1px rgba(255, 255, 255, 0.4), 0 0 10px 2px rgba(255, 223, 0, 0.2);
            animation: twinkle-light 2s infinite alternate;
            opacity: 0.5;
            transition: all 0.5s ease;
        }
        body.dark-mode .light-bulb {
            opacity: 1;
            animation-name: twinkle-dark;
            box-shadow: 0 0 10px 2px rgba(255, 255, 255, 0.6), 0 0 15px 4px rgba(255, 223, 0, 0.4);
        }

        @keyframes twinkle-light {
            0% { opacity: 0.3; transform: scale(0.9); box-shadow: 0 0 3px 1px rgba(255, 255, 255, 0.3), 0 0 6px 1px rgba(255, 223, 0, 0.1); }
            100% { opacity: 0.7; transform: scale(1.1); box-shadow: 0 0 6px 2px rgba(255, 255, 255, 0.6), 0 0 12px 3px rgba(255, 223, 0, 0.3); }
        }

        @keyframes twinkle-dark {
            0% { opacity: 0.4; transform: scale(0.9); box-shadow: 0 0 8px 2px rgba(255, 255, 255, 0.5), 0 0 12px 3px rgba(255, 223, 0, 0.3); }
            100% { opacity: 1; transform: scale(1.2); box-shadow: 0 0 15px 4px rgba(255, 255, 255, 0.9), 0 0 25px 8px rgba(255, 223, 0, 0.7); }
        }

        @media (prefers-reduced-motion: reduce) {
            .light-bulb {
                animation: none !important;
                opacity: 0.8 !important;
            }
        }
        
        @media (max-width: 768px) {
            .light-bulb:nth-child(even) {
                display: none;
            }
            .theme-toggle-btn {
                top: 15px;
                right: 15px;
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }
        }
    </style>
@endpush

@section('content')
<!-- Lớp overlay làm tối nền -->
<div class="dark-overlay"></div>

<!-- Dây đèn trang trí -->
<div class="fairy-lights-container" id="fairy-lights">
    <div class="light-wire"></div>
</div>

<div class="auth-login-shell chill-container" style="position: relative; z-index: 2;">
    <div class="chill-card">
        
        <div class="chill-header">
            <h3>BeatyCare 🌸!</h3>
            <p>Chào mừng bạn trở lại tại BeatyCare 🌸</p>
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
            
            <div class="form-floating form-floating-custom"> 
                <input type="email" name="email" id="email" class="form-control chill-input @error('email') is-invalid shake-invalid @enderror" value="{{ old('email') }}" required placeholder="Địa chỉ Email" autocomplete="username">
                <label for="email" class="chill-label">Địa chỉ Email</label> 
                @error('email') 
                <span class="text-danger small mt-2 d-block">{{ $message }}</span> 
                @enderror 
            </div> 
            
            <div class="form-floating form-floating-custom"> 
                <input type="password" name="password" id="password" class="form-control chill-input @error('password') is-invalid shake-invalid @enderror" required minlength="8" placeholder="Mật khẩu" autocomplete="current-password">
                <label for="password" class="chill-label">Mật khẩu</label> 
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
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- 2. HIỆU ỨNG DÂY ĐÈN ---
        const lightsContainer = document.getElementById('fairy-lights');
        const bulbCount = 45; // Số lượng đèn trải dài toàn bộ chiều rộng
        
        for (let i = 0; i < bulbCount; i++) {
            const bulb = document.createElement('div');
            bulb.className = 'light-bulb';
            
            // Random delay cho animation nhấp nháy từ 0s đến 3s
            const delay = Math.random() * 3;
            bulb.style.animationDelay = `-${delay}s`;
            
            // Tính toán vị trí x và y để tạo độ võng của dây đèn (nằm gọn trên cao, không chạm vào chữ)
            const xPos = (i / (bulbCount - 1)) * 100; // từ 0% đến 100%
            const yPos = Math.sin(Math.PI * (i / (bulbCount - 1))) * 22; // Võng tối đa 22px
            const randomY = Math.random() * 4 - 2;
            
            bulb.style.left = `calc(${xPos}% - 3px)`; // Căn giữa chấm đèn
            bulb.style.top = `${yPos + randomY + 3}px`; // Nằm gọn trên cao sát mép trên
            
            lightsContainer.appendChild(bulb);
        }
    });

    // Code xử lý form loading hiện tại
    document.querySelector('form').addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        if (btn && this.checkValidity()) {
            btn.classList.add('btn-loading');
        }
    });
</script>
@endpush
@endsection