@extends('layouts.app')
@section('title', 'Đổi mật khẩu')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card border-0 shadow-lg rounded-4 auth-glass-card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold auth-title"><i class="bi bi-shield-lock text-warning me-2"></i>Đổi Mật Khẩu</h3>
                    <p class="text-muted small">Bảo mật tài khoản của bạn</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success rounded-3 border-0 shadow-sm">{{ session('success') }}</div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">MẬT KHẨU HIỆN TẠI</label>
                        <input type="password" name="current_password" class="form-control rounded-3 py-2" required>
                        @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">MẬT KHẨU MỚI</label>
                        <input type="password" name="new_password" class="form-control rounded-3 py-2" required>
                        @error('new_password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">XÁC NHẬN MẬT KHẨU MỚI</label>
                        <input type="password" name="new_password_confirmation" class="form-control rounded-3 py-2" required>
                    </div>

                    <button type="submit" class="btn btn-info w-100 text-white fw-bold rounded-pill py-2 shadow-sm auth-submit-button">
                        CẬP NHẬT MẬT KHẨU
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection