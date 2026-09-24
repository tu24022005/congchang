<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
..        $revenueStatuses = ['paid', 'completed'];
        $totalRevenue = Order::whereIn('status', $revenueStatuses)->sum('total');
        $totalOrders = Order::count();
        $pendingOrders = Order::whereIn('status', ['processing', 'confirmed', 'packing'])->count();
        $countProcessing = $pendingOrders;
        $countPaid = Order::whereIn('status', ['paid', 'completed'])->count();
        $countCompleted = Order::where('status', 'completed')->count();
        $countCancelled = Order::whereIn('status', ['cancelled', 'refund_pending', 'refunded'])->count();

        // 2. Các thông số Khách hàng, Sản phẩm, Danh mục
        $totalCustomers = User::whereIn('role', ['customer', 'user'])->count(); 
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $todayRevenue = Order::whereIn('status', $revenueStatuses)->whereDate('created_at', today())->sum('total');
        $monthRevenue = Order::whereIn('status', $revenueStatuses)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total');
        $todayOrders = Order::whereDate('created_at', today())->count();
        $recentOrders = Order::with('user')->latest()->take(6)->get();
        $lowStockProducts = Product::with('category')->where('quantity', '<=', 10)->orderBy('quantity')->take(6)->get();
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as sold_quantity'))
            ->whereNotIn('orders.status', ['cancelled', 'refund_pending', 'refunded'])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold_quantity')
            ->take(5)
            ->get();
        $newCustomers = User::whereIn('role', ['customer', 'user'])->latest()->take(5)->get();

        // 3. Lấy dữ liệu năm cho bộ lọc biểu đồ mượt mà
        $years = Order::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
            
        if($years->isEmpty()) {
            $years = collect([Carbon::now()->year]);
        }

        // Gửi toàn bộ 10 biến này sang giao diện
        return view('admin.dashboard', compact(
            'totalRevenue', 'totalOrders', 'pendingOrders', 'totalCustomers', 
            'totalProducts', 'totalCategories', 'countProcessing', 'countPaid', 
            'countCancelled', 'countCompleted', 'years', 'todayRevenue', 'monthRevenue', 'todayOrders',
            'recentOrders', 'lowStockProducts', 'topProducts', 'newCustomers'
        ));
    }

    // HÀM XỬ LÝ DỮ LIỆU BIỂU ĐỒ (API AJAX)
    public function getChartData(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $revenueStatuses = ['paid', 'completed'];
        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenue = Order::whereIn('status', $revenueStatuses)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $i)
                ->sum('total');
            $revenueData[] = (int) $revenue;
        }

        // Tính tỉ trọng phương thức thanh toán
        $codCount = Order::where('payment_method', 'COD')->whereYear('created_at', $year)->count();
        $payosCount = Order::where('payment_method', 'PAYOS')->whereYear('created_at', $year)->count();
        $lastSevenDays = collect(range(6, 0))->map(function (int $daysAgo) use ($revenueStatuses) {
            $date = today()->subDays($daysAgo);
            return [
                'label' => $date->format('d/m'),
                'revenue' => (int) Order::whereIn('status', $revenueStatuses)->whereDate('created_at', $date)->sum('total'),
            ];
        });
        $statusCounts = collect([
            'Chờ xử lý' => Order::whereIn('status', ['processing', 'confirmed', 'packing'])->count(),
            'Đang giao' => Order::where('status', 'shipping')->count(),
            'Hoàn thành' => Order::where('status', 'completed')->count(),
            'Đã hủy' => Order::whereIn('status', ['cancelled', 'refund_pending', 'refunded'])->count(),
        ]);

        return response()->json([
            'revenue' => $revenueData,
            'payments' => [$codCount, $payosCount],
            'lastSevenDays' => $lastSevenDays->values(),
            'statuses' => [
                'labels' => $statusCounts->keys()->values(),
                'series' => $statusCounts->values(),
            ],
        ]);
    }
}