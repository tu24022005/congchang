<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ==================================================
    // HIỂN THỊ DANH SÁCH ĐƠN HÀNG VÀ XỬ LÝ KẾT QUẢ PAYOS
    // ==================================================
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        // 1. Kiểm tra xem có dữ liệu trả về từ cổng thanh toán PayOS hay không
        if ($request->has('orderCode') && $request->has('status')) {
            $orderId = $request->orderCode;
            $status = $request->status;
            $isCancelled = $request->cancel;

            // Tìm đơn hàng tương ứng trong Database
            $order = Order::find($orderId);

            // Kiểm tra đơn hàng tồn tại và thuộc về user đang đăng nhập
            if ($order && $order->user_id === Auth::id()) {
                // Nếu khách thanh toán thành công
                if ($status === 'PAID' && $isCancelled == 'false') {
                    $order->status = 'paid';
                    $order->save();
                    return redirect()->route('orders.index')->with('success', 'Thanh toán đơn hàng #' . $orderId . ' thành công qua PayOS!');
                } 
                // Nếu khách bấm nút Hủy giao dịch
                elseif ($status === 'CANCELLED' || $isCancelled == 'true') {
                    $order->status = 'cancelled';
                    $order->save();
                    return redirect()->route('orders.index')->with('error', 'Bạn đã hủy thanh toán cho đơn hàng #' . $orderId);
                }
            }
        }

        // 2. Truy vấn danh sách đơn hàng của User đang đăng nhập
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
            'cancelled' => Order::where('user_id', Auth::id())->where('status', 'cancelled')->count(),
        ];

        return view('orders.index', compact('orders', 'orderStats', 'search'));
    }
    // ==================================================
    // XỬ LÝ LƯU ĐƠN HÀNG (CÓ VOUCHER & PAYOS)
    // ==================================================
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        
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
            if (session()->has('voucher')) {
                $voucher = Voucher::where('code', session('voucher')['code'])->lockForUpdate()->first();

                if (!$voucher || !$voucher->isAvailable()) {
                    DB::rollBack();
                    session()->forget('voucher');
                    return redirect()->route('cart.index')->with('error', 'Voucher vừa hết hạn hoặc hết lượt sử dụng. Vui lòng chọn mã khác.');
                }

                if ($total < $voucher->min_order_value) {
                    DB::rollBack();
                    session()->forget('voucher');
                    return redirect()->route('cart.index')->with('error', 'Đơn hàng chưa đạt mức tối thiểu để dùng voucher này.');
                }

                if ($voucher->type === 'fixed') {
                    $discount = $voucher->value;
                } else {
                    $discount = $total * ($voucher->value / 100);
                }
                $discount = min($discount, $total); // Không cho giảm âm tiền
            }
            $serviceFee = config('shop.service_fee', 3000);
            $finalTotal = $total - $discount + $serviceFee;

            // 3. Tạo đơn hàng và lưu tổng tiền đã giảm
            $order = new Order();
            $order->user_id = Auth::id();
            $order->total = $finalTotal; 
            $order->status = 'processing';
            $order->payment_method = $request->input('payment_method', 'COD');
            
            $order->customer_name = $request->customer_name;
            $order->customer_phone = $request->customer_phone;
            $order->customer_address = $request->customer_address;
            $order->latitude = $request->input('latitude');
            $order->longitude = $request->input('longitude');
            
            $order->save(); 

            // 4. Lưu chi tiết từng sản phẩm
            foreach ($cart as $id => $details) {
                $productId = (int) ($details['product_id'] ?? explode(':', (string) $id)[0]);
                $variationId = $details['variation_id'] ?? (isset(explode(':', (string) $id)[1]) ? (int) explode(':', (string) $id)[1] : null);
                $variation = $variationId ? ProductVariation::where('id', $variationId)->where('product_id', $productId)->lockForUpdate()->first() : null;
                if ($variation) {
                    if ($variation->stock < $details['quantity']) {
                        throw new \RuntimeException('Biến thể ' . ($details['variation'] ?? '') . ' vừa hết hàng.');
                    }
                    $variation->decrement('stock', $details['quantity']);
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

            if ($voucher) {
                $voucher->increment('used_count');
            }

            // 5. Dọn dẹp session
            session()->forget('cart');
            session()->forget('voucher');
            
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
                    // Cấu hình URL để PayOS trả kết quả về đúng hàm index ở trên
                    "returnUrl" => route('orders.index') . '?orderCode=' . $order->id . '&status=PAID&cancel=false', 
                    "cancelUrl" => route('orders.index') . '?orderCode=' . $order->id . '&status=CANCELLED&cancel=true'
                ];

                $response = $payOS->createPaymentLink($data);
                
                // Dừng luồng xử lý và chuyển thẳng sang cổng PayOS
                return redirect($response['checkoutUrl']);
            }

            // 7. Nếu là COD thì về thẳng trang Chi tiết
            return redirect()->route('orders.show', $order->id)->with('success', 'Đặt hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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
        ];

        if ($requestedStatus !== $order->status && !in_array($requestedStatus, $allowedTransitions[$order->status] ?? [], true)) {
            return back()->with('error', 'Không thể chuyển đơn hàng sang trạng thái này.');
        }

        if ($requestedStatus === 'paid' && $order->payment_method === 'COD') {
            return back()->with('error', 'Đơn COD chỉ được ghi nhận thanh toán khi khách đã nhận hàng.');
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

        return back()->with('success', 'Đã xác nhận nhận hàng. Bạn có thể đánh giá sản phẩm ngay bây giờ.');
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