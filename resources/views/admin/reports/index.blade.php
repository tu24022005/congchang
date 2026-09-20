@extends('layouts.app')
@section('title', 'Báo cáo & Thống kê')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
<div class="container py-4">
    <div class="report-page-heading d-flex justify-content-between align-items-center mb-4">
        <div>
        <span class="report-kicker">ALOHA BEAUTY / TRUNG TÂM PHÂN TÍCH</span>
        <h2 class="fw-bold text-primary mb-1">
            <i class="bi bi-bar-chart-fill me-2"></i>Báo cáo & Thống kê Tổng quan
        </h2>
        <p class="text-muted mb-0">Theo dõi doanh thu, đơn hàng và hiệu suất sản phẩm trong một màn hình.</p>
        </div>
        <a href="{{ route('admin.reports.export', array_filter($filters)) }}" class="btn btn-primary rounded-pill shadow-sm">
            <i class="bi bi-download me-1"></i> Tải CSV
        </a>
    </div>

    <div class="report-filter-panel mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div><h5 class="fw-bold mb-1"><i class="bi bi-sliders2-vertical me-2"></i>Bộ lọc báo cáo</h5><small class="text-muted">Chọn phạm vi để các số liệu bên dưới tự cập nhật.</small></div>
            @if(array_filter($filters))<span class="report-filter-active"><i class="bi bi-funnel-fill me-1"></i>Đang áp dụng bộ lọc</span>@endif
        </div>
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3"><label for="report-from" class="form-label">Từ ngày</label><input type="date" id="report-from" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control"></div>
            <div class="col-md-3"><label for="report-to" class="form-label">Đến ngày</label><input type="date" id="report-to" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control"></div>
            <div class="col-md-3"><label for="report-status" class="form-label">Trạng thái đơn</label><select id="report-status" name="status" class="form-select"><option value="">Tất cả trạng thái</option><option value="processing" @selected(($filters['status'] ?? '') === 'processing')>Chờ xác nhận</option><option value="confirmed" @selected(($filters['status'] ?? '') === 'confirmed')>Đã xác nhận</option><option value="packing" @selected(($filters['status'] ?? '') === 'packing')>Đang đóng gói</option><option value="shipping" @selected(($filters['status'] ?? '') === 'shipping')>Đang giao hàng</option><option value="paid" @selected(($filters['status'] ?? '') === 'paid')>Đã thanh toán</option><option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Đã nhận hàng</option><option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Đã hủy</option></select></div>
            <div class="col-md-3 d-flex gap-2"><button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i>Áp dụng</button><a href="{{ route('admin.reports.index') }}" class="btn btn-light border" title="Xóa bộ lọc"><i class="bi bi-arrow-counterclockwise"></i></a></div>
        </form>
    </div>

    <!-- Hàng 1: Các thẻ chỉ số (Cards) -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <a href="#report-kpi-detail" class="card border-0 shadow-sm rounded-4 h-100 bg-gradient report-card report-revenue-card report-kpi-action" data-report-kind="revenue" data-report-accent="success">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-white-50 fw-bold text-uppercase report-label">Tổng doanh thu (Đã thu)</p>
                        <h4 class="fw-bold mb-0">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h4>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center report-icon">
                        <i class="bi bi-cash-coin fs-3"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="#report-kpi-detail" class="card border-0 shadow-sm rounded-4 h-100 bg-gradient report-card report-pending-card report-kpi-action" data-report-kind="pending" data-report-accent="pending">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-white-50 fw-bold text-uppercase report-label">Đơn cần xử lý</p>
                        <h4 class="fw-bold mb-0">{{ $pendingOrders }} đơn</h4>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center report-icon">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="#report-kpi-detail" class="card border-0 shadow-sm rounded-4 h-100 bg-gradient report-card report-orders-card report-kpi-action" data-report-kind="orders" data-report-accent="orders">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-white-50 fw-bold text-uppercase report-label">Tổng đơn hàng</p>
                        <h4 class="fw-bold mb-0">{{ $totalOrders }} đơn</h4>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center report-icon">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="#report-kpi-detail" class="card border-0 shadow-sm rounded-4 h-100 bg-gradient report-card report-customers-card report-kpi-action" data-report-kind="customers" data-report-accent="customers">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-white-50 fw-bold text-uppercase report-label">Tổng khách hàng</p>
                        <h4 class="fw-bold mb-0">{{ $totalCustomers }} người</h4>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center report-icon">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <section id="report-kpi-detail" class="report-kpi-detail mb-5" aria-live="polite">
        <div class="report-detail-view is-visible" data-report-view="revenue">
            <span class="report-section-kicker">CHI TIẾT DOANH THU</span><h5 class="fw-bold mb-3">Tiền bán thành công theo phương thức thanh toán</h5>
            <div class="report-detail-grid"><div><i class="bi bi-cash-coin"></i><span>Tiền mặt / COD</span><strong>{{ number_format($successfulCodRevenue, 0, ',', '.') }} đ</strong></div><div><i class="bi bi-credit-card-2-front"></i><span>Chuyển khoản / PayOS</span><strong>{{ number_format($successfulPayosRevenue, 0, ',', '.') }} đ</strong></div><div><i class="bi bi-check2-circle"></i><span>Tổng bán thành công</span><strong>{{ number_format($successfulRevenue, 0, ',', '.') }} đ</strong></div></div>
        </div>
        <div class="report-detail-view" data-report-view="pending">
            <span class="report-section-kicker">TIẾN ĐỘ XỬ LÝ</span><h5 class="fw-bold mb-3">Các đơn đang hoàn thiện và đóng gói</h5>
            <div class="report-detail-grid"><div><i class="bi bi-hourglass-split"></i><span>Chờ xác nhận</span><strong>{{ $statusCounts->get('processing', 0) }} đơn</strong></div><div><i class="bi bi-check2"></i><span>Đã xác nhận</span><strong>{{ $statusCounts->get('confirmed', 0) }} đơn</strong></div><div><i class="bi bi-box-seam"></i><span>Đang đóng gói</span><strong>{{ $statusCounts->get('packing', 0) }} đơn</strong></div><div><i class="bi bi-truck"></i><span>Đang giao hàng</span><strong>{{ $statusCounts->get('shipping', 0) }} đơn</strong></div></div>
        </div>
        <div class="report-detail-view" data-report-view="orders">
            <span class="report-section-kicker">TỔNG QUAN ĐƠN HÀNG</span><h5 class="fw-bold mb-3">Đơn khách đặt, đã bán thành công và bị hủy</h5>
            <div class="report-detail-grid"><div><i class="bi bi-receipt"></i><span>Tổng đơn khách đặt</span><strong>{{ $totalOrders }} đơn</strong></div><div><i class="bi bi-check-circle"></i><span>Bán thành công</span><strong>{{ $successfulOrders }} đơn</strong></div><div><i class="bi bi-arrow-repeat"></i><span>Đang xử lý</span><strong>{{ $activeOrders }} đơn</strong></div><div><i class="bi bi-x-circle"></i><span>Đã hủy</span><strong>{{ $cancelledOrders }} đơn</strong></div></div>
        </div>
        <div class="report-detail-view" data-report-view="customers">
            <span class="report-section-kicker">TÀI KHOẢN NGƯỜI DÙNG</span><h5 class="fw-bold mb-3">Danh sách tài khoản khách hàng</h5>
            <div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Khách hàng</th><th>Email</th><th>Số đơn</th><th>Ngày tham gia</th></tr></thead><tbody>@forelse($customerAccounts as $customer)<tr><td class="fw-semibold">{{ $customer->name }}</td><td class="text-muted">{{ $customer->email }}</td><td><span class="badge bg-info-subtle text-info-emphasis rounded-pill">{{ $customer->orders_count }}</span></td><td class="text-muted">{{ $customer->created_at?->format('d/m/Y') }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">Chưa có tài khoản khách hàng.</td></tr>@endforelse</tbody></table></div>
        </div>
    </section>

    @php
        $statusSummary = [
            ['key' => 'processing', 'label' => 'Chờ xử lý', 'icon' => 'bi-hourglass-split', 'class' => 'is-amber'],
            ['key' => 'shipping', 'label' => 'Đang giao', 'icon' => 'bi-truck', 'class' => 'is-blue'],
            ['key' => 'paid', 'label' => 'Đã thanh toán', 'icon' => 'bi-credit-card', 'class' => 'is-green'],
            ['key' => 'completed', 'label' => 'Đã nhận hàng', 'icon' => 'bi-check2-circle', 'class' => 'is-teal'],
            ['key' => 'cancelled', 'label' => 'Đã hủy', 'icon' => 'bi-x-circle', 'class' => 'is-red'],
        ];
    @endphp
    <div id="report-status-overview" class="report-status-strip mb-5">
        <div><span class="report-section-kicker">TÌNH HÌNH ĐƠN HÀNG</span><h5 class="fw-bold mb-0">Phân bổ theo trạng thái</h5></div>
        <div class="report-status-items">
            @foreach($statusSummary as $status)
                <div class="report-status-item {{ $status['class'] }}"><i class="bi {{ $status['icon'] }}"></i><div><strong>{{ $statusCounts->get($status['key'], 0) }}</strong><span>{{ $status['label'] }}</span></div></div>
            @endforeach
        </div>
    </div>

    <section id="report-money-panel" class="report-money-panel mb-5" aria-labelledby="report-money-title">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
            <div><span class="report-section-kicker">DÒNG TIỀN ĐƠN HÀNG</span><h5 id="report-money-title" class="fw-bold mb-1">Tổng quan giá trị đơn</h5><small class="text-muted">So sánh tiền đã bán thành công, tiền đơn hủy và tổng giá trị đơn trong phạm vi đã chọn.</small></div>
            <i class="bi bi-bar-chart-line-fill report-money-heading-icon"></i>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="report-money-card is-success"><span><i class="bi bi-check2-circle me-1"></i>Bán thành công</span><strong>{{ number_format($successfulRevenue, 0, ',', '.') }} đ</strong><small>Đã thanh toán / đã nhận hàng</small></div></div>
            <div class="col-md-4"><div class="report-money-card is-cancelled"><span><i class="bi bi-x-circle me-1"></i>Đơn bị hủy</span><strong>{{ number_format($cancelledRevenue, 0, ',', '.') }} đ</strong><small>Giá trị không ghi nhận doanh thu</small></div></div>
            <div class="col-md-4"><div class="report-money-card is-total"><span><i class="bi bi-wallet2 me-1"></i>Tổng giá trị đơn</span><strong>{{ number_format($grossOrderValue, 0, ',', '.') }} đ</strong><small>Tất cả trạng thái trong bộ lọc</small></div></div>
        </div>
        <div id="reportRevenueChart" class="report-revenue-chart" aria-label="Biểu đồ giá trị đơn hàng"></div>
    </section>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Doanh thu theo từng tháng</h5>
                    <small class="text-muted">Chỉ tính các đơn đã thanh toán hoặc hoàn tất trong phạm vi bộ lọc.</small>
                </div>
                <div class="card-body"><div id="monthlyRevenueChart" style="min-height: 300px"></div></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-1"><i class="bi bi-arrow-repeat me-2 text-danger"></i>Tỷ lệ hủy / chuyển hoàn</h5>
                    <small class="text-muted">Tính trên tổng số đơn trong phạm vi bộ lọc.</small>
                </div>
                <div class="card-body"><div id="orderOutcomeChart" style="min-height: 260px"></div>
                    <div class="small text-muted mt-2">Hủy: <strong>{{ $cancelledCount }}</strong> đơn ({{ $cancelledRate }}%) · Chuyển/hoàn: <strong>{{ $refundCount }}</strong> đơn ({{ $refundRate }}%) · Tổng: <strong>{{ $cancelledOrRefundedCount }}</strong> đơn ({{ $cancelledOrRefundedRate }}%)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <div><h5 class="fw-bold mb-1"><i class="bi bi-trophy me-2 text-warning"></i>Khách hàng mua nhiều nhất</h5><small class="text-muted">Xếp theo tổng giá trị đơn đã thanh toán hoặc hoàn tất.</small></div>
            <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">{{ $topCustomers->count() }} khách hàng</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-4">#</th><th>Khách hàng</th><th>Email</th><th>Số đơn thành công</th><th class="text-end pe-4">Tổng mua</th></tr></thead>
                <tbody>
                    @forelse($topCustomers as $index => $customer)
                        <tr><td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td><td class="fw-semibold">{{ $customer->name }}</td><td class="text-muted">{{ $customer->email }}</td><td>{{ $customer->successful_orders }} đơn</td><td class="text-end pe-4 text-success fw-bold">{{ number_format($customer->successful_spend ?? 0, 0, ',', '.') }} đ</td></tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu khách hàng mua hàng.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Hàng 2: Chi tiết báo cáo -->
    <div class="row g-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0 text-secondary"><i class="bi bi-pie-chart me-2"></i>Doanh thu theo danh mục</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush rounded-bottom-4">
                        @forelse($revenueByCategory as $cat)
                            <li class="list-group-item d-flex justify-content-between align-items-center p-4">
                                <span class="fw-bold text-dark">{{ $cat->name }}</span>
                                <span class="badge bg-success rounded-pill fs-6 px-3">{{ number_format($cat->total_revenue, 0, ',', '.') }} đ</span>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">
                                Chưa có dữ liệu doanh thu (Cần có đơn hàng ở trạng thái "Đã thanh toán")
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div id="report-recent-orders" class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-secondary"><i class="bi bi-clock-history me-2"></i>5 Đơn hàng gần nhất</h5>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3 text-start">Mã đơn</th>
                                    <th class="py-3 text-start">Khách hàng</th>
                                    <th class="py-3">Giá trị</th>
                                    <th class="py-3">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary text-start">#{{ $order->id }}</td>
                                        <td class="fw-bold text-dark text-start">{{ $order->customer_name ?? ($order->user->name ?? 'Khách') }}</td>
                                        <td class="text-danger fw-bold">{{ number_format($order->total, 0, ',', '.') }} đ</td>
                                        <td>
                                            <!-- Đã đồng bộ toàn bộ từ điển tiếng Việt -->
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
                                            @elseif(strtolower($order->status) == 'cancelled' || $order->status == 'Đã huỷ')
                                                <span class="badge bg-danger rounded-pill px-3">Đã huỷ</span>
                                            @else
                                                <span class="badge bg-dark rounded-pill px-3">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có đơn hàng nào</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-header bg-white border-bottom p-4">
            <h5 class="fw-bold mb-0 text-secondary"><i class="bi bi-trophy me-2 text-warning"></i>Sản phẩm bán chạy nhất</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light"><tr><th class="text-start ps-4">Sản phẩm</th><th>Số lượng đã bán</th><th>Doanh thu</th></tr></thead>
                    <tbody>
                        @forelse($topProducts as $product)
                            <tr><td class="text-start ps-4 fw-semibold">{{ $product->name }}</td><td><span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3">{{ $product->sold_quantity }}</span></td><td class="text-danger fw-bold">{{ number_format($product->revenue, 0, ',', '.') }} đ</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-muted py-4">Chưa có dữ liệu bán hàng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.report-kpi-action').forEach(function (card) {
        card.addEventListener('click', function (event) {
            event.preventDefault();
            document.querySelectorAll('[data-report-view]').forEach(view => view.classList.toggle('is-visible', view.dataset.reportView === this.dataset.reportKind));
            const detail = document.getElementById('report-kpi-detail');
            detail.classList.add('is-active', 'accent-' + this.dataset.reportAccent);
            detail.classList.remove('accent-success', 'accent-pending', 'accent-orders', 'accent-customers');
            detail.classList.add('accent-' + this.dataset.reportAccent);
        });
    });

    const chartElement = document.getElementById('reportRevenueChart');
    if (!chartElement || !window.ApexCharts) return;

    const formatCurrency = value => new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    const chart = new ApexCharts(chartElement, {
        chart: { type: 'bar', height: 250, toolbar: { show: false }, fontFamily: 'DM Sans, sans-serif' },
        series: [{ name: 'Giá trị', data: [@json((float) $successfulRevenue), @json((float) $cancelledRevenue), @json((float) $grossOrderValue)] }],
        xaxis: { categories: ['Bán thành công', 'Đơn bị hủy', 'Tổng giá trị'], labels: { style: { colors: '#71808a' } } },
        yaxis: { labels: { formatter: value => formatCurrency(value), style: { colors: '#71808a' } } },
        colors: ['#238b5c', '#d64d59', '#117c83'],
        plotOptions: { bar: { borderRadius: 7, distributed: true, columnWidth: '44%' } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#e7eeee', strokeDashArray: 4 },
        tooltip: { y: { formatter: value => formatCurrency(value) } },
        legend: { show: false }
    });
    chart.render();

    const monthlyElement = document.getElementById('monthlyRevenueChart');
    if (monthlyElement) {
        new ApexCharts(monthlyElement, {
            chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'DM Sans, sans-serif' },
            series: [{ name: 'Doanh thu', data: @json($monthlyRevenue->values()->all()) }],
            xaxis: { categories: @json($monthlyRevenue->keys()->map(fn ($month) => \Carbon\Carbon::createFromFormat('Y-m', $month)->format('m/Y'))->values()->all()) },
            colors: ['#238b5c'],
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { opacityFrom: .35, opacityTo: .04 } },
            dataLabels: { enabled: false },
            yaxis: { labels: { formatter: value => formatCurrency(value) } },
            tooltip: { y: { formatter: value => formatCurrency(value) } }
        }).render();
    }

    const outcomeElement = document.getElementById('orderOutcomeChart');
    if (outcomeElement) {
        new ApexCharts(outcomeElement, {
            chart: { type: 'donut', height: 260, fontFamily: 'DM Sans, sans-serif' },
            series: [@json($cancelledCount), @json($refundCount), @json(max(0, $totalOrders - $cancelledOrRefundedCount))],
            labels: ['Đã hủy', 'Chuyển/hoàn', 'Còn lại'],
            colors: ['#d64d59', '#f0a202', '#238b5c'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
            tooltip: { y: { formatter: value => value + ' đơn' } }
        }).render();
    }
});
</script>
@endpush
@endsection