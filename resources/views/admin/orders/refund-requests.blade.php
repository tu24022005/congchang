@extends('layouts.app')
@section('title', 'Yêu cầu hoàn tiền')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="admin-eyebrow"><i class="bi bi-shield-check me-1"></i> ALOHA BEAUTY / TÀI CHÍNH</span>
            <h2 class="fw-bold mb-1"><i class="bi bi-cash-coin text-warning me-2"></i>Yêu cầu hoàn tiền</h2>
            <p class="text-muted mb-0">Kiểm tra và duyệt yêu cầu hoàn tiền của khách hàng trước khi chuyển khoản.</p>
        </div>
        <a href="{{ route('admin.orders.index', ['status' => 'refund_pending']) }}" class="btn btn-light border rounded-pill">Xem trong đơn hàng</a>
    </div>

    @if(session('success'))<div class="alert alert-success rounded-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger rounded-4">{{ session('error') }}</div>@endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark"><tr><th>Mã đơn</th><th>Khách hàng</th><th>Số tiền</th><th>Tài khoản nhận hoàn</th><th>Ngày yêu cầu</th><th class="text-end">Xử lý</th></tr></thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="fw-bold text-primary">#{{ $order->id }}</td>
                            <td><strong>{{ $order->customer_name ?: $order->user?->name }}</strong><small class="d-block text-muted">{{ $order->user?->email }}</small></td>
                            <td class="fw-bold text-danger">{{ number_format($order->total, 0, ',', '.') }} đ</td>
                            <td><span class="d-block">{{ $order->refund_bank_name }}</span><small class="text-muted">{{ $order->refund_account_number }} - {{ $order->refund_account_holder }}</small></td>
                            <td>{{ $order->updated_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <form action="{{ route('admin.orders.refund.approve', $order) }}" method="POST">@csrf<button class="btn btn-success btn-sm rounded-pill" onclick="return confirm('Duyệt yêu cầu hoàn tiền đơn #{{ $order->id }}?')"><i class="bi bi-check-circle me-1"></i>Duyệt</button></form>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#rejectRefund{{ $order->id }}">Từ chối</button>
                                </div>
                                <div class="modal fade text-start" id="rejectRefund{{ $order->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form action="{{ route('admin.orders.refund.reject', $order) }}" method="POST">@csrf<div class="modal-header"><h5 class="modal-title">Từ chối đơn #{{ $order->id }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label">Lý do từ chối</label><textarea name="refund_rejection_note" class="form-control" rows="3" required></textarea></div><div class="modal-footer"><button class="btn btn-danger rounded-pill">Xác nhận từ chối</button></div></form></div></div></div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-check2-circle fs-1 d-block mb-2 text-success"></i>Hiện không có yêu cầu hoàn tiền chờ duyệt.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0">{{ $orders->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
