@extends('layouts.app')
@section('title', 'Đơn hàng của bạn')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset_v('css/orders.css') }}">
@endpush


<div class="container py-4 orders-page">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- HERO --}}
    <div class="orders-hero mb-4 d-flex align-items-center justify-content-between gap-3">
        <div>
            <div class="orders-breadcrumb">
                <i class="bi bi-house me-1"></i>Aloha Beauty / Đơn hàng của tôi
            </div>
            <h2><i class="bi bi-box-seam me-2"></i>Đơn hàng của bạn</h2>
            <p>Theo dõi hành trình mua sắm và lịch sử giao dịch của bạn.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-shop flex-shrink-0">
            <i class="bi bi-bag-plus me-1"></i>Mua sắm thêm
        </a>
    </div>

    {{-- STATS --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <a href="{{ route('orders.index', array_filter(['search' => $search])) }}"
               class="order-stat-card s-all {{ !request('status') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap stat-icon"></i>
                <div class="stat-num">{{ $orderStats['all'] }}</div>
                <div class="stat-label">Tất cả đơn</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('orders.index', array_filter(['status' => 'processing', 'search' => $search])) }}"
               class="order-stat-card s-proc {{ request('status') === 'processing' ? 'active' : '' }}">
                <i class="bi bi-truck stat-icon"></i>
                <div class="stat-num">{{ $orderStats['processing'] }}</div>
                <div class="stat-label">Đang xử lý</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('orders.index', array_filter(['status' => 'paid', 'search' => $search])) }}"
               class="order-stat-card s-done {{ request('status') === 'paid' ? 'active' : '' }}">
                <i class="bi bi-check2-circle stat-icon"></i>
                <div class="stat-num">{{ $orderStats['paid'] }}</div>
                <div class="stat-label">Đã hoàn tất</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('orders.index', array_filter(['status' => 'cancelled', 'search' => $search])) }}"
               class="order-stat-card s-canc {{ request('status') === 'cancelled' ? 'active' : '' }}">
                <i class="bi bi-x-circle stat-icon"></i>
                <div class="stat-num">{{ $orderStats['cancelled'] }}</div>
                <div class="stat-label">Đã hủy</div>
            </a>
        </div>
    </div>

    {{-- SPEND STRIP --}}
    @php
        $totalSpent = $orders->sum('total');
        $totalItems = $orders->sum(fn($o) => $o->items->count());
    @endphp
    @if($orders->count() > 0)
    <div class="spend-strip mb-3">
        <i class="bi bi-wallet2 text-danger fs-5"></i>
        <span class="text-muted">Tổng chi tiêu ({{ $orders->count() }} đơn hiện thấy):</span>
        <span class="spend-val">{{ number_format($totalSpent, 0, ',', '.') }} d</span>
        <span class="text-muted">•</span>
        <span class="text-muted">{{ $totalItems }} sản phẩm</span>
    </div>
    @endif

    {{-- FILTER BAR --}}
    <div class="orders-filter-bar mb-4">
        {{-- Tab lọc --}}
        <div class="filter-tabs">
            @php
                $tabs = [
                    '' => ['label'=>'Tất cả','icon'=>'bi-grid','count'=>$orderStats['all']],
                    'processing' => ['label'=>'Đang xử lý','icon'=>'bi-clock','count'=>$orderStats['processing']],
                    'paid'       => ['label'=>'Hoàn tất',   'icon'=>'bi-check-circle','count'=>$orderStats['paid']],
                    'cancelled'  => ['label'=>'Đã hủy',     'icon'=>'bi-x-circle','count'=>$orderStats['cancelled']],
                ];
            @endphp
            @foreach($tabs as $ts => $tab)
                <a href="{{ route('orders.index', array_filter(['status'=>$ts?:null,'search'=>$search,'sort'=>request('sort')])) }}"
                   class="filter-tab {{ request('status','') === $ts ? 'active' : '' }}">
                    <i class="bi {{ $tab['icon'] }} me-1"></i>{{ $tab['label'] }}
                    <span class="bt">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Sort --}}
        <form action="{{ route('orders.index') }}" method="GET" id="sort-form" class="d-flex align-items-center ms-auto">
            @if($search)              <input type="hidden" name="search" value="{{ $search }}"> @endif
            @if(request('status'))   <input type="hidden" name="status" value="{{ request('status') }}"> @endif
            <select name="sort" class="order-sort-select" onchange="document.getElementById('sort-form').submit()">
                <option value="newest"     {{ request('sort','newest') === 'newest'     ? 'selected' : '' }}>Mới nhất</option>
                <option value="oldest"     {{ request('sort') === 'oldest'              ? 'selected' : '' }}>Cũ nhất</option>
                <option value="total_desc" {{ request('sort') === 'total_desc'          ? 'selected' : '' }}>Tiền: cao → thấp</option>
                <option value="total_asc"  {{ request('sort') === 'total_asc'           ? 'selected' : '' }}>Tiền: thấp → cao</option>
            </select>
        </form>

        {{-- Search --}}
        <form action="{{ route('orders.index') }}" method="GET" class="order-search-wrap">
            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
            @if(request('sort'))   <input type="hidden" name="sort"   value="{{ request('sort') }}">   @endif
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="search" name="search" value="{{ $search }}" class="form-control border-start-0 border-end-0" placeholder="Mã đơn, sản phẩm, người nhận...">
                @if($search)
                    <a href="{{ route('orders.index', array_filter(['status'=>request('status'),'sort'=>request('sort')])) }}"
                       class="btn btn-outline-secondary border-start-0" title="Xóa"><i class="bi bi-x-lg"></i></a>
                @endif
                <button class="btn btn-primary px-3 order-search-submit" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>

    {{-- DANH SÁCH ĐƠN --}}
    @php $totalPersonalOrders = \App\Models\Order::where('user_id', Auth::id())->count(); @endphp

    @if($orders->count() > 0)
        <div class="d-flex flex-column gap-3">
        @foreach($orders as $order)
        @php
            $pNum = $totalPersonalOrders - $loop->index;
            $oItems = $order->items->filter(fn($i) => $i->product);
            $first  = $oItems->first();
            $extra  = max(0, $oItems->count() - 3);

            $stMap = [
                'processing'     => ['Chờ xác nhận',    'bg-warning-subtle text-warning',    'bi-hourglass-split'],
                'confirmed'      => ['Đã xác nhận',      'bg-info-subtle text-info',           'bi-patch-check'],
                'packing'        => ['Đang gói hàng',    'bg-secondary-subtle text-secondary', 'bi-box'],
                'shipping'       => ['Đang vận chuyển',  'bg-primary-subtle text-primary',     'bi-truck'],
                'paid'           => ['Đã thanh toán',    'bg-success-subtle text-success',     'bi-check-circle-fill'],
                'completed'      => ['Đã nhận hàng',     'bg-success-subtle text-success',     'bi-bag-check-fill'],
                'refund_pending' => ['Chờ hoàn tiền',    'bg-warning-subtle text-warning',     'bi-cash-coin'],
                'refunded'       => ['Đã hoàn tiền',     'bg-success-subtle text-success',     'bi-cash-stack'],
                'cancelled'      => ['Đã hủy',           'bg-danger-subtle text-danger',       'bi-x-circle-fill'],
            ];
            $st = $stMap[strtolower($order->status)] ?? [ucfirst($order->status), 'bg-dark-subtle text-dark', 'bi-circle'];
        @endphp
        <div class="order-card">

            {{-- HEAD --}}
            <div class="order-card-head">
                <div>
                    <div class="order-num"><i class="bi bi-receipt me-1"></i>Đơn hàng thứ {{ $pNum }}</div>
                    <div class="order-id">Mã tra cứu: #{{ $order->id }}</div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="order-date"><i class="bi bi-calendar3 me-1"></i>{{ $order->created_at->format('d/m/Y · H:i') }}</div>
                    <span class="{{ $st[1] }} fw-bold order-status-badge">
                        <i class="bi {{ $st[2] }}"></i> {{ $st[0] }}
                    </span>
                </div>
            </div>

            {{-- BODY --}}
            <div class="order-card-body">
                <div class="order-products-preview">
                    @foreach($oItems->take(3) as $item)
                        <img src="{{ $item->product && $item->product->image ? asset('storage/'.$item->product->image) : asset('images/placeholder.svg') }}"
                             alt="{{ $item->product->name ?? '' }}" class="product-thumb"
                             onerror="this.src='{{ asset('images/placeholder.svg') }}'">
                    @endforeach
                    @if($extra > 0)
                        <div class="more-thumb">+{{ $extra }}</div>
                    @endif
                    @if($first)
                        <div class="prod-info">
                            <div class="prod-name">{{ $first->product->name ?? 'Sản phẩm' }}</div>
                            <div class="prod-sub">
                                {{ $oItems->count() }} sản phẩm
                                @if($first->variation)
                                    • {{ collect([$first->variation->sku,$first->variation->color,$first->variation->size_value?rtrim(rtrim($first->variation->size_value,'0'),'.').$first->variation->size_unit:null])->filter()->implode(' • ') }}
                                @endif
                            </div>
                        </div>
                    @else
                        <span class="text-muted small ms-2">Sản phẩm không còn tồn tại</span>
                    @endif
                </div>

                <div class="order-card-meta">
                    @if($order->payment_method == 'COD')
                        <span class="pay-badge bg-secondary-subtle text-secondary"><i class="bi bi-cash me-1"></i>COD</span>
                    @else
                        <span class="pay-badge bg-primary-subtle text-primary"><i class="bi bi-qr-code-scan me-1"></i>PayOS</span>
                    @endif
                    <div class="order-price">{{ number_format($order->total, 0, ',', '.') }} đ</div>
                </div>
            </div>

            {{-- FOOT --}}
            <div class="order-card-foot">
                <div class="order-address">
                    <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($order->shipping_address ?? $order->customer_name ?? 'Không có địa chỉ', 60) }}
                </div>
                <div class="order-actions">
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye"></i> Chi tiết
                    </a>
                    @if(in_array($order->status, ['completed','paid'], true))
                        <form action="{{ route('orders.reorder', $order) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-arrow-repeat"></i> Mua lại
                            </button>
                        </form>
                    @endif
                    @if(in_array($order->status, ['processing','confirmed','paid'], true))
                        @if($order->payment_method !== 'COD' && $order->status === 'paid')
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                data-bs-toggle="modal" data-bs-target="#cancelOnlineOrderModal"
                                data-order-url="{{ route('orders.refund.request', $order) }}">
                                <i class="bi bi-cash-coin"></i> Hủy & hoàn tiền
                            </button>
                        @else
                            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-x-circle"></i> Hủy đơn
                                </button>
                            </form>
                        @endif
                    @endif
                    @if($order->status === 'shipping')
                        <form action="{{ route('orders.confirm_received', $order) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-bag-check"></i> Đã nhận hàng
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        </div>

    @else
        <div class="empty-orders">
            @if($search !== '')
                <div class="empty-icon"><i class="bi bi-search"></i></div>
                <h4 class="mt-3 fw-bold text-muted">Không tìm thấy đơn hàng</h4>
                <p class="text-muted mb-3">Thử từ khóa khác hoặc xóa bộ lọc để xem tất cả đơn.</p>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-x-circle me-1"></i>Xóa bộ lọc
                </a>
            @else
                <div class="empty-icon"><i class="bi bi-bag-x"></i></div>
                <h4 class="mt-3 fw-bold text-muted">Bạn chưa có đơn hàng nào</h4>
                <p class="text-muted mb-3">Khám phá hàng ngàn sản phẩm làm đẹp tuyệt vời của chúng tôi!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-5 shadow-sm">
                    <i class="bi bi-bag-heart me-2"></i>Mua sắm ngay
                </a>
            @endif
        </div>
    @endif
</div>

{{-- MODAL HOÀN TIỀN --}}
<div class="modal fade" id="cancelOnlineOrderModal" tabindex="-1" aria-labelledby="cancelOnlineOrderTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="cancelOnlineOrderForm" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="cancelOnlineOrderTitle">
                        <i class="bi bi-cash-coin text-danger me-2"></i>Yêu cầu hoàn tiền
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="small text-muted">Đơn online đã thanh toán sẽ được hủy và PayOS tự động chuyển tiền về tài khoản này.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ngân hàng</label>
                        <select name="refund_bank_name" id="refundModalBankSelect" class="form-select" required>
                            <option value="">-- Chọn ngân hàng --</option>
                            <option data-bin="970436" value="Vietcombank">Vietcombank</option>
                            <option data-bin="970415" value="VietinBank">VietinBank</option>
                            <option data-bin="970418" value="BIDV">BIDV</option>
                            <option data-bin="970405" value="Agribank">Agribank</option>
                            <option data-bin="970422" value="MB Bank">MB Bank</option>
                            <option data-bin="970407" value="Techcombank">Techcombank</option>
                            <option data-bin="970416" value="ACB">ACB</option>
                            <option data-bin="970432" value="VPBank">VPBank</option>
                            <option data-bin="970403" value="Sacombank">Sacombank</option>
                            <option data-bin="970423" value="TPBank">TPBank</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mã BIN ngân hàng</label>
                        <input type="text" name="refund_bank_bin" id="refundModalBankBin" class="form-control bg-light" readonly required>
                        <small class="text-muted">Mã BIN sẽ tự hiện khi chọn ngân hàng.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số tài khoản</label>
                        <input type="text" name="refund_account_number" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Tên chủ tài khoản</label>
                        <input type="text" name="refund_account_holder" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border rounded-pill" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="bi bi-send me-1"></i>Gửi yêu cầu hoàn tiền
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset_v('js/views/orders-index-blade-php.js') }}" defer></script>
@endsection