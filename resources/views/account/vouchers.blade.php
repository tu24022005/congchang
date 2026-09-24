@extends('layouts.app')
@section('title', 'Voucher của tôi')
@section('content')
<div class="account-page py-3 py-lg-4">
    <div class="account-page-heading mb-4"><div><span class="account-kicker">ƯU ĐÃI CỦA BẠN</span><h1 class="mb-2">Voucher</h1><p class="text-muted mb-0">Các mã giảm giá được cấp riêng cho tài khoản của bạn.</p></div><a href="{{ route('account') }}" class="btn btn-outline-primary rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i>Về tài khoản</a></div>
    @if(session('success'))<div class="alert alert-success border-0 rounded-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger border-0 rounded-3">{{ session('error') }}</div>@endif
    <h5 class="fw-bold mb-3"><i class="bi bi-ticket-perforated-fill text-warning me-2"></i>Voucher của tôi</h5>
    <div class="row g-3 mb-4">
        @forelse($vouchers as $voucher)
            <div class="col-md-6"><div class="account-voucher-card"><div class="account-voucher-code"><i class="bi bi-ticket-perforated-fill"></i><strong>{{ $voucher->code }}</strong></div><div><strong>{{ $voucher->type === 'percent' ? $voucher->value . '% giảm giá' : ($voucher->type === 'free_shipping' ? 'Miễn phí vận chuyển' : 'Giảm ' . number_format($voucher->value, 0, ',', '.') . 'đ') }}</strong><small>Đơn từ {{ number_format($voucher->min_order_value, 0, ',', '.') }}đ · Áp dụng: {{ $voucher->applies_to === 'category' ? ($voucher->category->name ?? 'Danh mục') : ($voucher->applies_to === 'product' ? ($voucher->product->name ?? 'Sản phẩm') : 'Toàn shop') }} · {{ $voucher->expires_at ? 'HSD: ' . $voucher->expires_at->format('d/m/Y') : 'Không thời hạn' }}</small></div><a href="{{ route('cart.index') }}" class="btn btn-sm btn-primary rounded-pill">Dùng ngay</a></div></div>
        @empty
            <div class="col-12"><div class="account-empty-state"><i class="bi bi-ticket-perforated"></i><h4>Chưa có voucher</h4><p class="text-muted">Voucher đổi từ điểm sẽ xuất hiện tại đây.</p><a href="{{ route('loyalty.index') }}" class="btn btn-primary rounded-pill">Đổi điểm lấy voucher</a></div></div>
        @endforelse
    </div>
    <h5 class="fw-bold mb-3"><i class="bi bi-stars text-primary me-2"></i>Voucher đang có</h5>
    <div class="row g-3">
        @forelse($availableVouchers as $voucher)
            <div class="col-md-6"><div class="account-voucher-card"><div class="account-voucher-code"><i class="bi bi-ticket-perforated-fill"></i><strong>{{ $voucher->code }}</strong></div><div><strong>{{ $voucher->type === 'percent' ? $voucher->value . '% giảm giá' : 'Giảm ' . number_format($voucher->value, 0, ',', '.') . 'đ' }}</strong><small>Đơn từ {{ number_format($voucher->min_order_value, 0, ',', '.') }}đ · Áp dụng: {{ $voucher->applies_to === 'category' ? ($voucher->category->name ?? 'Danh mục') : ($voucher->applies_to === 'product' ? ($voucher->product->name ?? 'Sản phẩm') : 'Toàn shop') }} · {{ $voucher->expires_at ? 'HSD: ' . $voucher->expires_at->format('d/m/Y') : 'Không thời hạn' }}</small></div><form method="POST" action="{{ route('account.vouchers.collect', $voucher) }}">@csrf<button class="btn btn-sm btn-outline-primary rounded-pill">Thu thập</button></form></div></div>
        @empty
            <div class="col-12"><p class="text-muted">Hiện chưa có voucher công khai mới.</p></div>
        @endforelse
    </div>
</div>
@endsection
