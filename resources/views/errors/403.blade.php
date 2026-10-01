@extends('layouts.app')
@section('title', '403 - Không có quyền truy cập | BeatyCare 🌸')

@section('content')
<div class="container py-5 text-center my-auto" style="min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <div class="error-illustration mb-4">
        <svg width="180" height="180" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="100" cy="100" r="90" fill="#fff7ed" stroke="#fed7aa" stroke-width="4"/>
            <rect x="70" y="90" width="60" height="50" rx="10" fill="#ea580c"/>
            <path d="M80 90V75C80 63.9543 88.9543 55 100 55C111.046 55 120 63.9543 120 75V90" stroke="#ea580c" stroke-width="8" stroke-linecap="round"/>
            <circle cx="100" cy="112" r="5" fill="#fff"/>
            <path d="M100 117V126" stroke="#fff" stroke-width="4" stroke-linecap="round"/>
        </svg>
    </div>
    <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-semibold mb-2">Khu vực bảo vệ</span>
    <h1 class="display-6 fw-bold text-dark mt-2 mb-3">Bạn không có quyền truy cập khu vực này 🔒</h1>
    <p class="text-muted mx-auto mb-4" style="max-width: 520px; line-height: 1.7;">
        Trang này yêu cầu quyền quản trị viên hoặc tài khoản của bạn chưa được cấp phép. Vui lòng đăng nhập với tài khoản phù hợp hoặc quay về trang chủ.
    </p>
    <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="{{ route('welcome') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
            <i class="bi bi-house-door me-2"></i>Về trang chủ
        </a>
        <a href="{{ route('login') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">
            <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập tài khoản khác
        </a>
    </div>
</div>
@endsection
