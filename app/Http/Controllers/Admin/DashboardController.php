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
        // 1. Các thông số Đơn hàng & Doanh thu
        $totalRevenue = Order::where('status', 'paid')->sum('total');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'processing')->count();
        
        $countProcessing = $pendingOrders;
        $countPaid = Order::where('status', 'paid')->count();
        $countCancelled = Order::where('status', 'cancelled')->count();

        // 2. Các thông số Khách hàng, Sản phẩm, Danh mục
        $totalCustomers = User::where('role', 'user')->count(); 
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $todayRevenue = Order::where('status', 'paid')->whereDate('created_at', today())->sum('total');
        $todayOrders = Order::whereDate('created_at', today())->count();
        $recentOrders = Order::with('user')->latest()->take(6)->get();
        $lowStockProducts = Product::where('quantity', '<=', 10)->orderBy('quantity')->take(6)->get();
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as sold_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold_quantity')
            ->take(5)
            ->get();

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
            'countCancelled', 'years', 'todayRevenue', 'todayOrders',
            'recentOrders', 'lowStockProducts', 'topProducts'
        ));
    }

    // HÀM XỬ LÝ DỮ LIỆU BIỂU ĐỒ (API AJAX)
    public function getChartData(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // Tính doanh thu 12 tháng
        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenue = Order::where('status', 'paid')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $i)
                ->sum('total');
            $revenueData[] = (int) $revenue;
        }

        // Tính tỉ trọng phương thức thanh toán
        $codCount = Order::where('payment_method', 'COD')->whereYear('created_at', $year)->count();
        $payosCount = Order::where('payment_method', 'PAYOS')->whereYear('created_at', $year)->count();

        return response()->json([
            'revenue' => $revenueData,
            'payments' => [$codCount, $payosCount]
        ]);
    }
}