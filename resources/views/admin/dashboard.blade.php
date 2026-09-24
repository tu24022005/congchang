@extends('layouts.app')
@section('title', 'Trang tổng quan quản trị') 

@section('content') 
<!-- Bổ sung thư viện ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="container-fluid py-4 admin-dashboard">
    
    <!-- Tiêu đề, Breadcrumb & BỘ LỌC AJAX -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tổng quan</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0"><i class="bi bi-speedometer2 text-success me-2"></i>Tổng quan quản trị</h2>
            <p class="text-muted small">Tổng quan hoạt động kinh doanh và tình trạng cửa hàng.</p>
        </div>
        
        <div class="text-end d-flex flex-column align-items-end">
            <!-- Bộ lọc năm mượt mà -->
            <div class="d-flex align-items-center bg-white px-3 py-2 rounded-pill shadow-sm border mb-2">
                <i class="bi bi-calendar-event text-primary me-2"></i>
                <select id="yearFilter" class="form-select form-select-sm border-0 fw-bold shadow-none p-0 pe-3 admin-year-filter">
                    @foreach($years as $y)
                        <option value="{{ $y }}">Dữ liệu năm {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <span class="text-muted small d-block">Hôm nay</span>
                <strong class="fw-bold text-success d-block">{{ number_format($todayRevenue, 0, ',', '.') }} ₫</strong>
                <span class="text-muted small">{{ $todayOrders }} đơn mới</span>
            </div>
        </div>
    </div>

    <!-- KPI tổng quan -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="text-muted small fw-bold mb-1">TỔNG DOANH THU</p>
                        <i class="bi bi-cash-coin text-warning fs-5"></i>
                    </div>
                    <h3 class="text-danger fw-bold mb-1">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h3>
                    <p class="text-muted admin-stat-note">Chỉ tính đơn đã thanh toán</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="text-muted small fw-bold mb-1">TỔNG ĐƠN HÀNG</p>
                        <i class="bi bi-box-seam text-secondary fs-5"></i>
                    </div>
                    <h3 class="text-dark fw-bold mb-1">{{ $totalOrders }}</h3>
                    <p class="text-muted admin-stat-note">{{ $pendingOrders }} đơn đang chờ xử lý</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="text-muted small fw-bold mb-1">KHÁCH HÀNG</p>
                        <i class="bi bi-people text-primary fs-5"></i>
                    </div>
                    <h3 class="text-dark fw-bold mb-1">{{ $totalCustomers }}</h3>
                    <p class="text-muted admin-stat-note">Tài khoản Customer</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="text-muted small fw-bold mb-1">SẢN PHẨM</p>
                        <i class="bi bi-tag text-info fs-5"></i>
                    </div>
                    <h3 class="text-dark fw-bold mb-1">{{ $totalProducts }}</h3>
                    <p class="text-muted admin-stat-note">{{ $totalCategories }} danh mục đang bán</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body">
                <p class="text-muted small fw-bold mb-1">DOANH THU HÔM NAY</p>
                <h3 class="text-success fw-bold mb-1">{{ number_format($todayRevenue, 0, ',', '.') }} đ</h3>
                <p class="text-muted admin-stat-note">{{ $todayOrders }} đơn hôm nay</p>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body">
                <p class="text-muted small fw-bold mb-1">DOANH THU THÁNG</p>
                <h3 class="text-primary fw-bold mb-1">{{ number_format($monthRevenue, 0, ',', '.') }} đ</h3>
                <p class="text-muted admin-stat-note">Tháng {{ now()->format('m/Y') }}</p>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body">
                <p class="text-muted small fw-bold mb-1">ĐƠN HOÀN THÀNH</p>
                <h3 class="text-success fw-bold mb-1">{{ $countCompleted }}</h3>
                <p class="text-muted admin-stat-note">Đã giao thành công</p>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body">
                <p class="text-muted small fw-bold mb-1">ĐƠN ĐÃ HỦY</p>
                <h3 class="text-danger fw-bold mb-1">{{ $countCancelled }}</h3>
                <p class="text-muted admin-stat-note">Gồm cả yêu cầu hoàn tiền</p>
            </div></div>
        </div>
    </div>

    @if($lowStockProducts->isNotEmpty())
        <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                <div>
                    <strong>Cảnh báo tồn kho thấp</strong>
                    <div class="small mt-1">Có {{ $lowStockProducts->count() }} sản phẩm đang ở mức tồn kho từ 10 trở xuống.</div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach($lowStockProducts as $lowStockProduct)
                            <a href="{{ route('admin.products.edit', $lowStockProduct) }}" class="badge text-bg-light text-decoration-none">
                                {{ $lowStockProduct->name }}: {{ $lowStockProduct->quantity }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Hàng 2: Trạng thái đơn hàng -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-card-list me-2"></i>Trạng thái đơn hàng</h6>
            <div class="row g-3">
                <div class="col">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <p class="text-muted small mb-1"><i class="bi bi-hourglass-split me-1"></i>Đang xử lý</p>
                        <h4 class="fw-bold mb-0 text-warning">{{ $countProcessing }}</h4>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <p class="text-muted small mb-1"><i class="bi bi-check-circle me-1"></i>Đã thanh toán</p>
                        <h4 class="fw-bold mb-0 text-success">{{ $countPaid }}</h4>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <p class="text-muted small mb-1"><i class="bi bi-x-circle me-1"></i>Đã hủy</p>
                        <h4 class="fw-bold mb-0 text-danger">{{ $countCancelled }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng 3: Danh sách cần xử lý -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 admin-dashboard-panel">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Đơn hàng mới nhất</h5>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 admin-dashboard-table">
                        <thead><tr><th class="ps-4">Mã đơn</th><th>Khách hàng</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#{{ $order->id }}</td>
                                    <td>{{ $order->customer_name ?: ($order->user->name ?? 'Khách') }}</td>
                                    <td class="fw-bold">{{ number_format($order->total, 0, ',', '.') }} đ</td>
                                    <td><span class="badge rounded-pill {{ in_array($order->status, ['paid', 'completed'], true) ? 'bg-success' : ($order->status === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $order->status === 'paid' ? 'Đã thanh toán' : ($order->status === 'completed' ? 'Đã nhận hàng' : ($order->status === 'cancelled' ? 'Đã hủy' : 'Đang xử lý')) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Chưa có đơn hàng.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 admin-dashboard-panel">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Sắp hết hàng</h5>
                </div>
                <div class="card-body pt-2">
                    @forelse($lowStockProducts as $product)
                        <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                            <div class="text-truncate me-3"><strong class="d-block text-truncate">{{ $product->name }}</strong><small class="text-muted">{{ $product->category->name ?? 'Chưa phân loại' }}</small></div>
                            <span class="badge {{ $product->quantity <= 3 ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill text-nowrap">{{ $product->quantity }} còn lại</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4"><i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>Tồn kho đang ổn định.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 admin-dashboard-panel">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0"><i class="bi bi-trophy text-warning me-2"></i>Sản phẩm bán chạy</h5>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8"><div class="card border-0 shadow-sm rounded-4 h-100 admin-chart-card"><div class="card-header bg-transparent border-0 pt-4 px-4"><h5 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow text-success me-2"></i>Doanh thu 7 ngày</h5></div><div class="card-body"><div id="weeklyRevenueChart"></div></div></div></div>
            <div class="col-lg-4"><div class="card border-0 shadow-sm rounded-4 h-100 admin-chart-card"><div class="card-header bg-transparent border-0 pt-4 px-4"><h5 class="fw-bold mb-0"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Đơn hàng theo trạng thái</h5></div><div class="card-body"><div id="orderStatusChart"></div></div></div></div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6"><div class="card border-0 shadow-sm rounded-4 h-100 admin-dashboard-panel"><div class="card-header bg-transparent border-0 pt-4 px-4"><h5 class="fw-bold mb-0"><i class="bi bi-trophy text-warning me-2"></i>Top sản phẩm bán chạy</h5></div><div class="card-body">@foreach($topProducts as $index => $product)<div class="top-product-item mb-2"><span class="top-product-rank">{{ $index + 1 }}</span><div class="flex-grow-1 text-truncate"><strong>{{ $product->name }}</strong><small class="d-block text-muted">{{ $product->sold_quantity }} sản phẩm đã bán</small></div><i class="bi bi-chevron-right text-muted"></i></div>@endforeach</div></div></div>
            <div class="col-lg-6"><div class="card border-0 shadow-sm rounded-4 h-100 admin-dashboard-panel"><div class="card-header bg-transparent border-0 pt-4 px-4"><h5 class="fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i>Khách hàng mới</h5></div><div class="card-body">@forelse($newCustomers as $customer)<div class="d-flex align-items-center gap-3 py-2 border-bottom"><span class="account-avatar m-0" style="width:38px;height:38px;font-size:1rem">{{ strtoupper(substr($customer->name, 0, 1)) }}</span><div class="flex-grow-1"><strong class="d-block">{{ $customer->name }}</strong><small class="text-muted">{{ $customer->email }}</small></div><small class="text-muted">{{ $customer->created_at->format('d/m/Y') }}</small></div>@empty<p class="text-muted">Chưa có khách hàng mới.</p>@endforelse</div></div></div>
        </div>
        <div class="card-body px-4">
            <div class="row g-3">
                @forelse($topProducts as $index => $product)
                    <div class="col-md">
                        <div class="top-product-item"><span class="top-product-rank">{{ $index + 1 }}</span><div class="text-truncate"><strong class="d-block text-truncate">{{ $product->name }}</strong><small class="text-muted">{{ $product->sold_quantity }} sản phẩm đã bán</small></div></div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Chưa có dữ liệu bán hàng.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- HÀNG 4: KHU VỰC BIỂU ĐỒ -->
    <div class="row g-4 mb-4">
        <!-- Biểu đồ Doanh thu -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 admin-chart-card">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-primary"><i class="bi bi-graph-up-arrow me-2"></i>Biểu đồ Doanh thu theo Tháng</h5>
                </div>
                <div class="card-body px-2 pb-2">
                    <div id="revenueChart" class="revenue-chart"></div>
                </div>
            </div>
        </div>
        
        <!-- Biểu đồ Phương thức thanh toán -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 admin-chart-card">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-success"><i class="bi bi-pie-chart-fill me-2"></i>Kênh Thanh Toán</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="paymentChart" class="payment-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng 4: Phím tắt điều hướng nhanh -->
    <h6 class="fw-bold mb-3"><i class="bi bi-link-45deg me-2"></i>Lối tắt truy cập nhanh</h6>
    <div class="row g-3">
        <div class="col-md-3">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-success w-100 py-3 fw-bold rounded-3 shadow-sm">
                <i class="bi bi-cart-check fs-4 d-block mb-1"></i> Quản lý Đơn Hàng
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.reports.index') ?? '#' }}" class="btn btn-outline-info w-100 py-3 fw-bold rounded-3 shadow-sm">
                <i class="bi bi-bar-chart-line fs-4 d-block mb-1"></i> Xem Báo Cáo
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary w-100 py-3 fw-bold rounded-3 shadow-sm">
                <i class="bi bi-box-seam fs-4 d-block mb-1"></i> Kho Sản Phẩm
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary w-100 py-3 fw-bold rounded-3 shadow-sm">
                <i class="bi bi-tags fs-4 d-block mb-1"></i> Danh Mục
            </a>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- SCRIPT XỬ LÝ BIỂU ĐỒ VÀ AJAX CALL -->
<!-- ============================================== -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // CẤU HÌNH BIỂU ĐỒ DOANH THU
    var revOptions = {
        series: [{ name: 'Doanh thu', data: [] }],
        chart: { type: 'area', height: 350, toolbar: { show: false }, zoom: { enabled: false } },
        colors: ['#2F80ED'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 90, 100] } },
        xaxis: { categories: ['Th 1', 'Th 2', 'Th 3', 'Th 4', 'Th 5', 'Th 6', 'Th 7', 'Th 8', 'Th 9', 'Th 10', 'Th 11', 'Th 12'] },
        yaxis: { labels: { formatter: function (val) { return val.toLocaleString('vi-VN') + " đ"; } } },
        tooltip: { y: { formatter: function (val) { return val.toLocaleString('vi-VN') + " VNĐ"; } } }
    };
    var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revOptions);
    revenueChart.render();

    // CẤU HÌNH BIỂU ĐỒ TRÒN
    var payOptions = {
        series: [],
        chart: { type: 'donut', height: 320 },
        labels: ['Tiền mặt (COD)', 'Chuyển khoản (PayOS)'],
        colors: ['#00E396', '#008FFB'],
        plotOptions: { donut: { size: '65%' } },
        dataLabels: { enabled: true },
        legend: { position: 'bottom' }
    };
    var paymentChart = new ApexCharts(document.querySelector("#paymentChart"), payOptions);
    paymentChart.render();

    var weeklyRevenueChart = new ApexCharts(document.querySelector("#weeklyRevenueChart"), {
        series: [{ name: 'Doanh thu', data: [] }],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        colors: ['#20c997'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '48%' } },
        xaxis: { categories: [] },
        yaxis: { labels: { formatter: function (val) { return val.toLocaleString('vi-VN') + ' đ'; } } },
        tooltip: { y: { formatter: function (val) { return val.toLocaleString('vi-VN') + ' VNĐ'; } } }
    });
    weeklyRevenueChart.render();

    var orderStatusChart = new ApexCharts(document.querySelector("#orderStatusChart"), {
        series: [], chart: { type: 'donut', height: 300 },
        labels: [], colors: ['#ffc107', '#0d6efd', '#198754', '#dc3545'],
        legend: { position: 'bottom' }, dataLabels: { enabled: true }
    });
    orderStatusChart.render();

    // HÀM GỌI DỮ LIỆU TỪ API
    function loadChartData(year) {
        fetch("{{ route('admin.chart.data') }}?year=" + year)
            .then(response => response.json())
            .then(data => {
                revenueChart.updateSeries([{ data: data.revenue }]);
                paymentChart.updateSeries(data.payments);
                weeklyRevenueChart.updateOptions({
                    xaxis: { categories: data.lastSevenDays.map(function (item) { return item.label; }) }
                });
                weeklyRevenueChart.updateSeries([{ name: 'Doanh thu', data: data.lastSevenDays.map(function (item) { return item.revenue; }) }]);
                orderStatusChart.updateOptions({ labels: data.statuses.labels });
                orderStatusChart.updateSeries(data.statuses.series);
            })
            .catch(error => console.error('Lỗi tải dữ liệu:', error));
    }

    loadChartData(document.getElementById('yearFilter').value);

    document.getElementById('yearFilter').addEventListener('change', function() {
        loadChartData(this.value);
    });
});
</script>
@endsection