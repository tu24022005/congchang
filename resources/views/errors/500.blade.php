@extends('layouts.app')
@section('title', '500 - Sự cố máy chủ | BeatyCare 🌸')

@section('content')
<div class="container py-5 text-center my-auto view-inline-1">
    <div class="error-illustration mb-4">
        <svg width="180" height="180" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="100" cy="100" r="90" fill="#f8fafc" stroke="#e2e8f0" stroke-width="4"/>
            <circle cx="100" cy="100" r="40" fill="#64748b" opacity="0.2"/>
            <path d="M100 65V105" stroke="#ef4444" stroke-width="8" stroke-linecap="round"/>
            <circle cx="100" cy="125" r="5" fill="#ef4444"/>
        </svg>
    </div>
    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold mb-2">Sự cố kỹ thuật</span>
    <h1 class="display-6 fw-bold text-dark mt-2 mb-3">Đã xảy ra sự cố ngoài ý muốn 🛠️</h1>
    <p class="text-muted mx-auto mb-4 view-inline-2">
        Thành thật xin lỗi bạn vì sự gián đoạn này. Đội ngũ kỹ thuật của BeatyCare đã ghi nhận và đang khẩn trương xử lý. Bạn có thể thử tải lại trang hoặc liên hệ hotline để được hỗ trợ tức thì.
    </p>
    <div class="d-flex flex-wrap justify-content-center gap-3">
        <button type="button" onclick="window.location.reload()" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
            <i class="bi bi-arrow-clockwise me-2"></i>Thử tải lại
        </button>
        <a href="tel:0900000000" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold">
            <i class="bi bi-telephone me-2"></i>Hotline CSKH: 1900 6868
        </a>
        <a href="{{ route('welcome') }}" class="btn btn-light border rounded-pill px-4 py-2 fw-semibold">
            <i class="bi bi-house-door me-2"></i>Về trang chủ
        </a>
    </div>
</div>
@endsection
