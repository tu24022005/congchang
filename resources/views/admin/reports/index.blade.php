@extends('layouts.app')
@section('title', 'Báo cáo & Thống kê - Aloha Beauty')

@push('head')
    <script src="{{ asset_v('js/views/admin-reports-index-blade-php.js') }}" defer></script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset_v('css/views/admin-reports-index-blade-php.css') }}">
@endpush

@section('content')
<div class="container py-4">
    <!-- TIÊU ĐỀ TRANG VÀ CÁC THAO TÁC XUẤT/IN -->
    <div class="report-page-header mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="report-kicker"><i class="bi bi-shield-check me-1"></i>Aloha Beauty &bull; Trung tâm Phân tích & Báo cáo</span>
                <h2 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up-arrow text-primary"></i> Báo cáo & Thống kê Doanh thu
                </h2>
                <p class="text-muted mb-0">
                    Phân tích chuyên sâu doanh thu theo từng tháng/năm, đơn hàng, khách hàng và hiệu suất sản phẩm.
                </p>
            </div>
            
            <div class="d-flex align-items-center gap-2 btn-print-hide flex-wrap">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> In báo cáo
                </button>

                <div class="dropdown">
                    <button class="btn btn-primary rounded-pill px-4 shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download me-1"></i> Xuất dữ liệu (CSV)
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-1">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('admin.reports.export', array_merge($filters, ['type' => 'orders'])) }}">
                                <i class="bi bi-receipt me-2 text-primary"></i> Xuất danh sách đơn hàng
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('admin.reports.export', ['type' => 'monthly', 'year' => $selectedYear]) }}">
                                <i class="bi bi-calendar3 me-2 text-success"></i> Xuất báo cáo 12 tháng (Năm {{ $selectedYear }})
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('admin.reports.export', array_merge($filters, ['type' => 'products'])) }}">
                                <i class="bi bi-trophy me-2 text-warning"></i> Xuất top sản phẩm bán chạy
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- CÁC NÚT MỐC THỜI GIAN NHANH (QUICK RANGE PRESETS) -->
    <div class="report-quick-pills-wrap d-flex flex-wrap align-items-center gap-2 mb-3">
        <span class="text-muted fw-bold small me-1"><i class="bi bi-clock-history me-1"></i>Mốc nhanh:</span>
        <a href="{{ route('admin.reports.index', ['preset' => 'all', 'year' => $selectedYear]) }}" class="report-quick-pill {{ ($filters['preset'] ?? 'all') === 'all' ? 'active' : '' }}">
            Tất cả thời gian
        </a>
        <a href="{{ route('admin.reports.index', ['preset' => 'today']) }}" class="report-quick-pill {{ ($filters['preset'] ?? '') === 'today' ? 'active' : '' }}">
            Hôm nay
        </a>
        <a href="{{ route('admin.reports.index', ['preset' => 'yesterday']) }}" class="report-quick-pill {{ ($filters['preset'] ?? '') === 'yesterday' ? 'active' : '' }}">
            Hôm qua
        </a>
        <a href="{{ route('admin.reports.index', ['preset' => '7days']) }}" class="report-quick-pill {{ ($filters['preset'] ?? '') === '7days' ? 'active' : '' }}">
            7 ngày qua
        </a>
        <a href="{{ route('admin.reports.index', ['preset' => '30days']) }}" class="report-quick-pill {{ ($filters['preset'] ?? '') === '30days' ? 'active' : '' }}">
            30 ngày qua
        </a>
        <a href="{{ route('admin.reports.index', ['preset' => 'this_month']) }}" class="report-quick-pill {{ ($filters['preset'] ?? '') === 'this_month' ? 'active' : '' }}">
            Tháng này
        </a>
        <a href="{{ route('admin.reports.index', ['preset' => 'last_month']) }}" class="report-quick-pill {{ ($filters['preset'] ?? '') === 'last_month' ? 'active' : '' }}">
            Tháng trước
        </a>
        <a href="{{ route('admin.reports.index', ['preset' => 'this_year']) }}" class="report-quick-pill {{ ($filters['preset'] ?? '') === 'this_year' ? 'active' : '' }}">
            Năm nay ({{ now()->year }})
        </a>
    </div>

    <!-- KHUNG BỘ LỌC NÂNG CAO (CHỌN NĂM, THÁNG, TRẠNG THÁI, THANH TOÁN) -->
    <div class="report-filter-box mb-4">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end" id="reportFilterForm">
            <input type="hidden" name="preset" value="custom">

            <!-- Chọn Năm -->
            <div class="col-md-2 col-sm-6">
                <label for="filter-year" class="form-label small fw-bold text-muted mb-1">
                    <i class="bi bi-calendar-event me-1"></i>Chọn Năm
                </label>
                <select name="year" id="filter-year" class="form-select form-select-sm rounded-3">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" @selected($selectedYear == $yr)>Năm {{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Chọn Tháng -->
            <div class="col-md-2 col-sm-6">
                <label for="filter-month" class="form-label small fw-bold text-muted mb-1">
                    <i class="bi bi-calendar-month me-1"></i>Chọn Tháng
                </label>
                <select name="month" id="filter-month" class="form-select form-select-sm rounded-3">
                    <option value="all" @selected(($selectedMonth ?? 'all') === 'all')>Cả năm {{ $selectedYear }}</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected(($selectedMonth ?? '') == (string)$m)>Tháng {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>
            </div>

            <!-- Từ ngày -->
            <div class="col-md-2 col-sm-6">
                <label for="filter-from" class="form-label small fw-bold text-muted mb-1">Từ ngày</label>
                <input type="date" id="filter-from" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control form-control-sm rounded-3">
            </div>

            <!-- Đến ngày -->
            <div class="col-md-2 col-sm-6">
                <label for="filter-to" class="form-label small fw-bold text-muted mb-1">Đến ngày</label>
                <input type="date" id="filter-to" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control form-control-sm rounded-3">
            </div>

            <!-- Trạng thái đơn hàng -->
            <div class="col-md-2 col-sm-6">
                <label for="filter-status" class="form-label small fw-bold text-muted mb-1">Trạng thái đơn</label>
                <select id="filter-status" name="status" class="form-select form-select-sm rounded-3">
                    <option value="">Tất cả trạng thái</option>
                    <option value="processing" @selected(($filters['status'] ?? '') === 'processing')>Chờ xác nhận</option>
                    <option value="confirmed" @selected(($filters['status'] ?? '') === 'confirmed')>Đã xác nhận</option>
                    <option value="packing" @selected(($filters['status'] ?? '') === 'packing')>Đang đóng gói</option>
                    <option value="shipping" @selected(($filters['status'] ?? '') === 'shipping')>Đang giao hàng</option>
                    <option value="paid" @selected(($filters['status'] ?? '') === 'paid')>Đã thanh toán</option>
                    <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Đã nhận hàng (Hoàn tất)</option>
                    <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Đã hủy</option>
                    <option value="refund_pending" @selected(($filters['status'] ?? '') === 'refund_pending')>Chờ duyệt hoàn tiền</option>
                    <option value="refunded" @selected(($filters['status'] ?? '') === 'refunded')>Đã hoàn tiền</option>
                </select>
            </div>

            <!-- Phương thức thanh toán & Nút Áp dụng -->
            <div class="col-md-2 col-sm-6">
                <label for="filter-payment" class="form-label small fw-bold text-muted mb-1">Phương thức</label>
                <select id="filter-payment" name="payment_method" class="form-select form-select-sm rounded-3 mb-2">
                    <option value="">Tất cả phương thức</option>
                    <option value="COD" @selected(($filters['payment_method'] ?? '') === 'COD')>Tiền mặt (COD)</option>
                    <option value="ONLINE" @selected(($filters['payment_method'] ?? '') === 'ONLINE')>Chuyển khoản / PayOS</option>
                </select>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-light border px-3 rounded-pill" title="Đặt lại bộ lọc mặc định">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Đặt lại
                </a>
                <button type="submit" class="btn btn-sm btn-primary px-4 rounded-pill">
                    <i class="bi bi-funnel-fill me-1"></i>Áp dụng bộ lọc
                </button>
            </div>
        </form>
    </div>

    <!-- 6 THẺ CHỈ SỐ KPI CỐT LÕI (CORE E-COMMERCE METRICS) -->
    <div class="row g-3 mb-4">
        <!-- 1. Doanh thu thực tế -->
        <div class="col-xl-4 col-md-6">
            <div class="report-kpi-card kpi-bg-revenue h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-white-50 text-uppercase small fw-bold">Tổng Doanh Thu Thực Thu</span>
                        <h3 class="fw-bold my-1 text-white">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h3>
                        <div class="small text-white-50">
                            <i class="bi bi-check-circle-fill me-1 text-white"></i>{{ $successfulOrders }} đơn thành công (Đã thanh toán / nhận hàng)
                        </div>
                    </div>
                    <div class="kpi-icon-bubble">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Giá trị đơn trung bình (AOV) -->
        <div class="col-xl-4 col-md-6">
            <div class="report-kpi-card kpi-bg-aov h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-white-50 text-uppercase small fw-bold">Giá Trị Đơn Trung Bình (AOV)</span>
                        <h3 class="fw-bold my-1 text-white">{{ number_format($aov, 0, ',', '.') }} đ</h3>
                        <div class="small text-white-50">
                            <i class="bi bi-info-circle me-1 text-white"></i>Mức chi tiêu trung bình trên mỗi đơn hoàn tất
                        </div>
                    </div>
                    <div class="kpi-icon-bubble">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Tỷ lệ hoàn thành đơn hàng -->
        <div class="col-xl-4 col-md-6">
            <div class="report-kpi-card kpi-bg-success h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-white-50 text-uppercase small fw-bold">Tỷ Lệ Giao Thành Công</span>
                        <h3 class="fw-bold my-1 text-white">{{ $successRate }}%</h3>
                        <div class="small text-white-50">
                            <i class="bi bi-pie-chart-fill me-1 text-white"></i>{{ $successfulOrders }} / {{ $totalOrders }} tổng số đơn phát sinh
                        </div>
                    </div>
                    <div class="kpi-icon-bubble">
                        <i class="bi bi-patch-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Tổng số đơn hàng -->
        <div class="col-xl-4 col-md-6">
            <div class="report-kpi-card kpi-bg-orders h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-white-50 text-uppercase small fw-bold">Tổng Đơn Hàng</span>
                        <h3 class="fw-bold my-1 text-white">{{ number_format($totalOrders) }} đơn</h3>
                        <div class="small text-white-50">
                            <span><i class="bi bi-hourglass-split me-1 text-white"></i>{{ $activeOrders }} đang xử lý &bull; <i class="bi bi-x-circle text-white"></i> {{ $cancelledOrders }} đã hủy</span>
                        </div>
                    </div>
                    <div class="kpi-icon-bubble">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Đơn hàng cần xử lý gấp -->
        <div class="col-xl-4 col-md-6">
            <div class="report-kpi-card kpi-bg-pending h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-white-50 text-uppercase small fw-bold">Đơn Cần Xử Lý Ngay</span>
                        <h3 class="fw-bold my-1 text-white">{{ $urgentOrders }} đơn</h3>
                        <div class="small text-white-50">
                            <i class="bi bi-exclamation-triangle-fill me-1 text-white"></i>Chờ xác nhận & đang đóng gói xuất kho
                        </div>
                    </div>
                    <div class="kpi-icon-bubble">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Số lượng sản phẩm bán ra -->
        <div class="col-xl-4 col-md-6">
            <div class="report-kpi-card kpi-bg-products h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-white-50 text-uppercase small fw-bold">Tổng Sản Phẩm Đã Bán</span>
                        <h3 class="fw-bold my-1 text-white">{{ number_format($totalUnitsSold) }} món</h3>
                        <div class="small text-white-50">
                            <i class="bi bi-people-fill me-1 text-white"></i>Phục vụ {{ $customersWithOrders }} khách hàng đã mua
                        </div>
                    </div>
                    <div class="kpi-icon-bubble">
                        <i class="bi bi-bag-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HỆ THỐNG TABS ĐIỀU HƯỚNG BÁO CÁO CHUYÊN SÂU -->
    <ul class="nav report-nav-tabs mb-4 btn-print-hide" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-yearly-btn" data-bs-toggle="tab" data-bs-target="#tab-yearly" type="button" role="tab">
                <i class="bi bi-calendar-range me-2"></i>Thống kê Theo Tháng & Năm
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-money-btn" data-bs-toggle="tab" data-bs-target="#tab-money" type="button" role="tab">
                <i class="bi bi-wallet2 me-2"></i>Dòng tiền & Phương thức thanh toán
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-products-btn" data-bs-toggle="tab" data-bs-target="#tab-products" type="button" role="tab">
                <i class="bi bi-box2-heart me-2"></i>Sản phẩm & Tồn kho
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-customers-btn" data-bs-toggle="tab" data-bs-target="#tab-customers" type="button" role="tab">
                <i class="bi bi-person-lines-fill me-2"></i>Khách hàng VIP & Đơn mới
            </button>
        </li>
    </ul>

    <!-- NỘI DUNG CÁC TABS -->
    <div class="tab-content" id="reportTabsContent">

        <!-- ========================================== -->
        <!-- TAB 1: THỐNG KÊ THEO THÁNG VÀ NĂM (TRỌNG TÂM) -->
        <!-- ========================================== -->
        <div class="tab-pane fade show active" id="tab-yearly" role="tabpanel">
            
            <!-- BIỂU ĐỒ 12 THÁNG NĂM ĐƯỢC CHỌN -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <span class="report-kicker">XU HƯỚNG KINH DOANH 12 THÁNG</span>
                        <h5 class="fw-bold text-dark mb-0">
                            Biểu đồ Doanh thu & Đơn hàng Năm {{ $selectedYear }}
                        </h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-2 fw-semibold">
                            Tổng năm: {{ number_format($yearlySummary['total_revenue'], 0, ',', '.') }} đ
                        </span>
                        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2 fw-semibold">
                            {{ $yearlySummary['successful_orders'] }} đơn thành công
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="monthlyRevenueAndOrdersChart" class="view-inline-1"></div>
                </div>
            </div>

            <!-- BẢNG SỐ LIỆU CHI TIẾT 12 THÁNG TRONG NĂM -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-table me-2 text-primary"></i>Bảng Tổng hợp 12 Tháng - Năm {{ $selectedYear }}
                        </h5>
                        <small class="text-muted">Chi tiết doanh thu, tăng trưởng MoM, số đơn thành công, tỷ lệ hoàn tất và giá trị đơn trung bình.</small>
                    </div>
                    <a href="{{ route('admin.reports.export', ['type' => 'monthly', 'year' => $selectedYear]) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 btn-print-hide">
                        <i class="bi bi-file-earmark-excel me-1"></i>Xuất Excel Bảng Này
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table report-table table-hover align-middle mb-0 text-center">
                        <thead>
                            <tr>
                                <th class="text-start ps-4">Tháng</th>
                                <th class="text-end">Doanh thu thực thu</th>
                                <th>Tăng trưởng MoM</th>
                                <th>Đơn thành công</th>
                                <th>Đơn hủy</th>
                                <th>Tổng đơn</th>
                                <th>Tỷ lệ thành công</th>
                                <th class="text-end">AOV (Giá trị TB)</th>
                                <th class="text-end pe-4">Đóng góp năm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($yearlyMonthlyStats as $stat)
                                <tr>
                                    <td class="text-start ps-4 fw-bold text-dark">
                                        <a href="{{ route('admin.reports.index', ['preset' => 'custom', 'year' => $selectedYear, 'month' => $stat['month']]) }}" class="text-decoration-none text-primary">
                                            {{ $stat['month_label'] }}
                                        </a>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        {{ number_format($stat['revenue'], 0, ',', '.') }} đ
                                    </td>
                                    <td>
                                        @if(is_null($stat['growth_rate']))
                                            <span class="growth-neutral">-</span>
                                        @elseif($stat['growth_rate'] > 0)
                                            <span class="growth-up"><i class="bi bi-arrow-up-right me-1"></i>+{{ $stat['growth_rate'] }}%</span>
                                        @elseif($stat['growth_rate'] < 0)
                                            <span class="growth-down"><i class="bi bi-arrow-down-right me-1"></i>{{ $stat['growth_rate'] }}%</span>
                                        @else
                                            <span class="growth-neutral">0%</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-2">
                                            {{ $stat['successful_orders'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($stat['cancelled_orders'] > 0)
                                            <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-2">{{ $stat['cancelled_orders'] }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold text-secondary">{{ $stat['total_orders'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <span>{{ $stat['completion_rate'] }}%</span>
                                            <div class="progress view-inline-2">
                                                <div class="progress-bar bg-success" role="progressbar" data-inline-width="{{ $stat['completion_rate'] }}" class="inline-dynamic-width"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-semibold text-dark">
                                        {{ number_format($stat['aov'], 0, ',', '.') }} đ
                                    </td>
                                    <td class="text-end pe-4 text-muted small">
                                        {{ $stat['share'] }}%
                                    </td>
                                </tr>
                            @endforeach
                            <!-- HÀNG TỔNG CỘNG CẢ NĂM -->
                            <tr class="table-light fw-bold border-top border-2">
                                <td class="text-start ps-4 text-uppercase text-primary fs-6">
                                    <i class="bi bi-award-fill me-1"></i>CẢ NĂM {{ $selectedYear }}
                                </td>
                                <td class="text-end text-success fs-6">
                                    {{ number_format($yearlySummary['total_revenue'], 0, ',', '.') }} đ
                                </td>
                                <td class="text-muted">-</td>
                                <td class="text-success">{{ $yearlySummary['successful_orders'] }}</td>
                                <td class="text-danger">{{ $yearlySummary['cancelled_orders'] }}</td>
                                <td class="text-dark">{{ $yearlySummary['total_orders'] }}</td>
                                <td>{{ $yearlySummary['completion_rate'] }}%</td>
                                <td class="text-end text-primary fs-6">{{ number_format($yearlySummary['aov'], 0, ',', '.') }} đ</td>
                                <td class="text-end pe-4 text-primary">100%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BẢNG SO SÁNH GIỮA CÁC NĂM (YEAR-OVER-YEAR) -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-layers-fill me-2 text-warning"></i>So sánh Doanh thu giữa các Năm
                    </h5>
                    <small class="text-muted">Tổng quan tăng trưởng và quy mô kinh doanh qua từng năm hoạt động của cửa hàng.</small>
                </div>
                <div class="table-responsive">
                    <table class="table report-table table-hover align-middle mb-0 text-center">
                        <thead>
                            <tr>
                                <th class="text-start ps-4">Năm</th>
                                <th class="text-end">Doanh thu năm</th>
                                <th>Đơn thành công</th>
                                <th>Đơn bị hủy</th>
                                <th>Tổng đơn</th>
                                <th>Tỷ lệ hoàn tất</th>
                                <th class="text-end pe-4">Giá trị TB/Đơn (AOV)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($yearlyComparison as $yrData)
                                <tr @if($yrData['year'] == $selectedYear) class="table-active" @endif>
                                    <td class="text-start ps-4 fw-bold">
                                        <a href="{{ route('admin.reports.index', ['preset' => 'custom', 'year' => $yrData['year']]) }}" class="text-decoration-none">
                                            Năm {{ $yrData['year'] }}
                                            @if($yrData['year'] == $selectedYear)
                                                <span class="badge bg-primary ms-1">Đang xem</span>
                                            @endif
                                        </a>
                                    </td>
                                    <td class="text-end fw-bold text-success">{{ number_format($yrData['revenue'], 0, ',', '.') }} đ</td>
                                    <td><span class="badge bg-success-subtle text-success-emphasis rounded-pill px-2">{{ $yrData['successful_orders'] }}</span></td>
                                    <td><span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-2">{{ $yrData['cancelled_orders'] }}</span></td>
                                    <td class="fw-semibold text-secondary">{{ $yrData['total_orders'] }}</td>
                                    <td>{{ $yrData['completion_rate'] }}%</td>
                                    <td class="text-end pe-4 fw-semibold text-primary">{{ number_format($yrData['aov'], 0, ',', '.') }} đ</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-3 text-muted">Chưa có dữ liệu giữa các năm.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- NẾU ĐANG CHỌN XEM MỘT THÁNG CỤ THỂ: HIỂN THỊ BIỂU ĐỒ TỪNG NGÀY TRONG THÁNG -->
            @if(!empty($dailyStats))
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <span class="report-kicker">CHI TIẾT THEO NGÀY</span>
                        <h5 class="fw-bold text-dark mb-0">
                            Biến động Doanh thu từng ngày - Tháng {{ $selectedMonth }}/{{ $selectedYear }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div id="dailyRevenueChart" class="view-inline-3"></div>
                    </div>
                </div>
            @endif

        </div>

        <!-- ========================================== -->
        <!-- TAB 2: DÒNG TIỀN & PHƯƠNG THỨC THANH TOÁN -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="tab-money" role="tabpanel">
            <div class="row g-4 mb-4">
                <!-- Cơ cấu thanh toán COD vs PayOS -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-4">
                            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-credit-card-2-back me-2 text-info"></i>Phương thức thanh toán</h5>
                            <small class="text-muted">Tỷ trọng tiền bán thành công giữa Tiền mặt (COD) và Chuyển khoản (PayOS).</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-sm-6">
                                    <div id="paymentMethodChart" class="view-inline-4"></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-3 bg-light mb-3 border">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="fw-bold text-secondary"><i class="bi bi-cash-coin me-1 text-success"></i>Tiền mặt (COD)</span>
                                            <span class="badge bg-success rounded-pill">{{ $codShareRate }}%</span>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-0">{{ number_format($successfulCodRevenue, 0, ',', '.') }} đ</h5>
                                        <small class="text-muted">{{ $successfulCodCount }} đơn hàng thành công</small>
                                    </div>

                                    <div class="p-3 rounded-3 bg-light border">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="fw-bold text-secondary"><i class="bi bi-bank me-1 text-primary"></i>Chuyển khoản (PayOS)</span>
                                            <span class="badge bg-primary rounded-pill">{{ $payosShareRate }}%</span>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-0">{{ number_format($successfulPayosRevenue, 0, ',', '.') }} đ</h5>
                                        <small class="text-muted">{{ $successfulPayosCount }} đơn hàng thành công</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phân bổ trạng thái đơn hàng -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-4">
                            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Phân bổ Trạng thái Đơn hàng</h5>
                            <small class="text-muted">Tỷ lệ các trạng thái đơn hàng trong phạm vi bộ lọc hiện tại.</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-sm-6">
                                    <div id="orderStatusChart" class="view-inline-4"></div>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="list-group list-group-flush small">
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span><i class="bi bi-hourglass-split text-warning me-1"></i>Chờ xác nhận:</span>
                                            <strong>{{ $statusCounts->get('processing')?->total ?? 0 }} đơn</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span><i class="bi bi-check2 text-info me-1"></i>Đã xác nhận:</span>
                                            <strong>{{ $statusCounts->get('confirmed')?->total ?? 0 }} đơn</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span><i class="bi bi-box-seam text-secondary me-1"></i>Đang đóng gói:</span>
                                            <strong>{{ $statusCounts->get('packing')?->total ?? 0 }} đơn</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span><i class="bi bi-truck text-primary me-1"></i>Đang giao hàng:</span>
                                            <strong>{{ $statusCounts->get('shipping')?->total ?? 0 }} đơn</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span><i class="bi bi-check2-circle text-success me-1"></i>Đã thanh toán / Hoàn tất:</span>
                                            <strong class="text-success">{{ $successfulOrders }} đơn</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span><i class="bi bi-x-circle text-danger me-1"></i>Đã hủy:</span>
                                            <strong class="text-danger">{{ $cancelledOrders }} đơn</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tổng quan giá trị đơn hàng (Thành công vs Bị hủy vs GMV) -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-bar-chart-steps me-2 text-success"></i>Cân đối Dòng tiền & Tổn thất đơn hủy</h5>
                    <small class="text-muted">Đánh giá doanh thu thực tế thu về so với tổng giá trị khách đặt và tiền bị mất do đơn hủy.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 bg-success-subtle text-success-emphasis border border-success-subtle">
                                <span class="small text-uppercase fw-bold"><i class="bi bi-check2-circle me-1"></i>Doanh thu thực thu</span>
                                <h4 class="fw-bold mt-1 mb-0">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h4>
                                <small class="text-success-emphasis">Đã thu tiền từ {{ $successfulOrders }} đơn</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 bg-danger-subtle text-danger-emphasis border border-danger-subtle">
                                <span class="small text-uppercase fw-bold"><i class="bi bi-x-circle me-1"></i>Tổn thất đơn bị hủy</span>
                                <h4 class="fw-bold mt-1 mb-0">{{ number_format($cancelledRevenue, 0, ',', '.') }} đ</h4>
                                <small class="text-danger-emphasis">{{ $cancelledOrders }} đơn hủy (Tỷ lệ hủy: {{ $cancelledRate }}%)</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 bg-primary-subtle text-primary-emphasis border border-primary-subtle">
                                <span class="small text-uppercase fw-bold"><i class="bi bi-wallet2 me-1"></i>Tổng giá trị đặt hàng (GMV)</span>
                                <h4 class="fw-bold mt-1 mb-0">{{ number_format($grossOrderValue, 0, ',', '.') }} đ</h4>
                                <small class="text-primary-emphasis">Bao gồm cả đơn đang xử lý và chưa thanh toán</small>
                            </div>
                        </div>
                    </div>

                    <div id="cashFlowBarChart" class="view-inline-5"></div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: SẢN PHẨM & CẢNH BÁO TỒN KHO -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="tab-products" role="tabpanel">
            
            <!-- DOANH THU THEO DANH MỤC SẢN PHẨM -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-tag-fill me-2 text-primary"></i>Doanh thu theo Danh mục sản phẩm</h5>
                    <small class="text-muted">Xếp hạng danh mục mỹ phẩm đem lại doanh thu cao nhất cho cửa hàng.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div id="categoryRevenueChart" class="view-inline-5"></div>
                        </div>
                        <div class="col-md-7">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Danh mục</th>
                                            <th class="text-center">Số lượng bán</th>
                                            <th class="text-end">Doanh thu</th>
                                            <th class="text-end pe-3">Tỷ trọng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($revenueByCategory as $cat)
                                            @php
                                                $catShare = $totalRevenue > 0 ? round(($cat->total_revenue / $totalRevenue) * 100, 1) : 0;
                                            @endphp
                                            <tr>
                                                <td class="fw-bold text-dark">{{ $cat->name }}</td>
                                                <td class="text-center"><span class="badge bg-secondary rounded-pill">{{ $cat->total_sold }}</span></td>
                                                <td class="text-end fw-bold text-success">{{ number_format($cat->total_revenue, 0, ',', '.') }} đ</td>
                                                <td class="text-end pe-3"><span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">{{ $catShare }}%</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted py-3">Chưa có dữ liệu danh mục trong phạm vi bộ lọc.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOP 10 SẢN PHẨM BÁN CHẠY NHẤT -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top 10 Sản phẩm bán chạy nhất</h5>
                        <small class="text-muted">Sản phẩm có số lượng bán cao nhất từ các đơn hàng thành công.</small>
                    </div>
                    <a href="{{ route('admin.reports.export', array_merge($filters, ['type' => 'products'])) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 btn-print-hide">
                        <i class="bi bi-download me-1"></i>Xuất Danh sách SP
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table report-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center ps-3 view-inline-6">#</th>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th class="text-center">Số lượng bán</th>
                                <th class="text-end">Doanh thu</th>
                                <th class="text-center">Tồn kho</th>
                                <th class="text-center pe-3 btn-print-hide">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $index => $item)
                                <tr>
                                    <td class="text-center ps-3 fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="rounded-3 border view-inline-7">
                                            @else
                                                <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted view-inline-8">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark">{{ $item->name }}</div>
                                                <small class="text-muted">{{ $item->product_code ?? ('#' . $item->id) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $item->category_name ?? 'Chưa phân loại' }}</span></td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-1 fs-6">
                                            {{ $item->sold_quantity }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        {{ number_format($item->revenue, 0, ',', '.') }} đ
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->stock <= 10 ? 'bg-danger' : 'bg-secondary' }} rounded-pill">
                                            {{ $item->stock }}
                                        </span>
                                    </td>
                                    <td class="text-center pe-3 btn-print-hide">
                                        <a href="{{ route('admin.products.edit', $item->id) }}" class="btn btn-sm btn-light border rounded-pill px-3" title="Chỉnh sửa sản phẩm">
                                            <i class="bi bi-pencil-square me-1"></i>Sửa
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">Chưa có dữ liệu bán hàng.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CẢNH BÁO TỒN KHO THẤP (SẢN PHẨM SẮP HẾT HÀNG) -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1 text-danger">
                            <i class="bi bi-exclamation-octagon-fill me-2"></i>Cảnh báo Tồn kho Thấp (Sắp hết hàng &bull; &le; 10 cái)
                        </h5>
                        <small class="text-muted">Các mặt hàng cần chủ động nhập thêm để không làm gián đoạn chuỗi cung ứng và bán lẻ.</small>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-print-hide">
                        <i class="bi bi-box-seam me-1"></i>Quản lý kho hàng
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table report-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Sản phẩm</th>
                                <th>Danh mục</th>
                                <th class="text-end">Giá bán</th>
                                <th class="text-center">Tồn kho hiện tại</th>
                                <th>Mức cảnh báo</th>
                                <th class="text-center pe-4 btn-print-hide">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $lowP)
                                <tr class="{{ $lowP->quantity <= 5 ? 'low-stock-critical' : 'low-stock-warning' }}">
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $lowP->name }}</div>
                                        <small class="text-muted">{{ $lowP->product_code ?? ('#' . $lowP->id) }}</small>
                                    </td>
                                    <td>{{ $lowP->category?->name ?? 'N/A' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($lowP->price, 0, ',', '.') }} đ</td>
                                    <td class="text-center">
                                        <span class="badge {{ $lowP->quantity <= 5 ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill px-3 py-1 fs-6">
                                            {{ $lowP->quantity }} cái
                                        </span>
                                    </td>
                                    <td>
                                        @if($lowP->quantity == 0)
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Hết hàng hoàn toàn</span>
                                        @elseif($lowP->quantity <= 5)
                                            <span class="badge bg-danger-subtle text-danger-emphasis"><i class="bi bi-exclamation-triangle-fill me-1"></i>Khẩn cấp (&le; 5)</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis"><i class="bi bi-exclamation-circle-fill me-1"></i>Cảnh báo (&le; 10)</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4 btn-print-hide">
                                        <a href="{{ route('admin.products.edit', $lowP->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Nhập kho / Sửa
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-success py-4">
                                        <i class="bi bi-check-circle fs-4 d-block mb-1"></i>
                                        Kho hàng an toàn! Hiện không có sản phẩm nào sắp hết hàng (&le; 10 cái).
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- TAB 4: KHÁCH HÀNG VIP & ĐƠN HÀNG MỚI NHẤT -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="tab-customers" role="tabpanel">
            
            <!-- TOP KHÁCH HÀNG VIP -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-star-fill me-2 text-warning"></i>Khách hàng VIP & Mua nhiều nhất</h5>
                        <small class="text-muted">Danh sách khách hàng có tổng giá trị chi tiêu lớn nhất cho cửa hàng.</small>
                    </div>
                    <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-2">
                        {{ $topCustomers->count() }} khách hàng hàng đầu
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table report-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center ps-3 view-inline-6">#</th>
                                <th>Khách hàng</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th class="text-center">Đơn thành công</th>
                                <th class="text-end pe-4">Tổng tiền chi tiêu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $index => $customer)
                                <tr>
                                    <td class="text-center ps-3 fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $customer->name }}</div>
                                        <small class="text-muted">Gia nhập: {{ $customer->created_at?->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="text-muted">{{ $customer->email }}</td>
                                    <td class="text-muted">{{ $customer->phone ?? 'Chưa cập nhật' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-1">
                                            {{ $customer->successful_orders }} đơn
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-success fs-6">
                                        {{ number_format($customer->successful_spend ?? 0, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu khách hàng mua hàng.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 10 ĐƠN HÀNG GẦN ĐÂY NHẤT -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>10 Đơn hàng phát sinh mới nhất</h5>
                        <small class="text-muted">Theo dõi nhanh các đơn hàng vừa được khách đặt trong hệ thống.</small>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-print-hide">
                        Xem tất cả đơn hàng
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table report-table table-hover align-middle mb-0 text-center">
                        <thead>
                            <tr>
                                <th class="ps-4 text-start">Mã đơn</th>
                                <th class="text-start">Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Thanh toán</th>
                                <th class="text-end">Giá trị đơn</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-4 btn-print-hide">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="ps-4 text-start fw-bold text-primary">#{{ $order->id }}</td>
                                    <td class="text-start">
                                        <div class="fw-semibold text-dark">{{ $order->customer_name ?: ($order->user->name ?? 'Khách lẻ') }}</div>
                                        <small class="text-muted">{{ $order->customer_phone ?: ($order->user->phone ?? '') }}</small>
                                    </td>
                                    <td class="text-muted small">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge {{ $order->payment_method === 'COD' ? 'bg-secondary' : 'bg-primary' }} rounded-pill">
                                            {{ $order->payment_method ?? 'COD' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        {{ number_format($order->total, 0, ',', '.') }} đ
                                    </td>
                                    <td>
                                        @if($order->status === 'paid' || $order->status === 'Đã thanh toán')
                                            <span class="badge bg-success rounded-pill px-3 py-1">Đã thanh toán</span>
                                        @elseif($order->status === 'completed')
                                            <span class="badge bg-success rounded-pill px-3 py-1">Đã nhận hàng</span>
                                        @elseif($order->status === 'processing' || $order->status === 'Đang xử lý')
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Chờ xác nhận</span>
                                        @elseif($order->status === 'confirmed')
                                            <span class="badge bg-info text-dark rounded-pill px-3 py-1">Đã xác nhận</span>
                                        @elseif($order->status === 'packing')
                                            <span class="badge bg-secondary rounded-pill px-3 py-1">Đang gói hàng</span>
                                        @elseif($order->status === 'shipping')
                                            <span class="badge bg-primary rounded-pill px-3 py-1">Đang giao</span>
                                        @elseif($order->status === 'cancelled' || $order->status === 'Đã huỷ')
                                            <span class="badge bg-danger rounded-pill px-3 py-1">Đã hủy</span>
                                        @elseif(in_array($order->status, ['refund_pending', 'refunded']))
                                            <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-1">Hoàn tiền</span>
                                        @else
                                            <span class="badge bg-dark rounded-pill px-3 py-1">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 btn-print-hide">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                            Xem
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-4 text-muted">Chưa có đơn hàng nào trong hệ thống.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- KHỐI CHỮ KÝ DÀNH RIÊNG CHO BẢN IN (PRINT VIEW) -->
    <div class="print-signature-block">
        <div class="row text-center mt-5">
            <div class="col-4">
                <strong>Người Lập Báo Cáo</strong><br>
                <small class="text-muted">(Ký, ghi rõ họ tên)</small>
                <div class="view-inline-9"></div>
                <div class="fw-bold">{{ Auth::user()->name ?? 'Ban Quản trị' }}</div>
            </div>
            <div class="col-4">
                <strong>Kế Toán Trưởng</strong><br>
                <small class="text-muted">(Ký, ghi rõ họ tên)</small>
                <div class="view-inline-9"></div>
                <div class="fw-bold">Phòng Tài chính - Kế toán</div>
            </div>
            <div class="col-4">
                <strong>Giám Đốc Phê Duyệt</strong><br>
                <small class="text-muted">(Ký, đóng dấu)</small>
                <div class="view-inline-9"></div>
                <div class="fw-bold">Aloha Beauty Care</div>
            </div>
        </div>
        <div class="text-center text-muted small mt-4 pt-3 border-top">
            Báo cáo được trích xuất từ Hệ thống Quản trị Aloha Beauty vào lúc {{ now()->format('H:i:s d/m/Y') }}.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset_v('js/views/admin-reports-index-blade-php.js') }}" defer></script>
@endpush