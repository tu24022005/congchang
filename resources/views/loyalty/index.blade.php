@extends('layouts.app')
@section('title', 'Điểm thành viên')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><span class="cart-eyebrow">ALOHA BEAUTY / ƯU ĐÃI</span><h2 class="fw-bold">Điểm thành viên</h2></div>
        <a href="{{ route('account') }}" class="btn btn-outline-secondary rounded-pill">Tài khoản</a>
    </div>
    @if(session('success'))<div class="alert alert-success rounded-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger rounded-3">{{ session('error') }}</div>@endif
    <div class="card border-0 shadow-sm rounded-4 mb-4"><div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div><div class="text-muted">Điểm hiện có</div><div class="display-5 fw-bold text-primary">{{ number_format($user->loyalty_points) }} điểm</div><small class="text-muted">100 điểm = voucher giảm 10.000đ</small></div>
        <form action="{{ route('loyalty.redeem') }}" method="POST">@csrf<button class="btn btn-primary rounded-pill px-4" {{ $user->loyalty_points < 100 ? 'disabled' : '' }}>Đổi voucher</button></form>
    </div></div>
    <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4"><h5 class="fw-bold">Lịch sử điểm</h5>
        @forelse($transactions as $transaction)<div class="d-flex justify-content-between border-bottom py-3"><span>{{ $transaction->description }}</span><strong class="{{ $transaction->points > 0 ? 'text-success' : 'text-danger' }}">{{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }}</strong></div>@empty<p class="text-muted mb-0">Chưa có giao dịch điểm.</p>@endforelse
        {{ $transactions->links() }}
    </div></div>
</div>
@endsection
