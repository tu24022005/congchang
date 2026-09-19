<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'status' => 'nullable|in:processing,confirmed,packing,shipping,paid,completed,cancelled,refund_pending,refunded',
        ]);

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

        // 1. Các chỉ số tổng quan theo bộ lọc
        $totalOrders = (clone $ordersQuery)->count();
        $totalCustomers = User::where('role', '!=', 'admin')->count();

        $totalRevenue = (clone $ordersQuery)->whereIn('status', ['paid', 'completed'])->sum('total');
        $successfulRevenue = $totalRevenue;
        $cancelledRevenue = (clone $ordersQuery)->where('status', 'cancelled')->sum('total');
        $grossOrderValue = (clone $ordersQuery)->sum('total');
        $successfulOrders = (clone $ordersQuery)->whereIn('status', ['paid', 'completed'])->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', 'cancelled')->count();
        $activeOrders = (clone $ordersQuery)->whereNotIn('status', ['paid', 'completed', 'cancelled'])->count();
        $successfulCodRevenue = (clone $ordersQuery)->whereIn('status', ['paid', 'completed'])->where('payment_method', 'COD')->sum('total');
        $successfulPayosRevenue = (clone $ordersQuery)->whereIn('status', ['paid', 'completed'])->where('payment_method', '!=', 'COD')->sum('total');
        $pendingOrders = (clone $ordersQuery)->whereIn('status', ['processing', 'confirmed', 'packing'])->count();

        $statusCounts = (clone $ordersQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        $customerAccounts = User::where('role', '!=', 'admin')
            ->withCount('orders')
            ->latest()
            ->take(10)
            ->get(['id', 'name', 'email', 'created_at']);

        // 2. Thống kê doanh thu theo danh mục sản phẩm
        $revenueByCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('orders.status', ['paid', 'completed'])
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('orders.created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('orders.created_at', '<=', $to))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('orders.status', $status))
            ->select('categories.name', DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'))
            ->groupBy('categories.name')
            ->get();

        // 3. Lấy 5 đơn hàng mới nhất để hiển thị nhanh
        $recentOrders = (clone $ordersQuery)->latest()->take(5)->get();
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'completed'])
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('orders.created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('orders.created_at', '<=', $to))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('orders.status', $status))
            ->select('products.name', DB::raw('SUM(order_items.quantity) as sold_quantity'), DB::raw('SUM(order_items.quantity * order_items.price) as revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold_quantity')
            ->take(5)
            ->get();

        return view('admin.reports.index', compact(
            'totalOrders', 
            'totalCustomers', 
            'totalRevenue', 
            'successfulRevenue',
            'cancelledRevenue',
            'grossOrderValue',
            'successfulOrders',
            'cancelledOrders',
            'activeOrders',
            'successfulCodRevenue',
            'successfulPayosRevenue',
            'pendingOrders', 
            'revenueByCategory',
            'recentOrders',
            'topProducts',
            'statusCounts',
            'customerAccounts',
            'filters'
        ));
    }

    public function export(Request $request)
    {
        $filters = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'status' => 'nullable|in:processing,confirmed,packing,shipping,paid,completed,cancelled,refund_pending,refunded',
        ]);
        $ordersQuery = Order::with('user')->latest();
        if (!empty($filters['from'])) $ordersQuery->whereDate('created_at', '>=', $filters['from']);
        if (!empty($filters['to'])) $ordersQuery->whereDate('created_at', '<=', $filters['to']);
        if (!empty($filters['status'])) $ordersQuery->where('status', $filters['status']);

        $orders = $ordersQuery->get();
        $filename = 'aloha-beauty-orders-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Mã đơn', 'Khách hàng', 'Tổng tiền', 'Thanh toán', 'Trạng thái', 'Ngày tạo']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->id,
                    $order->customer_name ?: ($order->user->name ?? 'Khach'),
                    $order->total,
                    $order->payment_method,
                    $order->status,
                    $order->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}