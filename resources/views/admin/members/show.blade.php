@extends('layouts.app')
@section('title', 'Chi tiết thành viên')
@section('content')
<div class="container-fluid py-4">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-light border rounded-pill">← Tài khoản khách hàng</a>
        <div>
            <a href="{{ route('admin.customers.edit', $user) }}" class="btn btn-warning rounded-pill">Sửa tài khoản</a>
            <form action="{{ route('admin.customers.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài khoản khách hàng này?');">@csrf @method('DELETE')<button class="btn btn-outline-danger rounded-pill">Xóa tài khoản</button></form>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4">
                <h4 class="fw-bold">{{ $user->name }}</h4><p class="text-muted mb-1">{{ $user->email }}</p>
                <p class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $user->phone ?: 'Chưa cập nhật số điện thoại' }}</p>
                <hr>
                <div class="d-flex justify-content-between mb-3"><span>Điểm hiện có</span><strong class="text-primary">{{ number_format($user->loyalty_points) }}</strong></div>
                <div class="d-flex justify-content-between mb-3"><span>Hạng thành viên</span><strong class="{{ $membershipTier['class'] }}">{{ $membershipTier['name'] }}</strong></div>
                <div class="d-flex justify-content-between mb-3"><span>Doanh số hoàn thành</span><strong>{{ number_format($user->completed_spend, 0, ',', '.') }} đ</strong></div>
                <div class="d-flex justify-content-between"><span>Tổng số đơn</span><strong>{{ $user->orders_count }}</strong></div>
                <small class="text-muted d-block mt-3">Tham gia ngày {{ $user->created_at?->format('d/m/Y') }}</small>
            </div></div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4">
                <h5 class="fw-bold mb-3">Lịch sử điểm</h5>
                @forelse($transactions as $transaction)
                    <div class="d-flex justify-content-between border-bottom py-3"><span>{{ $transaction->description }}<small class="d-block text-muted">{{ $transaction->created_at?->format('d/m/Y H:i') }}</small></span><strong class="{{ $transaction->points > 0 ? 'text-success' : 'text-danger' }}">{{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }}</strong></div>
                @empty
                    <p class="text-muted mb-0">Chưa có giao dịch điểm.</p>
                @endforelse
                {{ $transactions->links() }}
            </div></div>
        </div>
    </div>
</div>
@endsection
