@extends('layouts.app')
@section('title', 'Tiền hoàn của tôi')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <span class="text-primary small fw-bold"><i class="bi bi-wallet2 me-1"></i> TÀI KHOẢN CỦA TÔI</span>
        <h2 class="fw-bold mt-1">Tiền hoàn của tôi</h2>
        <p class="text-muted mb-0">Theo dõi các khoản hoàn tiền từ đơn hàng online đã hủy.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6"><div class="card border-0 shadow-sm rounded-4 p-4 h-100"><small class="text-muted">Đang chờ hoàn</small><h3 class="text-warning fw-bold mb-0">{{ number_format($pendingTotal, 0, ',', '.') }} đ</h3></div></div>
        <div class="col-md-6"><div class="card border-0 shadow-sm rounded-4 p-4 h-100"><small class="text-muted">Đã hoàn về tài khoản</small><h3 class="text-success fw-bold mb-0">{{ number_format($refundedTotal, 0, ',', '.') }} đ</h3></div></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 p-4"><h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Lịch sử tiền hoàn</h5></div>
        <div class="card-body p-0">
            @forelse($orders as $order)
                <div class="p-4 border-top">
                    <div class="d-flex flex-wrap justify-content-between gap-2">
                        <div><a href="{{ route('orders.show', $order) }}" class="fw-bold text-decoration-none">Đơn hàng #{{ $order->id }}</a><div class="small text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</div></div>
                        <div class="text-end"><strong class="d-block text-danger">{{ number_format($order->total, 0, ',', '.') }} đ</strong><span class="badge {{ $order->status === 'refunded' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $order->status === 'refunded' ? 'Đã hoàn tiền' : 'Chờ hoàn tiền' }}</span></div>
                    </div>
                    @if($order->refund_bank_name)
                        <div class="small text-muted mt-3"><i class="bi bi-bank me-1"></i>{{ $order->refund_bank_name }} · {{ $order->refund_account_number }} · {{ $order->refund_account_holder }}</div>
                    @endif
                    @if($order->status === 'refunded')
                        <div class="small text-success mt-1"><i class="bi bi-check-circle me-1"></i>Mã giao dịch: {{ $order->refund_reference ?: 'Đã hoàn tiền' }}{{ $order->refunded_at ? ' · ' . $order->refunded_at->format('d/m/Y H:i') : '' }}</div>
                        @if($order->refund_note)<div class="small text-muted mt-1">{{ $order->refund_note }}</div>@endif
                    @else
                        <div class="small text-warning-emphasis mt-1">Shop đang xử lý chuyển khoản hoàn tiền.</div>
                    @endif
                </div>
            @empty
                <div class="p-5 text-center text-muted"><i class="bi bi-wallet2 fs-1 d-block mb-3"></i>Chưa có khoản tiền hoàn nào.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
