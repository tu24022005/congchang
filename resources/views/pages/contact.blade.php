@extends('layouts.app')
@section('title', 'Liên hệ - Aloha Beauty')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5"><span class="text-primary fw-bold small text-uppercase">Liên hệ</span><h1 class="fw-bold mt-2">Aloha Beauty luôn sẵn sàng hỗ trợ</h1><p class="text-muted">Hãy liên hệ với chúng mình khi bạn cần tư vấn về sản phẩm hoặc đơn hàng.</p></div>
    <div class="row g-4 justify-content-center">
        <div class="col-md-4"><div class="card border-0 shadow-sm h-100 p-4 text-center"><i class="bi bi-envelope-heart text-primary fs-1 mb-3"></i><h5>Email</h5><a href="mailto:contact@phungthanhtuc.com">contact@phungthanhtuc.com</a><p class="text-muted small mt-2 mb-0">Phản hồi trong giờ hành chính.</p></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm h-100 p-4 text-center"><i class="bi bi-telephone text-success fs-1 mb-3"></i><h5>Hotline</h5><a href="tel:0338054668">0338 054 668</a><p class="text-muted small mt-2 mb-0">Hỗ trợ mỗi ngày từ 8:00 - 21:00.</p></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm h-100 p-4 text-center"><i class="bi bi-chat-dots text-warning fs-1 mb-3"></i><h5>Chat hỗ trợ</h5><p class="text-muted mb-2">Đăng nhập để trò chuyện trực tiếp với CSKH.</p><a href="{{ route('login') }}">Đăng nhập</a></div></div>
    </div>
</div>
@endsection
