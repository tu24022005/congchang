@extends('layouts.app')
@section('title', 'Đăng ký tài khoản')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        /* DARK OVERLAY */
        .dark-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: 1; /* above body background, below form container */
            pointer-events: none;
        }
        body.dark-mode .dark-overlay {
            opacity: 1;
        }

        /* CARD DARK MODE (REGISTER SPECIFIC) */
        .auth-register-card, .auth-register-input, .auth-register-submit, .auth-register-login-link, .form-floating {
            transition: all 0.5s ease;
        }
        
        body.dark-mode .auth-register-card {
            background: rgba(20, 25, 35, 0.85) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6) !important;
        }
        body.dark-mode .auth-register-card::before {
            background: linear-gradient(90deg, #60a5fa, #3b82f6, #60a5fa) !important;
        }
        body.dark-mode .form-floating.text-dark {
            color: #f1f5f9 !important;
        }
        body.dark-mode .auth-register-input {
            background: rgba(0, 0, 0, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
        }
        body.dark-mode .auth-register-input:-webkit-autofill,
        body.dark-mode .auth-register-input:-webkit-autofill:hover, 
        body.dark-mode .auth-register-input:-webkit-autofill:focus, 
        body.dark-mode .auth-register-input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px rgba(20, 25, 35, 1) inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        body.dark-mode .form-floating > .auth-register-input:focus::placeholder {
            color: rgba(255,255,255,0.5) !important;
        }
        body.dark-mode .auth-register-input:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 0.25rem rgba(96, 165, 250, 0.25) !important;
        }
        body.dark-mode label[for] {
            color: #94a3b8 !important;
        }
        body.dark-mode .auth-register-submit {
            background: linear-gradient(90deg, #3b82f6, #2563eb) !important;
            color: #fff !important;
            border: none !important;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.35) !important;
        }
        body.dark-mode .auth-register-submit i {
            color: #fca5a5 !important;
        }
        body.dark-mode .auth-register-login-link {
            color: #93c5fd !important;
        }
        body.dark-mode .auth-register-login-link:hover {
            color: #bfdbfe !important;
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

<div class="auth-register-shell row justify-content-center mt-4 mb-4" style="position: relative; z-index: 2;">
    <div class="col-12">
        <!-- Thẻ Card phong cách Thần Mặt Trời -->
        <div class="card border-0 shadow-lg auth-register-card">
            <div class="card-body p-5 text-white">
                @if($errors->any())
                    <div class="alert alert-warning auth-register-alert mb-4">
                        <i class="bi bi-info-circle me-1"></i> Vui lòng kiểm tra lại thông tin đăng ký.
                    </div>
                @endif
                
                <!-- Tiêu đề & Icon phát sáng -->
                <div class="text-center mb-4">
                    <i class="bi bi-sun-fill auth-register-icon"></i>
                    <h2 class="fw-bold mt-2 auth-register-heading">ĐĂNG KÝ TÀI KHOẢN</h2>
                    <p class="mb-0 text-light fw-medium">Cùng BeatyCare 🌸 đón nắng, đón gió và chăm sóc làn da mỗi ngày</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <!-- Ô nhập Họ và Tên -->
                    <div class="form-floating mb-3 text-dark">
                        <input type="text" class="form-control fw-bold auth-register-input @error('name') is-invalid shake-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Họ và tên" autocomplete="name" required autofocus>
                        <label for="name"><i class="bi bi-person-fill text-warning"></i> Họ và tên</label>
                    </div>

                    <!-- Ô nhập Email -->
                    <div class="form-floating mb-3 text-dark">
                        <input type="email" class="form-control fw-bold auth-register-input @error('email') is-invalid shake-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" pattern="^[^@\s]+@[^@\s]+\.[A-Za-z]{2,}$" autocomplete="email" required>
                        <label for="email"><i class="bi bi-envelope-fill text-warning"></i> Địa chỉ Email</label>
                    </div>

                    <div class="form-floating mb-3 text-dark">
                        <input type="tel" class="form-control fw-bold auth-register-input @error('phone') is-invalid shake-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="0987654321" pattern="(0|\+84)(3|5|7|8|9)[0-9]{8}" maxlength="12" autocomplete="tel" required>
                        <label for="phone"><i class="bi bi-phone-fill text-warning"></i> Số điện thoại</label>
                    </div>

                    <!-- Ô nhập Mật khẩu -->
                    <div class="form-floating mb-3 text-dark">
                        <input type="password" class="form-control fw-bold auth-register-input @error('password') is-invalid shake-invalid @enderror" id="password" name="password" placeholder="Mật khẩu (tối thiểu 8 ký tự)" minlength="8" autocomplete="new-password" required>
                        <label for="password"><i class="bi bi-lock-fill text-warning"></i> Mật khẩu</label>
                    </div>
                    <div class="password-strength mb-3" id="password-strength" aria-live="polite"><span class="password-strength-bar"></span><small>Nhập mật khẩu để kiểm tra độ mạnh</small></div>

                    <!-- Ô Xác nhận Mật khẩu -->
                    <div class="form-floating mb-4 text-dark">
                        <input type="password" class="form-control fw-bold auth-register-input" id="password_confirmation" name="password_confirmation" placeholder="Xác nhận mật khẩu" minlength="8" autocomplete="new-password" required>
                        <label for="password_confirmation"><i class="bi bi-shield-lock-fill text-warning"></i> Xác nhận mật khẩu</label>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" {{ old('terms') ? 'checked' : '' }} required>
                        <label class="form-check-label small" for="terms">Tôi đồng ý với <a href="{{ route('pages.policies') }}" class="auth-register-login-link">điều khoản và chính sách</a> của BeatyCare 🌸.</label>
                    </div>

                    <!-- Nút Đăng ký (Nút mạ vàng) -->
                    <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-lg fw-bold text-dark text-uppercase auth-register-submit">
                            <i class="bi bi-fire text-danger"></i> Khởi tạo quyền năng
                        </button>
                    </div>
                </form>
                
                <!-- Link Đăng nhập -->
                <div class="text-center mt-4">
                    <span class="text-light">Đã có tài khoản?</span> 
                    <a href="{{ route('login') }}" class="fw-bold auth-register-login-link">
                        Đăng nhập tại đây
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('js/register.js') }}"></script>
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

    document.querySelector('form').addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        if (btn && this.checkValidity()) {
            btn.classList.add('btn-loading');
        }
    });
</script>
@endpush
@endsection