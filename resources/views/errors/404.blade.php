@extends('layouts.app')
@section('title', '404 - Không tìm thấy trang | BeatyCare 🌸')

@section('content')
<div class="container py-5 text-center my-auto view-inline-1">
    <div class="error-illustration mb-4">
        <svg width="180" height="180" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="animate__animated animate__pulse animate__infinite view-inline-2">
            <circle cx="100" cy="100" r="90" fill="#fff1f3" stroke="#fbcfe8" stroke-width="4"/>
            <!-- Flower petals -->
            <circle cx="100" cy="65" r="28" fill="#fda4af" opacity="0.8"/>
            <circle cx="100" cy="135" r="28" fill="#fda4af" opacity="0.8"/>
            <circle cx="65" cy="100" r="28" fill="#f43f5e" opacity="0.7"/>
            <circle cx="135" cy="100" r="28" fill="#f43f5e" opacity="0.7"/>
            <!-- Center circle -->
            <circle cx="100" cy="100" r="32" fill="#fff" stroke="#f43f5e" stroke-width="4"/>
            <!-- 404 text -->
            <text x="100" y="106" font-family="'DM Sans', sans-serif" font-size="20" font-weight="bold" fill="#e11d48" text-anchor="middle" dominant-baseline="middle">404</text>
        </svg>
    </div>
    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold mb-2">Trang không tồn tại</span>
    <h1 class="display-6 fw-bold text-dark mt-2 mb-3">Ôi! Trang bạn tìm đã lạc trong vườn hoa 🌸</h1>
    <p class="text-muted mx-auto mb-4 view-inline-3">
        Đường dẫn bạn vừa truy cập có thể đã đổi địa chỉ, bị xóa hoặc tạm thời không khả dụng. Hãy để Aloha Beauty dẫn bạn trở lại nhé!
    </p>
    <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="{{ route('welcome') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
            <i class="bi bi-house-door me-2"></i>Về trang chủ
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold">
            <i class="bi bi-stars me-2"></i>Khám phá sản phẩm
        </a>
    </div>
</div>
@endsection
