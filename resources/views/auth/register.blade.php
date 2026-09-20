@extends('layouts.app')
@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="auth-register-shell row justify-content-center mt-5 mb-5">
    <div class="col-md-5">
        <!-- Thẻ Card phong cách Thần Mặt Trời -->
        <div class="card border-0 shadow-lg auth-register-card">
            <div class="card-body p-5 text-white">
                
                <!-- Tiêu đề & Icon phát sáng -->
                <div class="text-center mb-4">
                    <i class="bi bi-sun-fill auth-register-icon"></i>
                    <h2 class="fw-bold mt-2 auth-register-heading">ĐĂNG KÝ TÀI KHOẢN</h2>
                    <p class="mb-0 text-light fw-medium">Cùng Aloha đón nắng, đón gió và chăm sóc làn da mỗi ngày</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <!-- Ô nhập Họ và Tên -->
                    <div class="form-floating mb-3 text-dark">
                        <input type="text" class="form-control fw-bold auth-register-input" id="name" name="name" placeholder="Họ và tên" required autofocus>
                        <label for="name"><i class="bi bi-person-fill text-warning"></i> Họ và tên</label>
                    </div>

                    <!-- Ô nhập Email -->
                    <div class="form-floating mb-3 text-dark">
                        <input type="email" class="form-control fw-bold auth-register-input" id="email" name="email" placeholder="name@example.com" pattern="^[^@\s]+@[^@\s]+\.[A-Za-z]{2,}$" required>
                        <label for="email"><i class="bi bi-envelope-fill text-warning"></i> Địa chỉ Email</label>
                    </div>

                    <!-- Ô nhập Mật khẩu -->
                    <div class="form-floating mb-3 text-dark">
                        <input type="password" class="form-control fw-bold auth-register-input" id="password" name="password" placeholder="Mật khẩu (tối thiểu 8 ký tự)" minlength="8" required>
                        <label for="password"><i class="bi bi-lock-fill text-warning"></i> Mật khẩu</label>
                    </div>

                    <!-- Ô Xác nhận Mật khẩu -->
                    <div class="form-floating mb-4 text-dark">
                        <input type="password" class="form-control fw-bold auth-register-input" id="password_confirmation" name="password_confirmation" placeholder="Xác nhận mật khẩu" minlength="8" required>
                        <label for="password_confirmation"><i class="bi bi-shield-lock-fill text-warning"></i> Xác nhận mật khẩu</label>
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
@endsection