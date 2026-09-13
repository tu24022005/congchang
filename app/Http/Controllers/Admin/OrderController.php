<?php 
namespace App\Http\Controllers\Admin; 
use App\Http\Controllers\Controller; 
use App\Models\Order; 
use Illuminate\Http\Request; 

class OrderController extends Controller 
{ 
    // Hiển thị tất cả đơn hàng cho admin quản lý 
    public function index(Request $request)
    {
        $query = Order::with('user', 'items.product')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('id', $search)
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('items.product', fn ($productQuery) => $productQuery->where('name', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->paginate(12)->withQueryString();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'processing')->count();
        $paidOrders = Order::where('status', 'paid')->count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $totalRevenue = Order::where('status', 'paid')->sum('total');

        return view('admin.orders.index', compact(
            'orders', 'totalOrders', 'pendingOrders', 'paidOrders', 'todayOrders', 'totalRevenue'
        ));
    } 

    // Cập nhật trạng thái đơn hàng (Admin tự sửa bằng tay nếu cần)
    public function updateStatus(Request $request, $id) 
    { 
        $request->validate([ 
            'status' => 'required|in:processing,confirmed,packing,shipping,paid,completed,cancelled',
        ]); 
        
        $order = Order::findOrFail($id); 
        $allowedTransitions = [
            'processing' => ['confirmed', 'cancelled'],
            'confirmed' => ['paid', 'packing', 'cancelled'],
            'paid' => ['packing', 'cancelled'],
            'packing' => ['shipping', 'cancelled'],
            'shipping' => ['completed'],
        ];

        if ($request->status !== $order->status && !in_array($request->status, $allowedTransitions[$order->status] ?? [], true)) {
            return back()->with('error', 'Không thể chuyển đơn hàng sang trạng thái này.');
        }

        if ($request->status === 'paid' && $order->payment_method === 'COD') {
            return back()->with('error', 'Đơn COD chỉ được ghi nhận thanh toán khi khách đã nhận hàng.');
        }

        $order->status = $request->status; 
        $order->save(); 
        
        return redirect()->route('admin.orders.index') 
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công!'); 
    } 
}