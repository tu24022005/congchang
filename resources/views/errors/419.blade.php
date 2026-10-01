@extends('layouts.app')
@section('title', '419 - Phiên làm việc đã hết hạn | BeatyCare 🌸')

@section('content')
<div class="container py-5 text-center my-auto view-inline-1">
    <div class="error-illustration mb-4">
        <svg width="180" height="180" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="100" cy="100" r="90" fill="#f0fdf4" stroke="#bbf7d0" stroke-width="4"/>
            <circle cx="100" cy="100" r="45" fill="none" stroke="#16a34a" stroke-width="8" stroke-dasharray="10 5"/>
            <path d="M100 70V100L120 115" stroke="#16a34a" stroke-width="6" stroke-linecap="round"/>
        </svg>
    </div>
    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold mb-2">Bảo mật phiên</span>
    <h1 class="display-6 fw-bold text-dark mt-2 mb-3">Phiên làm việc đã hết hạn ⏳</h1>
    <p class="text-muted mx-auto mb-4 view-inline-2">
        Để bảo vệ an toàn cho dữ liệu và giỏ hàng của bạn, phiên làm việc đã tạm dừng do không có hoạt động trong thời gian dài. Vui lòng tải lại trang để tiếp tục nhé!
    </p>
    <div class="d-flex flex-wrap justify-content-center gap-3">
        <button type="button" onclick="window.location.reload()" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
            <i class="bi bi-arrow-clockwise me-2"></i>Tải lại trang ngay
        </button>
        <a href="{{ route('welcome') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">
            <i class="bi bi-house-door me-2"></i>Về trang chủ
        </a>
    </div>
</div>
@endsection
