<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $timePreset = $request->input('preset', 'all');
        $status = $request->input('status');
        $paymentMethod = $request->input('payment_method');
        $from = $request->input('from');
        $to = $request->input('to');

        // Tìm danh sách các năm có đơn hàng
        $availableYears = Order::selectRaw('YEAR(created_at) as yr')
            ->distinct()
            ->orderByDesc('yr')
            ->pluck('yr')
            ->filter()
            ->values();

        $currentYear = now()->year;
        if ($availableYears->isEmpty()) {
            $availableYears = collect([$currentYear]);
        }
        if (!$availableYears->contains($currentYear)) {
            $availableYears->prepend($currentYear);
            $availableYears = $availableYears->unique()->values();
        }

        $selectedYear = (int) $request->input('year', $availableYears->first() ?? $currentYear);
        $selectedMonth = $request->input('month', 'all');

        // Xử lý các mốc thời gian nhanh (presets)
        if ($timePreset === 'today') {
            $from = now()->toDateString();
            $to = now()->toDateString();
            $selectedYear = now()->year;
            $selectedMonth = (string) now()->month;
        } elseif ($timePreset === 'yesterday') {
            $from = now()->subDay()->toDateString();
            $to = now()->subDay()->toDateString();
            $selectedYear = now()->subDay()->year;
            $selectedMonth = (string) now()->subDay()->month;
        } elseif ($timePreset === '7days') {
            $from = now()->subDays(6)->toDateString();
            $to = now()->toDateString();
        } elseif ($timePreset === '30days') {
            $from = now()->subDays(29)->toDateString();
            $to = now()->toDateString();
        } elseif ($timePreset === 'this_month') {
            $from = now()->startOfMonth()->toDateString();
            $to = now()->endOfMonth()->toDateString();
            $selectedYear = now()->year;
            $selectedMonth = (string) now()->month;
        } elseif ($timePreset === 'last_month') {
            $from = now()->subMonth()->startOfMonth()->toDateString();
            $to = now()->subMonth()->endOfMonth()->toDateString();
            $selectedYear = now()->subMonth()->year;
            $selectedMonth = (string) now()->subMonth()->month;
        } elseif ($timePreset === 'this_year') {
            $from = Carbon::create($selectedYear, 1, 1)->startOfYear()->toDateString();
            $to = Carbon::create($selectedYear, 12, 31)->endOfYear()->toDateString();
            $selectedYear = now()->year;
            $selectedMonth = 'all';
        } elseif ($timePreset === 'custom_month' || ($selectedMonth !== 'all' && is_numeric($selectedMonth) && empty($from) && empty($to))) {
            $from = Carbon::create($selectedYear, (int) $selectedMonth, 1)->startOfMonth()->toDateString();
            $to = Carbon::create($selectedYear, (int) $selectedMonth, 1)->endOfMonth()->toDateString();
        }

        $filters = [
            'preset' => $timePreset,
            'year' => $selectedYear,
            'month' => $selectedMonth,
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'payment_method' => $paymentMethod,
        ];

        // 1. Base Query cho đơn hàng theo bộ lọc
        $ordersQuery = Order::query();
        if (!empty($filters['from'])) {
            $ordersQuery->whereDate('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $ordersQuery->whereDate('created_at', '<=', $filters['to']);
        }
        if (!empty($filters['status'])) {
            $ordersQuery->where('status', $filters['status']);
        }
        if (!empty($filters['payment_method'])) {
            if ($filters['payment_method'] === 'COD') {
                $ordersQuery->where('payment_method', 'COD');
            } elseif ($filters['payment_method'] === 'ONLINE') {
                $ordersQuery->where('payment_method', '!=', 'COD');
            }
        }

        // 2. Các chỉ số KPI tổng quan
        $totalOrders = (clone $ordersQuery)->count();
        $successfulOrders = (clone $ordersQuery)->whereIn('status', ['paid', 'completed'])->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', 'cancelled')->count();
        $refundOrders = (clone $ordersQuery)->whereIn('status', ['refund_pending', 'refunded'])->count();
        $activeOrders = (clone $ordersQuery)->whereNotIn('status', ['paid', 'completed', 'cancelled', 'refunded'])->count();
        $urgentOrders = (clone $ordersQuery)->whereIn('status', ['processing', 'confirmed', 'packing'])->count();

        $totalRevenue = (float) (clone $ordersQuery)->whereIn('status', ['paid', 'completed'])->sum('total');
        $successfulRevenue = $totalRevenue;
        $cancelledRevenue = (float) (clone $ordersQuery)->where('status', 'cancelled')->sum('total');
        $refundRevenue = (float) (clone $ordersQuery)->whereIn('status', ['refund_pending', 'refunded'])->sum('total');
        $grossOrderValue = (float) (clone $ordersQuery)->sum('total');

        // AOV & Tỷ lệ
        $aov = $successfulOrders > 0 ? round($totalRevenue / $successfulOrders) : 0;
        $successRate = $totalOrders > 0 ? round(($successfulOrders / $totalOrders) * 100, 1) : 0;
        $cancelledRate = $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100, 1) : 0;
        $refundRate = $totalOrders > 0 ? round(($refundOrders / $totalOrders) * 100, 1) : 0;
        $cancelledOrRefundedCount = $cancelledOrders + $refundOrders;
        $cancelledOrRefundedRate = $totalOrders > 0 ? round(($cancelledOrRefundedCount / $totalOrders) * 100, 1) : 0;

        // Tổng sản phẩm xuất bán thành công
        $totalUnitsSold = (int) DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'completed'])
            ->when($filters['from'] ?? null, fn ($q, $f) => $q->whereDate('orders.created_at', '>=', $f))
            ->when($filters['to'] ?? null, fn ($q, $t) => $q->whereDate('orders.created_at', '<=', $t))
            ->when($filters['payment_method'] ?? null, function ($q, $pm) {
                if ($pm === 'COD') $q->where('orders.payment_method', 'COD');
                elseif ($pm === 'ONLINE') $q->where('orders.payment_method', '!=', 'COD');
            })
            ->sum('order_items.quantity');

        // Phương thức thanh toán
        $successfulCodRevenue = (float) (clone $ordersQuery)->whereIn('status', ['paid', 'completed'])->where('payment_method', 'COD')->sum('total');
        $successfulCodCount = (clone $ordersQuery)->whereIn('status', ['paid', 'completed'])->where('payment_method', 'COD')->count();
        $successfulPayosRevenue = (float) (clone $ordersQuery)->whereIn('status', ['paid', 'completed'])->where('payment_method', '!=', 'COD')->sum('total');
        $successfulPayosCount = (clone $ordersQuery)->whereIn('status', ['paid', 'completed'])->where('payment_method', '!=', 'COD')->count();
        $codShareRate = $totalRevenue > 0 ? round(($successfulCodRevenue / $totalRevenue) * 100, 1) : 0;
        $payosShareRate = $totalRevenue > 0 ? round(($successfulPayosRevenue / $totalRevenue) * 100, 1) : 0;

        // Trạng thái đơn hàng
        $statusCounts = (clone $ordersQuery)
            ->select('status', DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as revenue'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Khách hàng
        $totalCustomers = User::whereIn('role', ['customer', 'user'])->orWhereNull('role')->count();
        $customersWithOrders = Order::distinct('user_id')->whereNotNull('user_id')->count();
        $customerAccounts = User::whereIn('role', ['customer', 'user'])->orWhereNull('role')
            ->withCount('orders')
            ->latest()
            ->take(10)
            ->get(['id', 'name', 'email', 'created_at']);

        // 3. THỐNG KÊ CHI TIẾT 12 THÁNG TRONG NĂM ($selectedYear)
        $yearOrdersRaw = Order::whereYear('created_at', $selectedYear)->get(['id', 'total', 'status', 'created_at']);
        $yearItemsRaw = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereYear('orders.created_at', $selectedYear)
            ->whereIn('orders.status', ['paid', 'completed'])
            ->selectRaw('MONTH(orders.created_at) as m, SUM(order_items.quantity) as items')
            ->groupBy('m')
            ->pluck('items', 'm');

        $yearlyMonthlyStats = [];
        $yearTotalRevenue = 0;
        $yearTotalSuccessfulOrders = 0;
        $yearTotalCancelledOrders = 0;
        $yearTotalOrders = 0;
        $yearTotalItemsSold = 0;
        $prevMonthRevenue = 0;

        for ($m = 1; $m <= 12; $m++) {
            $monthOrders = $yearOrdersRaw->filter(fn ($o) => $o->created_at->month === $m);
            $monthSuccess = $monthOrders->whereIn('status', ['paid', 'completed']);
            $monthCancelled = $monthOrders->where('status', 'cancelled');

            $mRev = (float) $monthSuccess->sum('total');
            $mSucc = $monthSuccess->count();
            $mCanc = $monthCancelled->count();
            $mTot = $monthOrders->count();
            $mItems = (int) ($yearItemsRaw->get($m) ?? 0);
            $mAov = $mSucc > 0 ? round($mRev / $mSucc) : 0;
            $mCompRate = $mTot > 0 ? round(($mSucc / $mTot) * 100, 1) : 0;

            // MoM Growth %
            $growthRate = null;
            if ($prevMonthRevenue > 0) {
                $growthRate = round((($mRev - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1);
            } elseif ($mRev > 0 && $prevMonthRevenue == 0 && $m > 1) {
                $growthRate = 100.0;
            }

            $yearlyMonthlyStats[$m] = [
                'month' => $m,
                'month_label' => 'Tháng ' . str_pad($m, 2, '0', STR_PAD_LEFT),
                'revenue' => $mRev,
                'successful_orders' => $mSucc,
                'cancelled_orders' => $mCanc,
                'total_orders' => $mTot,
                'items_sold' => $mItems,
                'aov' => $mAov,
                'completion_rate' => $mCompRate,
                'growth_rate' => $growthRate,
            ];

            $yearTotalRevenue += $mRev;
            $yearTotalSuccessfulOrders += $mSucc;
            $yearTotalCancelledOrders += $mCanc;
            $yearTotalOrders += $mTot;
            $yearTotalItemsSold += $mItems;

            if ($mRev > 0) {
                $prevMonthRevenue = $mRev;
            }
        }

        // Tính tỷ lệ đóng góp của từng tháng vào tổng năm
        foreach ($yearlyMonthlyStats as &$stat) {
            $stat['share'] = $yearTotalRevenue > 0 ? round(($stat['revenue'] / $yearTotalRevenue) * 100, 1) : 0;
        }
        unset($stat);

        $yearlySummary = [
            'year' => $selectedYear,
            'total_revenue' => $yearTotalRevenue,
            'successful_orders' => $yearTotalSuccessfulOrders,
            'cancelled_orders' => $yearTotalCancelledOrders,
            'total_orders' => $yearTotalOrders,
            'total_items_sold' => $yearTotalItemsSold,
            'avg_monthly_revenue' => round($yearTotalRevenue / 12),
            'aov' => $yearTotalSuccessfulOrders > 0 ? round($yearTotalRevenue / $yearTotalSuccessfulOrders) : 0,
            'completion_rate' => $yearTotalOrders > 0 ? round(($yearTotalSuccessfulOrders / $yearTotalOrders) * 100, 1) : 0,
        ];

        // 4. SO SÁNH GIỮA CÁC NĂM (Year-over-Year)
        $yearlyComparison = [];
        foreach ($availableYears as $yr) {
            $yrOrders = Order::whereYear('created_at', $yr)->get(['id', 'total', 'status']);
            $yrSuccess = $yrOrders->whereIn('status', ['paid', 'completed']);
            $yrRev = (float) $yrSuccess->sum('total');
            $yrSucc = $yrSuccess->count();
            $yrTot = $yrOrders->count();
            $yrCanc = $yrOrders->where('status', 'cancelled')->count();
            $yrAov = $yrSucc > 0 ? round($yrRev / $yrSucc) : 0;
            $yrCompRate = $yrTot > 0 ? round(($yrSucc / $yrTot) * 100, 1) : 0;

            $yearlyComparison[$yr] = [
                'year' => $yr,
                'revenue' => $yrRev,
                'successful_orders' => $yrSucc,
                'cancelled_orders' => $yrCanc,
                'total_orders' => $yrTot,
                'aov' => $yrAov,
                'completion_rate' => $yrCompRate,
            ];
        }

        // 5. THỐNG KÊ THEO TỪNG NGÀY NẾU CHỌN XEM MỘT THÁNG CỤ THỂ
        $dailyStats = [];
        if ($selectedMonth !== 'all' && is_numeric($selectedMonth)) {
            $monthNum = (int) $selectedMonth;
            $daysInMonth = Carbon::create($selectedYear, $monthNum, 1)->daysInMonth;
            $monthOrdersForDays = Order::whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $monthNum)
                ->get(['id', 'total', 'status', 'created_at']);

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dayOrders = $monthOrdersForDays->filter(fn ($o) => $o->created_at->day === $d);
                $daySuccess = $dayOrders->whereIn('status', ['paid', 'completed']);
                $dailyStats[] = [
                    'day' => str_pad($d, 2, '0', STR_PAD_LEFT) . '/' . str_pad($monthNum, 2, '0', STR_PAD_LEFT),
                    'revenue' => (float) $daySuccess->sum('total'),
                    'orders_count' => $dayOrders->count(),
                    'successful_orders' => $daySuccess->count(),
                ];
            }
        }

        // 6. DOANH THU THEO DANH MỤC
        $revenueByCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('orders.status', ['paid', 'completed'])
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('orders.created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('orders.created_at', '<=', $to))
            ->when($filters['status'] ?? null, fn ($query, $st) => $query->where('orders.status', $st))
            ->when($filters['payment_method'] ?? null, function ($q, $pm) {
                if ($pm === 'COD') $q->where('orders.payment_method', 'COD');
                elseif ($pm === 'ONLINE') $q->where('orders.payment_method', '!=', 'COD');
            })
            ->select('categories.id', 'categories.name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // 7. TOP 10 SẢN PHẨM BÁN CHẠY NHẤT
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'completed'])
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('orders.created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('orders.created_at', '<=', $to))
            ->when($filters['status'] ?? null, fn ($query, $st) => $query->where('orders.status', $st))
            ->when($filters['payment_method'] ?? null, function ($q, $pm) {
                if ($pm === 'COD') $q->where('orders.payment_method', 'COD');
                elseif ($pm === 'ONLINE') $q->where('orders.payment_method', '!=', 'COD');
            })
            ->select(
                'products.id',
                'products.name',
                'products.image',
                'products.product_code',
                'products.quantity as stock',
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as sold_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.image', 'products.product_code', 'products.quantity', 'categories.name')
            ->orderByDesc('sold_quantity')
            ->take(10)
            ->get();

        // 8. CẢNH BÁO TỒN KHO THẤP (<= 10 SẢN PHẨM)
        $lowStockProducts = Product::with('category')
            ->where('quantity', '<=', 10)
            ->orderBy('quantity', 'asc')
            ->take(10)
            ->get();

        // 9. TOP KHÁCH HÀNG VIP
        $topCustomers = User::whereIn('role', ['customer', 'user'])->orWhereNull('role')
            ->withSum(['orders as successful_spend' => function ($orders) use ($filters) {
                $orders->whereIn('status', ['paid', 'completed'])
                    ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
                    ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
                    ->when($filters['payment_method'] ?? null, function ($q, $pm) {
                        if ($pm === 'COD') $q->where('payment_method', 'COD');
                        elseif ($pm === 'ONLINE') $q->where('payment_method', '!=', 'COD');
                    });
            }], 'total')
            ->withCount(['orders as successful_orders' => function ($orders) use ($filters) {
                $orders->whereIn('status', ['paid', 'completed'])
                    ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
                    ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
                    ->when($filters['payment_method'] ?? null, function ($q, $pm) {
                        if ($pm === 'COD') $q->where('payment_method', 'COD');
                        elseif ($pm === 'ONLINE') $q->where('payment_method', '!=', 'COD');
                    });
            }])
            ->orderByDesc('successful_spend')
            ->take(10)
            ->get();

        // 10. 10 ĐƠN HÀNG GẦN NHẤT
        $recentOrders = (clone $ordersQuery)->with('user')->latest()->take(10)->get();

        return view('admin.reports.index', compact(
            'filters',
            'availableYears',
            'selectedYear',
            'selectedMonth',
            'totalOrders',
            'totalRevenue',
            'successfulRevenue',
            'cancelledRevenue',
            'refundRevenue',
            'grossOrderValue',
            'successfulOrders',
            'cancelledOrders',
            'refundOrders',
            'activeOrders',
            'urgentOrders',
            'aov',
            'successRate',
            'cancelledRate',
            'refundRate',
            'cancelledOrRefundedCount',
            'cancelledOrRefundedRate',
            'totalUnitsSold',
            'successfulCodRevenue',
            'successfulCodCount',
            'successfulPayosRevenue',
            'successfulPayosCount',
            'codShareRate',
            'payosShareRate',
            'statusCounts',
            'totalCustomers',
            'customersWithOrders',
            'customerAccounts',
            'yearlyMonthlyStats',
            'yearlySummary',
            'yearlyComparison',
            'dailyStats',
            'revenueByCategory',
            'topProducts',
            'lowStockProducts',
            'topCustomers',
            'recentOrders'
        ));
    }

    public function export(Request $request)
    {
        $exportType = $request->input('type', 'orders');
        $from = $request->input('from');
        $to = $request->input('to');
        $status = $request->input('status');
        $year = (int) $request->input('year', now()->year);

        if ($exportType === 'monthly') {
            $yearOrdersRaw = Order::whereYear('created_at', $year)->get(['id', 'total', 'status', 'created_at']);
            $filename = "bao-cao-12-thang-nam-{$year}.csv";

            return response()->streamDownload(function () use ($yearOrdersRaw, $year) {
                $handle = fopen('php://output', 'w');
                // UTF-8 BOM
                fwrite($handle, "\xEF\xBB\xBF");
                fputcsv($handle, ["BÁO CÁO DOANH THU VÀ ĐƠN HÀNG THEO 12 THÁNG - NĂM {$year}"]);
                fputcsv($handle, ['Tháng', 'Doanh thu thực tế (VNĐ)', 'Số đơn thành công', 'Số đơn hủy', 'Tổng số đơn', 'Tỷ lệ thành công (%)', 'Giá trị TB/Đơn (VNĐ)']);

                $totRev = 0;
                $totSucc = 0;
                $totCanc = 0;
                $totAll = 0;

                for ($m = 1; $m <= 12; $m++) {
                    $monthOrders = $yearOrdersRaw->filter(fn ($o) => $o->created_at->month === $m);
                    $monthSuccess = $monthOrders->whereIn('status', ['paid', 'completed']);
                    $monthCancelled = $monthOrders->where('status', 'cancelled');

                    $rev = (float) $monthSuccess->sum('total');
                    $succ = $monthSuccess->count();
                    $canc = $monthCancelled->count();
                    $all = $monthOrders->count();
                    $rate = $all > 0 ? round(($succ / $all) * 100, 1) : 0;
                    $aov = $succ > 0 ? round($rev / $succ) : 0;

                    $totRev += $rev;
                    $totSucc += $succ;
                    $totCanc += $canc;
                    $totAll += $all;

                    fputcsv($handle, [
                        "Tháng {$m}",
                        number_format($rev, 0, ',', '.'),
                        $succ,
                        $canc,
                        $all,
                        $rate . '%',
                        number_format($aov, 0, ',', '.')
                    ]);
                }

                $totalRate = $totAll > 0 ? round(($totSucc / $totAll) * 100, 1) : 0;
                $totalAov = $totSucc > 0 ? round($totRev / $totSucc) : 0;
                fputcsv($handle, [
                    'TỔNG CỘNG CẢ NĂM',
                    number_format($totRev, 0, ',', '.'),
                    $totSucc,
                    $totCanc,
                    $totAll,
                    $totalRate . '%',
                    number_format($totalAov, 0, ',', '.')
                ]);

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        if ($exportType === 'products') {
            $topProducts = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereIn('orders.status', ['paid', 'completed'])
                ->when($from, fn ($q) => $q->whereDate('orders.created_at', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('orders.created_at', '<=', $to))
                ->select(
                    'products.id',
                    'products.product_code',
                    'products.name',
                    'categories.name as category_name',
                    'products.quantity as stock',
                    DB::raw('SUM(order_items.quantity) as sold_quantity'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
                )
                ->groupBy('products.id', 'products.product_code', 'products.name', 'categories.name', 'products.quantity')
                ->orderByDesc('sold_quantity')
                ->get();

            $filename = 'aloha-top-san-pham-' . now()->format('Y-m-d') . '.csv';

            return response()->streamDownload(function () use ($topProducts) {
                $handle = fopen('php://output', 'w');
                fwrite($handle, "\xEF\xBB\xBF");
                fputcsv($handle, ['Mã SP', 'Tên sản phẩm', 'Danh mục', 'Số lượng đã bán', 'Doanh thu (VNĐ)', 'Tồn kho hiện tại']);

                foreach ($topProducts as $item) {
                    fputcsv($handle, [
                        $item->product_code ?? ('#' . $item->id),
                        $item->name,
                        $item->category_name ?? 'N/A',
                        $item->sold_quantity,
                        number_format($item->revenue, 0, ',', '.'),
                        $item->stock
                    ]);
                }

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        // Mặc định: type=orders
        $ordersQuery = Order::with('user')->latest();
        if (!empty($from)) $ordersQuery->whereDate('created_at', '>=', $from);
        if (!empty($to)) $ordersQuery->whereDate('created_at', '<=', $to);
        if (!empty($status)) $ordersQuery->where('status', $status);

        $orders = $ordersQuery->get();
        $filename = 'aloha-beauty-don-hang-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Mã đơn', 'Khách hàng', 'Số điện thoại', 'Địa chỉ', 'Giá trị đơn (VNĐ)', 'Phí vận chuyển', 'Thanh toán', 'Trạng thái', 'Ngày tạo']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    '#' . $order->id,
                    $order->customer_name ?: ($order->user->name ?? 'Khách'),
                    $order->customer_phone ?: ($order->user->phone ?? 'N/A'),
                    $order->customer_address ?? 'N/A',
                    $order->total,
                    $order->shipping_fee ?? 0,
                    $order->payment_method ?? 'COD',
                    $order->status,
                    $order->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}