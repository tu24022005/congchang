<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\InventoryLog;
use App\Models\OrderVoucherUsage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\LoyaltyPointService;
use App\Services\CartService;

class OrderController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    // ==================================================
    // HIỂN THỊ DANH SÁCH ĐƠN HÀNG
    // ==================================================
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        // Payment status is changed only by the server-side payment webhook.
        $ordersQuery = Order::where('user_id', Auth::id())
            ->with('items.product', 'items.variation')
            ->latest();

        if ($search !== '') {
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $search . '%')
                    ->orWhereHas('items.product', function ($productQuery) use ($search) {
                        $productQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->input('status'));
        }

        $orders = $ordersQuery->get();
        $orderStats = [
            'all' => Order::where('user_id', Auth::id())->count(),
            'processing' => Order::where('user_id', Auth::id())->whereIn('status', ['processing', 'confirmed', 'packing', 'shipping'])->count(),
            'paid' => Order::where('user_id', Auth::id())->whereIn('status', ['paid', 'completed'])->count(),
            'cancelled' => Order::where('user_id', Auth::id())->whereIn('status', ['cancelled', 'refund_pending', 'refunded'])->count(),
        ];

        return view('orders.index', compact('orders', 'orderStats', 'search'));
    }

    public function refunds()
    {
        $orders = Order::where('user_id', Auth::id())
            ->whereIn('status', ['refund_pending', 'refunded'])
            ->latest('refunded_at')
            ->latest()
            ->get();

        $pendingTotal = $orders->where('status', 'refund_pending')->sum('total');
        $refundedTotal = $orders->where('status', 'refunded')->sum('total');

        return view('orders.refunds', compact('orders', 'pendingTotal', 'refundedTotal'));
    }
    // ==================================================
    // XỬ LÝ LƯU ĐƠN HÀNG (CÓ VOUCHER & PAYOS)
    // ==================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'min:2', 'max:120'],
            'customer_phone' => ['required', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/'],
            'customer_address' => ['required', 'string', 'min:10', 'max:500'],
            'payment_method' => 'required|in:COD,PAYOS',
        ], [
            'customer_name.required' => 'Vui lòng nhập tên người nhận.',
            'customer_name.min' => 'Tên người nhận phải có ít nhất 2 ký tự.',
            'customer_name.max' => 'Tên người nhận không được vượt quá 120 ký tự.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
            'customer_phone.regex' => 'Số điện thoại không đúng định dạng Việt Nam.',
            'customer_address.required' => 'Vui lòng nhập địa chỉ giao hàng.',
            'customer_address.min' => 'Địa chỉ giao hàng phải có ít nhất 10 ký tự.',
            'customer_address.max' => 'Địa chỉ giao hàng không được vượt quá 500 ký tự.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ]);

        $cart = $this->cartService->syncSession(Auth::user());
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống.');
        }

        try {
            DB::beginTransaction();

            // 1. Tính tổng tiền cơ bản
            $total = collect($cart)->sum(function ($details) {
                return $details['price'] * $details['quantity'];
            });

            // 2. Tính toán trừ tiền Voucher (nếu có) để lưu vào DB cho chuẩn
            $discount = 0;
            $voucher = null;
            $voucherIds = [];
            $voucherCodes = collect([session('voucher_discount.code'), session('voucher_shipping.code')])
                ->filter()
                ->unique()
                ->values();
            foreach ($voucherCodes as $voucherCode) {
                $voucher = Voucher::where('code', $voucherCode)->lockForUpdate()->first();

                if (!$voucher || ($voucher->user_id && $voucher->user_id !== Auth::id()) || !$voucher->isAvailable()) {
                    DB::rollBack();
                    session()->forget(['voucher', 'voucher_discount', 'voucher_shipping']);
                    return redirect()->route('cart.index')->with('error', 'Voucher vừa hết hạn hoặc hết lượt sử dụng.');
                }
                if ($total < $voucher->min_order_value) {
                    DB::rollBack();
                    session()->forget(['voucher', 'voucher_discount', 'voucher_shipping']);
                    return redirect()->route('cart.index')->with('error', 'Đơn hàng chưa đạt mức tối thiểu để dùng voucher này.');
                }

                if ($voucher->type !== 'free_shipping') {
                    $voucherDiscount = $voucher->type === 'fixed'
                        ? $voucher->value
                        : $total * ($voucher->value / 100);
                    $discount += min($voucherDiscount, $total - $discount);
                }
                $voucher->increment('used_count');
                $voucherIds[] = $voucher->id;
            }
            $serviceFee = config('shop.service_fee', 3000);
            $shippingFee = session()->has('voucher_shipping') ? 0 : $serviceFee;
            $finalTotal = $total - $discount + $shippingFee;

            // 3. Tạo đơn hàng và lưu tổng tiền đã giảm
            $order = new Order();
            $order->user_id = Auth::id();
            $order->total = $finalTotal; 
            $order->status = 'processing';
            $order->payment_method = $validated['payment_method'];
            
            $order->customer_name = $validated['customer_name'];
            $order->customer_phone = $validated['customer_phone'];
            $order->customer_address = $validated['customer_address'];
            $order->latitude = $request->input('latitude');
            $order->longitude = $request->input('longitude');
            
            $order->save(); 
            foreach ($voucherIds as $voucherId) {
                OrderVoucherUsage::create([
                    'order_id' => $order->id,
                    'voucher_id' => $voucherId,
                ]);
            }

            // 4. Lưu chi tiết từng sản phẩm
            foreach ($cart as $id => $details) {
                $productId = (int) ($details['product_id'] ?? explode(':', (string) $id)[0]);
                $variationId = $details['variation_id'] ?? (isset(explode(':', (string) $id)[1]) ? (int) explode(':', (string) $id)[1] : null);
                $variation = $variationId ? ProductVariation::where('id', $variationId)->where('product_id', $productId)->lockForUpdate()->first() : null;
                if ($variation) {
                    if ($variation->stock < $details['quantity']) {
                        throw new \RuntimeException('Biến thể ' . ($details['variation'] ?? '') . ' vừa hết hàng.');
                    }
                    $beforeStock = (int) $variation->stock;
                    $variation->decrement('stock', $details['quantity']);
                    $variation->refresh();
                    InventoryLog::record($variation, $beforeStock, (int) $variation->stock, 'Xuất kho theo đơn hàng', $order);
                } else {
                    $product = Product::whereKey($productId)->lockForUpdate()->first();
                    if (!$product || $product->quantity < $details['quantity']) {
                        throw new \RuntimeException('Sản phẩm trong giỏ vừa hết hàng.');
                    }
                    $product->decrement('quantity', $details['quantity']);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'variation_id' => $variation?->id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                ]);
            }

            // 5. Dọn dẹp session
            $this->cartService->clear(Auth::user());
            session()->forget(['voucher', 'voucher_discount', 'voucher_shipping']);
            
            DB::commit(); 
            
            // 6. KIỂM TRA PAYOS ĐỂ ĐẨY SANG TRANG QUÉT MÃ QR
            if ($order->payment_method === 'PAYOS') {
                $payOS = new \PayOS\PayOS(
                    env('PAYOS_CLIENT_ID'),
                    env('PAYOS_API_KEY'),
                    env('PAYOS_CHECKSUM_KEY')
                );

                $data = [
                    "orderCode" => intval($order->id), 
                    "amount" => intval($order->total), 
                    "description" => "Thanh toan don " . $order->id,
                    // Các URL này chỉ hiển thị kết quả; trạng thái thanh toán do webhook xác thực cập nhật.
                    "returnUrl" => route('orders.index'),
                    "cancelUrl" => route('orders.index')
                ];

                $response = $payOS->createPaymentLink($data);
                
                // Dừng luồng xử lý và chuyển thẳng sang cổng PayOS
                return redirect($response['checkoutUrl']);
            }

            // 7. Nếu là COD thì về thẳng trang Chi tiết
            return redirect()->route('orders.show', $order->id)->with('success', 'Đặt hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed.', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return redirect()->back()->with('error', 'Không thể tạo đơn hàng lúc này. Vui lòng thử lại.');
        }
    }

    // ==================================================
    // BẢNG ĐIỀU KHIỂN ĐƠN HÀNG DÀNH CHO ADMIN
    // ==================================================
    public function updateStatus(Request $request, Order $order)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            abort(403, 'Chỉ Admin mới có quyền thực hiện thao tác này.');
        }

        $requestedStatus = $request->status;
        $allowedTransitions = [
            'processing' => ['confirmed', 'cancelled'],
            'confirmed' => ['paid', 'packing', 'cancelled'],
            'paid' => ['packing', 'cancelled'],
            'packing' => ['shipping', 'cancelled'],
            'shipping' => ['completed'],
            'refund_pending' => ['refunded'],
        ];

        if ($requestedStatus !== $order->status && !in_array($requestedStatus, $allowedTransitions[$order->status] ?? [], true)) {
            return back()->with('error', 'Không thể chuyển đơn hàng sang trạng thái này.');
        }

        if ($requestedStatus === 'paid' && $order->payment_method === 'COD') {
            return back()->with('error', 'Đơn COD chỉ được ghi nhận thanh toán khi khách đã nhận hàng.');
        }

        if ($requestedStatus === 'refunded' && $order->payment_method === 'COD') {
            return back()->with('error', 'Đơn COD không có khoản thanh toán online cần hoàn.');
        }

        if ($requestedStatus === 'refunded' && $order->status !== 'refund_pending') {
            return back()->with('error', 'Chỉ có thể xác nhận hoàn tiền cho đơn đang chờ hoàn.');
        }

        if ($requestedStatus === 'cancelled' && $order->payment_method !== 'COD' && $order->status === 'paid') {
            return back()->with('error', 'Đơn online đã thanh toán cần chuyển sang Chờ hoàn tiền, không hủy trực tiếp.');
        }

        $order->status = $requestedStatus;
        $order->shipping_provider = $request->shipping_provider;
        $order->shipping_date = $request->shipping_date;
        $order->save();

        return back()->with('success', 'Đã cập nhật tiến độ vận chuyển cho đơn hàng!');
    }

    public function confirmReceived(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền cập nhật đơn hàng này.');
        }

        if ($order->status !== 'shipping') {
            return back()->with('error', 'Đơn hàng chưa ở trạng thái có thể xác nhận nhận hàng.');
        }

        $order->update(['status' => 'completed']);
        app(LoyaltyPointService::class)->awardForCompletedOrder($order);

        return back()->with('success', 'Đã xác nhận nhận hàng. Bạn có thể đánh giá sản phẩm ngay bây giờ.');
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này.');
        }

        if (!in_array($order->status, ['processing', 'confirmed', 'paid'], true)) {
            return back()->with('error', 'Đơn hàng chỉ có thể hủy trước khi shop bắt đầu đóng gói.');
        }

        $refundDetails = [];
        if ($order->payment_method !== 'COD' && $order->status === 'paid') {
            $refundDetails = $request->validate([
                'refund_bank_name' => 'required|string|max:120',
                'refund_account_number' => 'required|string|max:40',
                'refund_account_holder' => 'required|string|max:120',
            ], [
                'refund_bank_name.required' => 'Vui lòng nhập tên ngân hàng nhận hoàn tiền.',
                'refund_account_number.required' => 'Vui lòng nhập số tài khoản nhận hoàn tiền.',
                'refund_account_holder.required' => 'Vui lòng nhập tên chủ tài khoản.',
            ]);
        }

        try {
            DB::transaction(function () use ($order, $refundDetails) {
                $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
                if (!in_array($lockedOrder->status, ['processing', 'confirmed', 'paid'], true)) {
                    throw new \RuntimeException('Đơn hàng đã chuyển sang đóng gói hoặc trạng thái mới hơn.');
                }

                $this->restoreOrderStock($lockedOrder);
                app(\App\Services\OrderCancellationService::class)->releaseVouchers($lockedOrder);
                $isRefundRequest = $lockedOrder->payment_method !== 'COD' && $lockedOrder->status === 'paid';
                $nextStatus = $isRefundRequest
                    ? 'refund_pending'
                    : 'cancelled';
                $lockedOrder->update(array_merge($refundDetails, [
                    'status' => $nextStatus,
                    'refund_status' => $isRefundRequest ? 'requested' : null,
                ]));
                $order->status = $nextStatus;
            });
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);
            return back()->with('error', 'Không thể hủy đơn hàng lúc này. Vui lòng thử lại.');
        }

        $message = $order->payment_method !== 'COD' && $order->status === 'refund_pending'
            ? 'Đơn đã được hủy và chuyển sang trạng thái chờ hoàn tiền. Shop sẽ xác nhận sau khi chuyển khoản.'
            : 'Đã hủy đơn hàng thành công.';

        return redirect()->route('orders.show', $order)->with('success', $message);
    }

    private function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($lockedOrder->status === 'cancelled') {
                return;
            }
            $this->restoreOrderStock($lockedOrder);
            $lockedOrder->update(['status' => 'cancelled']);
        });
    }

    private function restoreOrderStock(Order $order): void
    {
        app(\App\Services\OrderCancellationService::class)->restoreStock($order);
    }

    // ==================================================
    // XEM CHI TIẾT ĐƠN HÀNG
    // ==================================================
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'BẠN KHÔNG CÓ QUYỀN TRUY CẬP ĐƠN HÀNG NÀY.');
        }

        $order->load('items.product', 'items.variation');

        return view('orders.show', compact('order'));
    }
}