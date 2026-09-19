@extends('layouts.app')
@section('title', 'Đơn hàng của bạn')

@section('content')
<style>
    .customer-orders-page { max-width: 1120px; }
    .customer-orders-page .customer-orders-heading { margin-bottom: 1.25rem !important; }
    .customer-orders-page .customer-orders-heading .btn { white-space: nowrap; padding: .55rem .8rem; font-size: .82rem; }
    .customer-orders-page .customer-orders-heading p { font-size: .86rem; }
    .customer-orders-page .customer-order-stat { min-height: 82px; padding: .85rem 1rem; }
    .customer-orders-page .customer-order-stat strong { font-size: 1.55rem; }
    .customer-orders-page .customer-order-stat span { font-size: .72rem; }
    .customer-orders-page .customer-order-toolbar { padding: 1rem 1.15rem !important; }
    .customer-orders-page .customer-order-toolbar h5 { font-size: 1rem; }
    .customer-orders-page .order-search-form { flex: 0 1 480px; max-width: 480px; }
    .customer-orders-page .order-product-preview { min-width: 175px; gap: .55rem; }
    .customer-orders-page .order-product-preview img { width: 40px; height: 40px; }
    .customer-orders-page .order-product-preview strong { font-size: .76rem; }
    .customer-orders-page .order-product-preview small { font-size: .68rem; }
    .customer-orders-page .table th { padding: .65rem .5rem !important; font-size: .67rem; white-space: nowrap; }
    .customer-orders-page .table td { padding: .65rem .5rem; font-size: .78rem; }
    .customer-orders-page .order-actions { display: inline-flex; align-items: center; justify-content: center; gap: .25rem; white-space: nowrap; }
    .customer-orders-page .order-actions .btn { display: inline-flex; align-items: center; justify-content: center; width: 31px; height: 31px; min-width: 31px; padding: 0; font-size: .78rem; }
    .customer-orders-page .order-actions .btn span { display: none; }
    .customer-orders-page .order-payment-badge { display: inline-block; max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: .3rem .5rem !important; font-size: .65rem; }
    .customer-orders-page .order-status-badge { display: inline-block; max-width: 108px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: .3rem .5rem !important; font-size: .65rem; }
    @media (max-width: 991.98px) {
        .customer-orders-page .order-payment-badge { max-width: 110px; }
        .customer-orders-page .order-status-badge { max-width: 95px; }
        .customer-orders-page .order-product-preview { min-width: 150px; }
    }
    @media (max-width: 575.98px) {
        .customer-orders-page .customer-orders-heading .btn { width: auto; }
        .customer-orders-page .customer-orders-heading .btn span { display: none; }
        .customer-orders-page .order-payment-badge { max-width: 88px; }
        .customer-orders-page .order-status-badge { max-width: 82px; }
        .customer-orders-page .order-date-cell { white-space: nowrap; }
    }
</style>
<div class="container py-4 customer-orders-page">
    <div class="customer-orders-heading mb-4"><div><span class="cart-eyebrow">ALOHA BEAUTY / ĐƠN HÀNG CỦA TÔI</span><h2 class="fw-bold storefront-title mb-1"><i class="bi bi-box-seam me-2"></i>Đơn hàng của bạn</h2><p class="text-muted mb-0">Theo dõi hành trình mua sắm và lịch sử giao dịch của bạn.</p></div><a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill" title="Mua sắm thêm"><i class="bi bi-bag-plus me-1"></i><span>Mua sắm thêm</span></a></div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><a href="{{ route('orders.index', array_filter(['search' => $search])) }}" class="customer-order-stat {{ !request('status') ? 'active' : '' }}"><i class="bi bi-grid"></i><strong>{{ $orderStats['all'] }}</strong><span>Tất cả đơn</span></a></div>
        <div class="col-6 col-md-3"><a href="{{ route('orders.index', array_filter(['status' => 'processing', 'search' => $search])) }}" class="customer-order-stat stat-amber {{ request('status') === 'processing' ? 'active' : '' }}"><i class="bi bi-truck"></i><strong>{{ $orderStats['processing'] }}</strong><span>Đang xử lý</span></a></div>
        <div class="col-6 col-md-3"><a href="{{ route('orders.index', array_filter(['status' => 'paid', 'search' => $search])) }}" class="customer-order-stat stat-green {{ request('status') === 'paid' ? 'active' : '' }}"><i class="bi bi-check2-circle"></i><strong>{{ $orderStats['paid'] }}</strong><span>Đã hoàn tất</span></a></div>
        <div class="col-6 col-md-3"><a href="{{ route('orders.index', array_filter(['status' => 'cancelled', 'search' => $search])) }}" class="customer-order-stat stat-red {{ request('status') === 'cancelled' ? 'active' : '' }}"><i class="bi bi-x-circle"></i><strong>{{ $orderStats['cancelled'] }}</strong><span>Đã hủy</span></a></div>
    </div>

    @if($orders->count() > 0)
        <!-- Đếm trước tổng số đơn của riêng user này để làm thuật toán STT cá nhân -->
        @php
            $totalPersonalOrders = \App\Models\Order::where('user_id', Auth::id())->count();
        @endphp

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden order-panel-card customer-order-table-card">
            <div class="card-body p-0">
            <div class="customer-order-toolbar"><div><h5 class="fw-bold mb-1">Lịch sử đơn hàng</h5><small class="text-muted">{{ $orders->count() }} đơn đang hiển thị{{ $search !== '' ? ' cho từ khóa "' . $search . '"' : '' }}</small></div><form action="{{ route('orders.index') }}" method="GET" class="order-search-form"><div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Mã đơn, tên sản phẩm, người nhận..."><button class="btn btn-primary" type="submit">Tìm kiếm</button>@if($search !== '')<a href="{{ route('orders.index') }}" class="btn btn-light border" title="Xóa tìm kiếm"><i class="bi bi-x-lg"></i></a>@endif</div></form></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-3 text-start ps-4">Mã đơn hàng</th>
                                <th class="py-3 text-start">Sản phẩm</th>
                                <th class="py-3">Tổng tiền</th>
                                <th class="py-3">Phương thức thanh toán</th>
                                <th class="py-3">Trạng thái</th>
                                <th class="py-3">Ngày tạo</th>
                                <th class="py-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                @php
                                    // Thuật toán: Vì đơn mới nhất xếp lên đầu, ta sẽ đếm lùi
                                    // Ví dụ có 3 đơn: index 0 -> Đơn #3, index 1 -> Đơn #2...
                                    $personalNumber = $totalPersonalOrders - $loop->index;
                                    $orderItems = $order->items->filter(fn ($item) => $item->product);
                                @endphp
                                <tr>
                                    <td class="text-start ps-4">
                                        <div class="fw-bold text-primary order-total">
                                            Đơn hàng thứ {{ $personalNumber }}
                                        </div>
                                        <div class="text-muted small">
                                            Mã tra cứu: #{{ $order->id }}
                                        </div>
                                    </td>
                                    <td class="text-start">
                                        @if($orderItems->isNotEmpty())
                                            @php $firstItem = $orderItems->first(); @endphp
                                            <div class="order-product-preview"><img src="{{ $firstItem->product->image ? asset('storage/' . $firstItem->product->image) : asset('images/placeholder.jpg') }}" alt="{{ $firstItem->product->name }}"><div><strong>{{ $firstItem->product->name }}</strong><small>{{ $firstItem->quantity }} sản phẩm{{ $orderItems->count() > 1 ? ' · +' . ($orderItems->count() - 1) . ' sản phẩm khác' : '' }}</small></div></div>
                                        @else
                                            <span class="text-muted small">Sản phẩm không còn tồn tại</span>
                                        @endif
                                    </td>

                                    <td class="text-danger fw-bold">{{ number_format($order->total, 0, ',', '.') }} đ</td>
                                    <td>
                                        @if($order->payment_method == 'COD')
                                            <span class="badge bg-secondary rounded-pill px-3 order-payment-badge" title="Thanh toán khi nhận hàng (COD)">COD - Nhận hàng trả tiền</span>
                                        @else
                                            <span class="badge bg-primary rounded-pill px-3 order-payment-badge" title="Chuyển khoản ngân hàng qua PayOS">Chuyển khoản PayOS</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(strtolower($order->status) == 'processing' || $order->status == 'Đang xử lý')
                                            <span class="badge bg-warning text-dark rounded-pill px-3 order-status-badge">Chờ xác nhận</span>
                                        @elseif(strtolower($order->status) == 'confirmed')
                                            <span class="badge bg-info text-dark rounded-pill px-3 order-status-badge">Đã xác nhận</span>
                                        @elseif(strtolower($order->status) == 'packing')
                                            <span class="badge bg-secondary rounded-pill px-3 order-status-badge">Đang gói hàng</span>
                                        @elseif(strtolower($order->status) == 'shipping')
                                            <span class="badge bg-primary rounded-pill px-3 order-status-badge">Đang vận chuyển</span>
                                        @elseif(strtolower($order->status) == 'paid' || $order->status == 'Đã thanh toán')
                                            <span class="badge bg-success rounded-pill px-3 order-status-badge">Đã thanh toán</span>
                                        @elseif(strtolower($order->status) == 'completed')
                                            <span class="badge bg-success rounded-pill px-3 order-status-badge">Đã nhận hàng</span>
                                        @elseif($order->status === 'refund_pending')
                                            <span class="badge bg-warning text-dark rounded-pill px-3 order-status-badge">Chờ hoàn tiền</span>
                                        @elseif($order->status === 'refunded')
                                            <span class="badge bg-success rounded-pill px-3 order-status-badge">Đã hoàn tiền</span>
                                        @elseif(strtolower($order->status) == 'cancelled' || $order->status == 'Đã huỷ')
                                            <span class="badge bg-danger rounded-pill px-3 order-status-badge">Đã huỷ</span>
                                        @else
                                            <span class="badge bg-dark rounded-pill px-3 order-status-badge">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="order-date-cell">
                                        <span class="d-block">{{ $order->created_at->format('d/m/Y') }}</span><small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="order-actions">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info text-white rounded-pill shadow-sm fw-bold" title="Xem chi tiết đơn hàng">
                                            <i class="bi bi-eye"></i><span>Chi tiết</span>
                                        </a>
                                        @if(in_array($order->status, ['processing', 'confirmed', 'paid'], true))
                                            @if($order->payment_method !== 'COD' && $order->status === 'paid')
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-bold" title="Hủy và yêu cầu hoàn tiền" data-bs-toggle="modal" data-bs-target="#cancelOnlineOrderModal" data-order-url="{{ route('orders.cancel', $order) }}">
                                                    <i class="bi bi-x-circle"></i><span>Hủy</span>
                                                </button>
                                            @else
                                            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill fw-bold" title="Hủy đơn hàng">
                                                    <i class="bi bi-x-circle"></i><span>Hủy</span>
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5 bg-light rounded-4 shadow-sm mt-4 border">
            <i class="bi bi-cart-x text-muted empty-cart-icon"></i>
            <h4 class="mt-3 text-muted">{{ $search !== '' ? 'Không tìm thấy đơn hàng phù hợp' : 'Bạn chưa có đơn hàng nào' }}</h4>
            @if($search !== '')
                <p class="text-muted">Thử tìm bằng mã đơn, tên sản phẩm hoặc tên người nhận khác.</p>
                <form action="{{ route('orders.index') }}" method="GET" class="order-search-form mx-auto mt-3"><div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Mã đơn, tên sản phẩm, người nhận..."><button class="btn btn-primary" type="submit">Tìm lại</button><a href="{{ route('orders.index') }}" class="btn btn-light border">Xóa</a></div></form>
            @else
                <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-4 mt-3 shadow-sm">Khám phá sản phẩm ngay</a>
            @endif
        </div>
    @endif
</div>

<div class="modal fade" id="cancelOnlineOrderModal" tabindex="-1" aria-labelledby="cancelOnlineOrderTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="cancelOnlineOrderForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="cancelOnlineOrderTitle"><i class="bi bi-arrow-counterclockwise text-danger me-2"></i>Hủy đơn và yêu cầu hoàn tiền</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">Đơn online đã thanh toán sẽ chuyển sang chờ hoàn tiền. Vui lòng nhập thông tin tài khoản nhận tiền.</p>
                    <div class="mb-3"><label class="form-label">Tên ngân hàng</label><input type="text" name="refund_bank_name" class="form-control" placeholder="Ví dụ: Vietcombank" required></div>
                    <div class="mb-3"><label class="form-label">Số tài khoản</label><input type="text" name="refund_account_number" class="form-control" required></div>
                    <div class="mb-0"><label class="form-label">Tên chủ tài khoản</label><input type="text" name="refund_account_holder" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light border rounded-pill" data-bs-dismiss="modal">Đóng</button><button type="submit" class="btn btn-danger rounded-pill"><i class="bi bi-send me-1"></i>Gửi yêu cầu hoàn tiền</button></div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('cancelOnlineOrderModal');
        const form = document.getElementById('cancelOnlineOrderForm');
        if (!modal || !form) return;
        modal.addEventListener('show.bs.modal', function (event) {
            form.action = event.relatedTarget.dataset.orderUrl;
        });
        if (modal.parentElement !== document.body) document.body.appendChild(modal);
    });
</script>
@endsection