@extends('layouts.app')
@section('title', '503 - Hệ thống đang nâng cấp | BeatyCare 🌸')

@section('content')
<div class="container py-5 text-center my-auto" style="min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <div class="error-illustration mb-4">
        <svg width="180" height="180" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="animate__animated animate__rotateIn animate__infinite" style="animation-duration: 12s;">
            <circle cx="100" cy="100" r="90" fill="#fdf4ff" stroke="#f0abfc" stroke-width="4"/>
            <!-- Flower gear decoration -->
            <path d="M100 40V55M100 145V160M40 100H55M145 100H160M58 58L68 68M132 132L142 142M58 142L68 132M132 68L142 58" stroke="#c084fc" stroke-width="8" stroke-linecap="round"/>
            <circle cx="100" cy="100" r="45" fill="#a855f7" opacity="0.9"/>
            <circle cx="100" cy="100" r="22" fill="#fff"/>
        </svg>
    </div>
    <span class="badge bg-purple-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Đang nâng cấp</span>
    <h1 class="display-6 fw-bold text-dark mt-2 mb-3">Hệ thống đang được bảo trì nâng cấp 🌸</h1>
    <p class="text-muted mx-auto mb-4" style="max-width: 520px; line-height: 1.7;">
        BeatyCare đang nâng cấp hệ thống để mang đến cho bạn trải nghiệm mua sắm nhanh hơn, mượt mà hơn và nhiều ưu đãi mới. Chúng tôi sẽ trở lại rất sớm!
    </p>
    <div class="d-flex flex-wrap justify-content-center gap-3">
        <button type="button" onclick="window.location.reload()" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
            <i class="bi bi-arrow-clockwise me-2"></i>Kiểm tra lại
        </button>
        <a href="tel:0900000000" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">
            <i class="bi bi-headset me-2"></i>Hỗ trợ đơn hàng: 1900 6868
        </a>
    </div>
</div>
@endsection
