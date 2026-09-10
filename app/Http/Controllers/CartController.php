<?php

namespace App\Http\Controllers;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // ==================================================
    // 1. HIỂN THỊ GIỎ HÀNG (Tự động cập nhật Category chuẩn Lab 05B)[cite: 1]
    // ==================================================
    public function index()
    {
        $cart = session()->get('cart', []);
        
        // Cập nhật tên danh mục cho các sản phẩm trong giỏ hàng (nếu chưa có hoặc session cũ)[cite: 1]
        if (!empty($cart)) {
            $productIds = collect(array_keys($cart))->map(fn ($key) => (int) explode(':', (string) $key)[0])->unique();
            $products = Product::with('category')->whereIn('id', $productIds)->get()->keyBy('id');
            $updated = false;
            
            foreach ($cart as $id => &$item) {
                $productId = (int) explode(':', (string) $id)[0];
                if (isset($products[$productId])) {
                    $categoryName = $products[$productId]->category ? $products[$productId]->category->name : 'Chưa phân loại';
                    if (!isset($item['category']) || $item['category'] !== $categoryName) {
                        $item['category'] = $categoryName;
                        $updated = true;
                    }
                }
            }
            
            if ($updated) {
                session()->put('cart', $cart);
            }
        }
        
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return view('cart.index', compact('cart', 'total'));
    }

    // ==================================================
    // 2. THÊM SẢN PHẨM VÀO GIỎ[cite: 1]
    // ==================================================
    public function add(Request $request, $id)
    {
        $product = Product::with(['category', 'variations'])->findOrFail($id);
        $variation = $request->filled('variation_id')
            ? $product->variations->firstWhere('id', $request->integer('variation_id'))
            : null;
        if ($request->filled('variation_id') && !$variation) {
            return redirect()->back()->with('error', 'Phiên bản sản phẩm không hợp lệ.');
        }
        $cart = session()->get('cart', []);
        $quantity = max(1, (int)$request->input('quantity', 1));
        $availableStock = $variation ? $variation->stock : $product->quantity;
        $cartKey = $variation ? $product->id . ':' . $variation->id : (string) $product->id;

        if ($availableStock < 1) {
            return redirect()->back()->with('error', 'Sản phẩm này hiện đã hết hàng.');
        }

        $currentQuantity = $cart[$cartKey]['quantity'] ?? 0;
        if ($currentQuantity + $quantity > $availableStock) {
            $available = max(0, $availableStock - $currentQuantity);
            return redirect()->back()->with('error', $available > 0
                ? 'Bạn chỉ có thể thêm thêm ' . $available . ' sản phẩm vào giỏ.'
                : 'Số lượng sản phẩm trong giỏ đã đạt mức tồn kho.');
        }

        $categoryName = $product->category ? $product->category->name : 'Chưa phân loại';
        $variationLabel = $variation ? collect([$variation->sku, $variation->color, $variation->size_value ? rtrim(rtrim($variation->size_value, '0'), '.') . $variation->size_unit : null, $variation->storage])->filter()->implode(' · ') : null;
        
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
            $cart[$cartKey]['category'] = $categoryName;
        } else {
            $cart[$cartKey] = [
                'name' => $product->name,
                'price' => $variation ? $variation->price : $product->price,
                'quantity' => $quantity,
                'image' => $product->image,
                'category' => $categoryName,
                'product_id' => $product->id,
                'variation_id' => $variation?->id,
                'variation' => $variationLabel,
            ];
        }
        
        session()->put('cart', $cart);
        if ($request->boolean('buy_now')) {
            return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm. Bạn có thể kiểm tra đơn hàng ngay.');
        }

        return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    // ==================================================
    // 3. CẬP NHẬT SỐ LƯỢNG[cite: 1]
    // ==================================================
    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $quantity = max(1, (int)$request->input('quantity', 1));
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }
        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng thành công!');
    }

    // ==================================================
    // 4. XÓA SẢN PHẨM[cite: 1]
    // ==================================================
    public function destroy($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function clear()
    {
        session()->forget('cart');
        session()->forget('voucher');

        return redirect()->route('cart.index')->with('success', 'Đã làm trống giỏ hàng.');
    }
// ==========================================
    // XỬ LÝ ÁP DỤNG VOUCHER
    // ==========================================
    public function applyVoucher(Request $request)
    {
        $code = strtoupper($request->voucher_code);
        $voucher = Voucher::where('code', $code)->first();

        // 1. Kiểm tra mã có tồn tại không
        if (!$voucher) {
            return back()->withInput()->with('error', 'Mã giảm giá không hợp lệ hoặc không tồn tại!');
        }

        if (!$voucher->isAvailable()) {
            return back()->withInput()->with('error', $voucher->expires_at && $voucher->expires_at->isBefore(today())
                ? 'Mã giảm giá đã hết hạn.'
                : 'Mã giảm giá đã hết lượt sử dụng.');
        }

        // 2. Tính tổng tiền hiện tại của giỏ hàng
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(function ($details) {
            return $details['price'] * $details['quantity'];
        });

        // 3. Kiểm tra điều kiện đơn tối thiểu
        if ($total < $voucher->min_order_value) {
            return back()->withInput()->with('error', 'Đơn hàng chưa đạt mức tối thiểu ' . number_format($voucher->min_order_value, 0, ',', '.') . 'đ để áp dụng mã này.');
        }

        // 4. Lưu voucher vào session để mang ra giao diện tính toán
        session()->put('voucher', [
            'code' => $voucher->code,
            'type' => $voucher->type,
            'value' => $voucher->value,
        ]);

        return back()->withInput()->with('success', 'Áp dụng mã giảm giá thành công!');
    }
    // ==================================================
    // 5. TRANG CHECKOUT (Giữ lại từ form thông tin khách hàng cũ của bạn)
    // ==================================================
    public function checkout()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.checkout', compact('cart', 'total'));
    }
}