<?php 
namespace App\Http\Controllers\Admin; 
use App\Http\Controllers\Controller; 
use App\Models\Order; 
use App\Models\ProductVariation;
use App\Models\InventoryLog;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\LoyaltyPointService;
use App\Services\ActivityLogService;

class OrderController extends Controller 
{ 
    public function refundRequests()
    {
        $orders = Order::with('user')
            ->where('status', 'refund_pending')
            ->where('refund_status', 'requested')
            ->latest()
            ->paginate(15);

        return view('admin.orders.refund-requests', compact('orders'));
    }

    // Hiển thị tất cả đơn hàng cho admin quản lý 
    public function index(Request $request)
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'min_total' => ['nullable', 'numeric', 'min:0'],
            'max_total' => ['nullable', 'numeric', 'min:0', 'gte:min_total'],
            'payment_method' => ['nullable', 'in:COD,PAYOS'],
            'shipping_provider' => ['nullable', 'in:' . implode(',', array_keys(config('shop.shipping_providers', [])))],
            'shipping_zone' => ['nullable', 'in:inner_city,other_city,remote'],
        ]);

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

        $query
            ->when($request->filled('payment_method'), fn ($builder) => $builder->where('payment_method', $request->input('payment_method')))
            ->when($request->filled('shipping_provider'), fn ($builder) => $builder->where('shipping_provider', $request->input('shipping_provider')))
            ->when($request->filled('shipping_zone'), fn ($builder) => $builder->where('shipping_zone', $request->input('shipping_zone')))
            ->when($request->filled('date_from'), fn ($builder) => $builder->whereDate('created_at', '>=', $request->input('date_from')))
            ->when($request->filled('date_to'), fn ($builder) => $builder->whereDate('created_at', '<=', $request->input('date_to')))
            ->when($request->filled('min_total'), fn ($builder) => $builder->where('total', '>=', $request->input('min_total')))
            ->when($request->filled('max_total'), fn ($builder) => $builder->where('total', '<=', $request->input('max_total')));

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
            'status' => 'required|in:processing,confirmed,packing,shipping,paid,completed,cancelled,refund_pending',
            'shipping_provider' => ['nullable', 'string', 'in:' . implode(',', array_keys(config('shop.shipping_providers', [])))],
            'tracking_number' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9][A-Za-z0-9._-]*$/'],
            'shipping_fee' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'shipping_date' => ['nullable', 'date'],
        ], [
            'tracking_number.regex' => 'Mã vận đơn chỉ được chứa chữ, số, dấu chấm, gạch ngang hoặc gạch dưới.',
        ]);
        
        $order = Order::findOrFail($id); 
        $allowedTransitions = [
            'processing' => ['confirmed', 'cancelled'],
            'confirmed' => ['paid', 'packing', 'cancelled'],
            'paid' => ['packing', 'cancelled'],
            'packing' => ['shipping', 'cancelled'],
            'shipping' => ['completed'],
            'refund_pending' => [],
        ];

        if ($request->status !== $order->status && !in_array($request->status, $allowedTransitions[$order->status] ?? [], true)) {
            return back()->with('error', 'Không thể chuyển đơn hàng sang trạng thái này.');
        }

        if ($request->status === 'paid' && $order->payment_method === 'COD') {
            return back()->with('error', 'Đơn COD chỉ được ghi nhận thanh toán khi khách đã nhận hàng.');
        }

        if ($request->status === 'cancelled' && $order->payment_method !== 'COD' && $order->status === 'paid') {
            return back()->with('error', 'Đơn online đã thanh toán cần chuyển sang Chờ hoàn tiền.');
        }

        if ($request->status === 'refunded') {
            return back()->with('error', 'Vui lòng dùng chức năng duyệt và xác nhận hoàn tiền riêng.');
        }

        $beforeStatus = $order->status;
        DB::transaction(function () use ($order, $request) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($request->status === 'cancelled' && $lockedOrder->status !== 'cancelled') {
                $cancellationService = app(\App\Services\OrderCancellationService::class);
                $cancellationService->restoreStock($lockedOrder);
                $cancellationService->releaseVouchers($lockedOrder);
            }
            $updateData = ['status' => $request->status];
            if ($request->status === 'completed' && $lockedOrder->status !== 'completed') {
                $updateData['received_at'] = now();
            }
            if ($request->has('shipping_provider')) {
                $updateData['shipping_provider'] = $request->input('shipping_provider') ?: null;
            }
            if ($request->has('tracking_number')) {
                $updateData['tracking_number'] = $request->input('tracking_number') ?: null;
            }
            if ($request->has('shipping_date')) {
                $updateData['shipping_date'] = $request->input('shipping_date') ?: null;
            }
            if ($request->has('shipping_fee')) {
                $shippingFee = $request->filled('shipping_fee') ? (int) $request->input('shipping_fee') : 0;
                $totalBeforeShipping = max(0, (int) $lockedOrder->total - (int) ($lockedOrder->shipping_fee ?? 0));
                $updateData['shipping_fee'] = $shippingFee;
                $updateData['total'] = $totalBeforeShipping + $shippingFee;
            }
            $lockedOrder->update($updateData);

            // Chỉ nhân viên xử lý kho khi chuyển đơn sang giao hàng mới là người xuất kho.
            if ($request->status === 'shipping') {
                InventoryLog::where('reference_type', $lockedOrder->getMorphClass())
                    ->where('reference_id', $lockedOrder->id)
                    ->where('type', 'out')
                    ->update([
                        'user_id' => Auth::id(),
                        'reason' => 'Xuất kho theo đơn hàng',
                    ]);
            }
        });
        $order->refresh();
        app(\App\Services\OrderStatusNotificationService::class)->notify($order, $beforeStatus);
        ActivityLogService::record('order.status.updated', 'Đã cập nhật trạng thái đơn #' . $order->id . '.', $order, ['status' => $beforeStatus], ['status' => $order->status]);
        if ($order->status === 'completed') {
            app(LoyaltyPointService::class)->awardForCompletedOrder($order);
        }
        
        return redirect()->route('admin.orders.index') 
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công!'); 
    } 

    public function refund(Request $request, $id)
    {
        $validated = $request->validate([
            'refund_reference' => 'required|string|max:120',
            'refund_note' => 'nullable|string|max:1000',
        ], [
            'refund_reference.required' => 'Vui lòng nhập mã giao dịch chuyển khoản.',
        ]);

        $order = Order::findOrFail($id);
        try {
            DB::transaction(function () use ($id, $validated) {
                $lockedOrder = Order::whereKey($id)->lockForUpdate()->firstOrFail();
                if ($lockedOrder->status !== 'refund_pending' || $lockedOrder->payment_method === 'COD' || !in_array($lockedOrder->refund_status, ['approved', null], true)) {
                    throw new \RuntimeException('Chỉ có thể hoàn tiền cho đơn online đang chờ hoàn.');
                }

                $lockedOrder->update([
                    'status' => 'refunded',
                    'refund_reference' => $validated['refund_reference'],
                    'refund_note' => $validated['refund_note'] ?? null,
                    'refunded_at' => now(),
                    'refunded_by' => Auth::id(),
                ]);
            });
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);
            return back()->with('error', 'Không thể xác nhận hoàn tiền lúc này. Vui lòng thử lại.');
        }

        $order->refresh();
        app(\App\Services\OrderStatusNotificationService::class)->notify($order, 'refund_pending');
        ActivityLogService::record('order.refunded', 'Đã ghi nhận hoàn tiền đơn #' . $order->id . '.', $order, ['status' => 'refund_pending'], ['status' => 'refunded'], ['refund_reference' => $validated['refund_reference']]);
        return back()->with('success', 'Đã ghi nhận hoàn tiền cho khách hàng #' . $order->id . '.');
    }

    public function approveRefund($id)
    {
        $order = Order::findOrFail($id);
        if ($order->status !== 'refund_pending' || $order->payment_method === 'COD' || $order->refund_status !== 'requested') {
            return back()->with('error', 'Yêu cầu hoàn tiền này không còn chờ duyệt.');
        }

        $order->update([
            'refund_status' => 'approved',
            'refund_reviewed_at' => now(),
            'refund_reviewed_by' => Auth::id(),
        ]);
        ActivityLogService::record('order.refund.approved', 'Đã duyệt yêu cầu hoàn tiền đơn #' . $order->id . '.', $order, ['refund_status' => 'requested'], ['refund_status' => 'approved']);

        return back()->with('success', 'Đã duyệt yêu cầu hoàn tiền. Có thể thực hiện chuyển khoản cho khách.');
    }

    public function rejectRefund(Request $request, $id)
    {
        $validated = $request->validate(['refund_rejection_note' => 'required|string|max:1000'], [
            'refund_rejection_note.required' => 'Vui lòng nhập lý do từ chối hoàn tiền.',
        ]);
        $order = Order::findOrFail($id);
        if ($order->status !== 'refund_pending' || $order->refund_status !== 'requested') {
            return back()->with('error', 'Yêu cầu hoàn tiền này không còn chờ duyệt.');
        }

        $order->update([
            'status' => 'cancelled',
            'refund_status' => 'rejected',
            'refund_rejection_note' => $validated['refund_rejection_note'],
            'refund_reviewed_at' => now(),
            'refund_reviewed_by' => Auth::id(),
        ]);
        ActivityLogService::record('order.refund.rejected', 'Đã từ chối yêu cầu hoàn tiền đơn #' . $order->id . '.', $order, ['refund_status' => 'requested'], ['refund_status' => 'rejected'], ['reason' => $validated['refund_rejection_note']]);

        return back()->with('success', 'Đã từ chối yêu cầu hoàn tiền của đơn #' . $order->id . '.');
    }
}