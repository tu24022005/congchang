@extends('layouts.app')
@section('title', 'Quản lý tất cả đơn hàng')

@section('content')
<div class="container-fluid py-4 admin-orders-page">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <span class="admin-eyebrow"><i class="bi bi-shield-check me-1"></i> ALOHA BEAUTY / QUẢN LÝ</span>
            <h2 class="fw-bold text-dark mb-1"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Quản lý đơn hàng</h2>
            <p class="text-muted mb-0">Theo dõi, lọc và xử lý toàn bộ đơn hàng trong một màn hình.</p>
        </div>
        <a href="{{ route('admin.reports.export') }}" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="bi bi-download me-2"></i>Xuất dữ liệu</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><div class="order-kpi order-kpi-blue"><span>Tổng đơn hàng</span><strong>{{ $totalOrders }}</strong><small>Toàn hệ thống</small><i class="bi bi-receipt"></i></div></div>
        <div class="col-6 col-xl-3"><div class="order-kpi order-kpi-amber"><span>Chờ xử lý</span><strong>{{ $pendingOrders }}</strong><small>Cần được xác nhận</small><i class="bi bi-hourglass-split"></i></div></div>
        <div class="col-6 col-xl-3"><div class="order-kpi order-kpi-green"><span>Đã thanh toán</span><strong>{{ $paidOrders }}</strong><small>Đơn hoàn tất thanh toán</small><i class="bi bi-check2-circle"></i></div></div>
        <div class="col-6 col-xl-3"><div class="order-kpi order-kpi-violet"><span>Doanh thu đã thu</span><strong>{{ number_format($totalRevenue, 0, ',', '.') }} đ</strong><small>{{ $todayOrders }} đơn trong hôm nay</small><i class="bi bi-graph-up-arrow"></i></div></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 order-filter-card">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-6"><div class="input-group"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span><input type="search" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Mã đơn, tên khách hàng hoặc sản phẩm..."></div></div>
                <div class="col-sm-5 col-lg-3"><select name="status" class="form-select"><option value="">Tất cả trạng thái</option><option value="processing" @selected(request('status') === 'processing')>Chờ xử lý</option><option value="paid" @selected(request('status') === 'paid')>Đã thanh toán</option><option value="refund_pending" @selected(request('status') === 'refund_pending')>Chờ hoàn tiền</option><option value="refunded" @selected(request('status') === 'refunded')>Đã hoàn tiền</option><option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy</option></select></div>
                <div class="col-sm-3 col-lg-1"><button class="btn btn-primary w-100" title="Lọc"><i class="bi bi-funnel"></i></button></div>
                <div class="col-sm-4 col-lg-2"><a href="{{ route('admin.orders.index') }}" class="btn btn-light border w-100">Xóa bộ lọc</a></div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden admin-panel-card order-table-card">
        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center"><div><h5 class="fw-bold mb-1">Danh sách đơn hàng</h5><small class="text-muted">Hiển thị {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} trong tổng số {{ $orders->total() }} đơn</small></div><i class="bi bi-three-dots text-muted fs-4"></i></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-dark">
                            <tr>
                            <th class="py-3">Mã đơn</th>
                            <th class="py-3 text-start">Sản phẩm</th>
                            <th class="py-3 text-start">Tên khách hàng</th>
                            <th class="py-3">Tổng tiền</th>
                            <th class="py-3">Trạng thái</th>
                            <th class="py-3">Ngày đặt</th>
                            <th class="py-3 text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            @php $orderItems = $order->items->filter(fn ($item) => $item->product); @endphp
                            <tr>
                                <td class="fw-bold text-primary">#{{ $order->id }}</td>
                                <td class="text-start">
                                    @if($orderItems->isNotEmpty())
                                        @php $firstItem = $orderItems->first(); @endphp
                                        <div class="order-product-preview"><img src="{{ $firstItem->product->image ? asset('storage/' . $firstItem->product->image) : asset('images/placeholder.jpg') }}" alt="{{ $firstItem->product->name }}"><div><strong>{{ $firstItem->product->name }}</strong><small>{{ $firstItem->quantity }} sản phẩm{{ $orderItems->count() > 1 ? ' · +' . ($orderItems->count() - 1) . ' sản phẩm khác' : '' }}</small></div></div>
                                    @else
                                        <span class="text-muted small">Không có sản phẩm</span>
                                    @endif
                                </td>
                                <td class="text-start"><strong class="d-block">{{ $order->customer_name ?? ($order->user->name ?? 'Khách vãng lai') }}</strong><small class="text-muted">{{ $order->user->email ?? 'Không có email' }}</small></td>
                                
                                <td class="text-danger fw-bold">{{ number_format($order->total, 0, ',', '.') }} đ</td>
                                <td>
                                    <!-- ĐÃ BỔ SUNG ĐẦY ĐỦ BỘ TỪ ĐIỂN DỊCH TRẠNG THÁI -->
                                    @if(strtolower($order->status) == 'processing' || $order->status == 'Đang xử lý')
                                        <span class="badge bg-warning text-dark rounded-pill px-3">Chờ xác nhận</span>
                                    @elseif(strtolower($order->status) == 'confirmed')
                                        <span class="badge bg-info text-dark rounded-pill px-3">Đã xác nhận đơn</span>
                                    @elseif(strtolower($order->status) == 'packing')
                                        <span class="badge bg-secondary rounded-pill px-3">Đang gói hàng</span>
                                    @elseif(strtolower($order->status) == 'shipping')
                                        <span class="badge bg-primary rounded-pill px-3">Đang vận chuyển</span>
                                    @elseif(strtolower($order->status) == 'paid' || $order->status == 'Đã thanh toán')
                                        <span class="badge bg-success rounded-pill px-3">Đã thanh toán</span>
                                    @elseif(strtolower($order->status) == 'completed')
                                        <span class="badge bg-success rounded-pill px-3">Đã nhận hàng</span>
                                    @elseif($order->status === 'refund_pending')
                                        <span class="badge bg-warning text-dark rounded-pill px-3">{{ in_array($order->refund_status, ['approved', null], true) ? 'Đã duyệt hoàn tiền' : 'Chờ duyệt hoàn tiền' }}</span>
                                    @elseif($order->status === 'refunded')
                                        <span class="badge bg-success rounded-pill px-3">Đã hoàn tiền</span>
                                    @elseif(strtolower($order->status) == 'cancelled' || $order->status == 'Đã huỷ')
                                        <span class="badge bg-danger rounded-pill px-3">Đã huỷ</span>
                                    @else
                                        <span class="badge bg-dark rounded-pill px-3">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                                <td><span class="d-block">{{ $order->created_at->format('d/m/Y') }}</span><small class="text-muted">{{ $order->created_at->format('H:i') }}</small></td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        @php
                                            $nextStatuses = [
                                                'processing' => ['confirmed', 'cancelled'],
                                                'confirmed' => $order->payment_method === 'COD' ? ['packing', 'cancelled'] : ['paid', 'packing', 'cancelled'],
                                                'paid' => ['packing', 'cancelled'],
                                                'packing' => ['shipping', 'cancelled'],
                                                'shipping' => ['completed'],
                                                'completed' => [],
                                                'cancelled' => [],
                                                'refund_pending' => [],
                                            ][$order->status] ?? [];
                                        @endphp
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="order-status-form">
                                            @csrf @method('PATCH')
                                            <select name="status" class="form-select form-select-sm order-status-select" onchange="this.form.submit()" title="Đổi trạng thái">
                                                <option value="{{ $order->status }}" selected>{{ ['processing' => 'Chờ xử lý', 'confirmed' => 'Đã xác nhận', 'packing' => 'Đang đóng gói', 'shipping' => 'Đang giao hàng', 'paid' => 'Đã thanh toán', 'completed' => 'Đã nhận hàng', 'cancelled' => 'Đã hủy', 'refund_pending' => 'Chờ hoàn tiền', 'refunded' => 'Đã hoàn tiền'][$order->status] ?? ucfirst($order->status) }}</option>
                                                @foreach($nextStatuses as $nextStatus)
                                                    <option value="{{ $nextStatus }}">{{ ['confirmed' => 'Đã xác nhận', 'packing' => 'Đang đóng gói', 'shipping' => 'Đang giao hàng', 'paid' => 'Đã thanh toán', 'completed' => 'Đã nhận hàng', 'cancelled' => 'Đã hủy', 'refunded' => 'Đã hoàn tiền'][$nextStatus] }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                        @if($order->status === 'refund_pending' && $order->refund_status === 'requested')
                                            <form action="{{ route('admin.orders.refund.approve', $order->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success rounded-pill px-3 fw-bold" type="submit" onclick="return confirm('Duyệt yêu cầu hoàn tiền cho đơn #{{ $order->id }}?')"><i class="bi bi-check-circle me-1"></i>Duyệt</button></form>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#rejectRefund{{ $order->id }}">Từ chối</button>
                                            <div class="modal fade" id="rejectRefund{{ $order->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form action="{{ route('admin.orders.refund.reject', $order->id) }}" method="POST">@csrf<div class="modal-header"><h5 class="modal-title">Từ chối hoàn tiền #{{ $order->id }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label">Lý do từ chối</label><textarea name="refund_rejection_note" class="form-control" rows="3" required></textarea></div><div class="modal-footer"><button type="submit" class="btn btn-danger rounded-pill">Xác nhận từ chối</button></div></form></div></div></div>
                                        @elseif($order->status === 'refund_pending' && in_array($order->refund_status, ['approved', null], true))
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold" title="Hoàn tiền cho khách"><i class="bi bi-cash-coin me-1"></i>Hoàn tiền</a>
                                        @endif
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" title="Xem chi tiết"><i class="bi bi-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    Chưa có đơn hàng nào trên toàn hệ thống.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 px-4 py-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection