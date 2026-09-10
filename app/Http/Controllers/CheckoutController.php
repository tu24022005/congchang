<?php 
namespace App\Http\Controllers; 
use Illuminate\Http\Request; 
use App\Models\Order; 
use App\Models\OrderItem; 
use App\Models\Product; // Đã thêm Model Product để gọi bảng sản phẩm
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 
use PayOS\PayOS; 

class CheckoutController extends Controller 
{ 
    public function index() { return view('cart.index'); } 

    public function process(Request $request) 
    { 
        $paymentMethod = $request->input('payment_method'); 
        $cart = session()->get('cart', []); 
        if (empty($cart)) return redirect()->route('cart.index')->with('error', 'Giỏ trống.'); 
        
        $paymentMethodNormalized = strtoupper($paymentMethod) === 'COD' ? 'COD' : 'online'; 
        
        try { 
            DB::beginTransaction();
            $totalAmount = collect($cart)->sum(fn($details) => $details['price'] * $details['quantity']); 
            
            // 1. Tạo đơn hàng và lưu thông tin
            $order = Order::create([ 
                'user_id' => Auth::id(), 
                'total' => $totalAmount, 
                'status' => 'processing', 
                'payment_method' => $paymentMethodNormalized, 
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
            ]); 
            
            // 2. Lưu chi tiết sản phẩm VÀ TRỪ TỒN KHO
            foreach ($cart as $id => $details) { 
                $productId = (int) ($details['product_id'] ?? explode(':', (string) $id)[0]);
                $variationId = $details['variation_id'] ?? (isset(explode(':', (string) $id)[1]) ? (int) explode(':', (string) $id)[1] : null);
                $variation = $variationId ? ProductVariation::where('id', $variationId)->where('product_id', $productId)->lockForUpdate()->first() : null;
                if ($variation) {
                    if ($variation->stock < $details['quantity']) throw new \RuntimeException('Biến thể trong giỏ vừa hết hàng.');
                    $variation->decrement('stock', $details['quantity']);
                } else {
                    $product = Product::whereKey($productId)->lockForUpdate()->first();
                    if (!$product || $product->quantity < $details['quantity']) throw new \RuntimeException('Sản phẩm trong giỏ vừa hết hàng.');
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
            
            session()->forget('cart'); 
            DB::commit(); 
            
            if ($paymentMethodNormalized === 'COD') { 
                return redirect()->route('orders.index')->with('success', 'Đặt hàng thành công.'); 
            } else { 
                $payOS = new PayOS(env('PAYOS_CLIENT_ID'), env('PAYOS_API_KEY'), env('PAYOS_CHECKSUM_KEY'));
                
                $data = [
                    "orderCode" => intval($order->id), 
                    "amount" => intval($totalAmount),
                    "description" => "Thanh toan don " . $order->id,
                    "returnUrl" => route('orders.index'), 
                    "cancelUrl" => route('cart.index')    
                ];
                
                $response = $payOS->createPaymentLink($data);
                return redirect($response['checkoutUrl']); 
            }
        } catch (\Exception $e) { 
            DB::rollBack(); 
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage()); 
        } 
    } 
}