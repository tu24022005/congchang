@extends('layouts.app')
@section('title', 'Đổi mật khẩu - Aloha Beauty')

@section('content')
<div class="account-page password-page py-3 py-lg-4">
    <div class="account-page-heading mb-4">
        <div>
            <span class="account-kicker">BẢO MẬT TÀI KHOẢN</span>
            <h1 class="mb-2">Đổi mật khẩu</h1>
            <p class="text-muted mb-0">Tạo một mật khẩu mới mạnh hơn để bảo vệ an toàn cho tài khoản của bạn.</p>
        </div>
        <a href="{{ route('account') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Về tài khoản
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="account-panel password-panel shadow-sm rounded-4">
                <div class="password-panel-intro">
                    <span class="account-panel-icon account-panel-icon-gold"><i class="bi bi-key-fill"></i></span>
                    <div>
                        <h2>Thông tin mật khẩu & Xác thực OTP</h2>
                        <p class="mb-0">
                            {{ $authenticatedWithGoogle ? 'Bạn vừa đăng nhập bằng Google. Hãy nhập mã xác thực OTP gửi qua email để tạo mật khẩu đăng nhập trực tiếp.' : 'Bạn cần nhập mật khẩu hiện tại và mã OTP gửi qua email đã đăng ký để hoàn tất thay đổi.' }}
                        </p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 bg-success-subtle text-success-emphasis">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>{{ session('success') }}
                    </div>
                @endif
                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 bg-danger-subtle text-danger-emphasis">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>Vui lòng kiểm tra lại các thông tin bên dưới:
                        <ul class="mb-0 mt-2 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- MẬT KHẨU HIỆN TẠI -->
                    <div class="mb-3">
                        @if (!$authenticatedWithGoogle)
                            <label for="current_password" class="form-label fw-semibold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                            <input id="current_password" type="password" name="current_password" class="form-control rounded-3" required autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại...">
                            @error('current_password')<div class="field-error mt-1">{{ $message }}</div>@enderror
                        @else
                            <div class="alert alert-info border-0 rounded-4 mb-0 bg-info-subtle text-info-emphasis">
                                <i class="bi bi-google me-2"></i>Tài khoản đăng nhập qua Google đã được xác thực danh tính. Bạn không cần nhập mật khẩu cũ.
                            </div>
                        @endif
                    </div>

                    <!-- MẬT KHẨU MỚI VÀ XÁC NHẬN -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="new_password" class="form-label fw-semibold">Mật khẩu mới <span class="text-danger">*</span></label>
                            <input id="new_password" type="password" name="new_password" class="form-control rounded-3" required minlength="8" autocomplete="new-password" placeholder="Tối thiểu 8 ký tự...">
                            @error('new_password')<div class="field-error mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="new_password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                            <input id="new_password_confirmation" type="password" name="new_password_confirmation" class="form-control rounded-3" required minlength="8" autocomplete="new-password" placeholder="Nhập lại mật khẩu mới...">
                        </div>
                    </div>
                    <div class="password-hint mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        <span>Mật khẩu mới cần có ít nhất 8 ký tự và không nên trùng với mật khẩu cũ.</span>
                    </div>

                    <!-- KHỐI XÁC THỰC MÃ OTP QUA EMAIL -->
                    <div class="p-3 bg-light rounded-4 border mt-4 mb-4">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-2">
                            <label for="otp" class="form-label fw-bold text-dark mb-0">
                                <i class="bi bi-shield-lock-fill me-1 text-primary"></i>Mã xác thực OTP qua Email <span class="text-danger">*</span>
                            </label>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 text-nowrap">
                                <i class="bi bi-envelope-check me-1"></i>{{ $maskedEmail }}
                            </span>
                        </div>
                        <p class="small text-muted mb-3">
                            Hệ thống sẽ gửi mã số xác thực gồm <strong>6 chữ số</strong> tới địa chỉ email <strong>{{ $maskedEmail }}</strong>. Vui lòng bấm <strong>"Gửi mã OTP"</strong> và nhập mã nhận được vào ô bên dưới.
                        </p>

                        <div class="row g-2 align-items-center">
                            <div class="col-sm-7 col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                    <input id="otp" type="text" name="otp" class="form-control border-start-0 font-monospace fs-5 text-center fw-bold letter-spacing-2" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autocomplete="one-time-code" value="{{ old('otp') }}">
                                </div>
                            </div>
                            <div class="col-sm-5 col-md-4">
                                <button class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm text-nowrap" type="button" id="btnSendOtp">
                                    <i class="bi bi-send-fill me-1" id="btnSendOtpIcon"></i>
                                    <span id="btnSendOtpText">Gửi mã OTP</span>
                                </button>
                            </div>
                        </div>
                        @error('otp')<div class="field-error mt-2">{{ $message }}</div>@enderror

                        <!-- Banner thông báo kết quả gửi OTP -->
                        <div id="otpStatusAlert" class="alert d-none mt-3 mb-0 rounded-3 py-2 px-3 small border-0"></div>
                    </div>

                    <!-- NÚT HÀNH ĐỘNG -->
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                        <a href="{{ route('account') }}" class="btn btn-light border rounded-pill px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="bi bi-shield-check me-2"></i>Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset_v('css/views/auth-change-password-blade-php.css') }}">
@endpush

@push('scripts')
<script src="{{ asset_v('js/views/auth-change-password-blade-php.js') }}" defer></script>
@endpush