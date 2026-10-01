<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Voucher;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $cart = $user ? $this->cartService->syncSession($user) : session()->get('cart', []);
        $total = collect($cart)->sum(fn (array $item) => $item['price'] * $item['quantity']);

        $savedForLater = [];
        if ($user) {
            foreach ($this->cartService->savedItems($user) as $item) {
                if ($item->product) {
                    $key = $item->variation ? $item->product_id . ':' . $item->variation->id : (string) $item->product_id;
                    $savedForLater[$key] = [
                        'name' => $item->product->name,
                        'price' => (float) $item->price,
                        'quantity' => $item->quantity,
                        'image' => $item->product->image,
                        'slug' => $item->product->slug,
                        'variation' => $item->variation ? collect([$item->variation->color, $item->variation->size_value ? $item->variation->size_value.$item->variation->size_unit : null, $item->variation->storage])->filter()->implode(' · ') : null,
                    ];
                }
            }
        } else {
            $savedForLater = session()->get('saved_for_later', []);
        }

        $vouchers = Voucher::where(function ($query) {
                $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            // Chỉ voucher toàn sàn được công khai trong modal.
            ->where('scope', 'platform')
            ->latest()
            ->get();

        $addresses = $user
            ? $user->addresses()->orderByDesc('is_default')->latest('id')->get()
            : collect();

        // Xóa trạng thái Mua ngay và bộ lọc chọn lẻ khi xem giỏ hàng thông thường
        session()->forget(['is_buy_now', 'buy_now_item', 'selected_cart_keys']);

        return view('cart.index', compact('cart', 'total', 'vouchers', 'addresses', 'savedForLater'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::with(['category', 'variations'])->findOrFail($id);
        $variation = $request->filled('variation_id')
            ? $product->variations->firstWhere('id', $request->integer('variation_id'))
            : null;

        if ($request->filled('variation_id') && !$variation) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Phiên bản sản phẩm không hợp lệ.'], 422);
            }
            return back()->with('error', 'Phiên bản sản phẩm không hợp lệ.');
        }

        $quantity = max(1, (int) $request->input('quantity', 1));
        $availableStock = $variation ? $variation->stock : $product->quantity;
        if ($availableStock < 1) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm này hiện đã hết hàng.'], 422);
            }
            return back()->with('error', 'Sản phẩm này hiện đã hết hàng.');
        }

        $user = Auth::user();
        $key = $variation ? $product->id . ':' . $variation->id : (string) $product->id;

        // XỬ LÝ "MUA NGAY" (BUY NOW): Đơn mua ngay chỉ chứa DUY NHẤT sản phẩm này, không gộp với giỏ hàng hiện có
        if ($request->boolean('buy_now')) {
            if ($quantity > $availableStock) {
                $msg = 'Số lượng đặt mua vượt quá tồn kho hiện có (' . $availableStock . ').';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg);
            }

            $currentPrice = $product->effectivePrice($variation);
            $originalPrice = $variation ? (float) $variation->price : (float) $product->price;
            $variationLabel = $variation
                ? collect([$variation->sku, $variation->color, $variation->size_value ? rtrim(rtrim($variation->size_value, '0'), '.') . $variation->size_unit : null, $variation->storage])->filter()->implode(' · ')
                : null;

            $buyNowData = [
                'key' => $key,
                'item' => [
                    'name' => $product->name,
                    'price' => $currentPrice,
                    'original_price' => $originalPrice,
                    'promotion_label' => $currentPrice < $originalPrice ? 'Flash sale' : null,
                    'quantity' => $quantity,
                    'image' => $product->image,
                    'category' => $product->category?->name ?? 'Chưa phân loại',
                    'product_id' => $product->id,
                    'variation_id' => $variation?->id,
                    'variation' => $variationLabel,
                ]
            ];

            session()->put('buy_now_item', $buyNowData);
            session()->put('is_buy_now', true);
            session()->forget(['voucher', 'voucher_discount', 'voucher_shipping', 'selected_cart_keys']);

            $redirectUrl = route('checkout', ['buy_now' => 1]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đang chuyển đến trang thanh toán...',
                    'redirect_url' => $redirectUrl,
                    'is_buy_now' => true,
                ]);
            }

            return redirect()->to($redirectUrl);
        }

        if ($user) {
            $cartModel = $this->cartService->forUser($user);
            $query = $cartModel->items()->where('product_id', $product->id);
            $query = $variation ? $query->where('variation_id', $variation->id) : $query->whereNull('variation_id');
            $item = $query->first();
            $currentQuantity = $item?->quantity ?? 0;

            if ($currentQuantity + $quantity > $availableStock) {
                $available = max(0, $availableStock - $currentQuantity);
                $msg = $available > 0
                    ? 'Bạn chỉ có thể thêm thêm ' . $available . ' sản phẩm vào giỏ.'
                    : 'Số lượng sản phẩm trong giỏ đã đạt mức tồn kho.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg);
            }

            if (!$item) {
                $item = new CartItem([
                    'product_id' => $product->id,
                    'variation_id' => $variation?->id,
                    'quantity' => $quantity,
                    'price' => $product->effectivePrice($variation),
                ]);
                $cartModel->items()->save($item);
            } else {
                $item->increment('quantity', $quantity);
            }

            $cartData = $this->cartService->syncSession($user);
        } else {
            $sessionCart = session()->get('cart', []);
            $currentQuantity = isset($sessionCart[$key]) ? (int) $sessionCart[$key]['quantity'] : 0;

            if ($currentQuantity + $quantity > $availableStock) {
                $available = max(0, $availableStock - $currentQuantity);
                $msg = $available > 0
                    ? 'Bạn chỉ có thể thêm thêm ' . $available . ' sản phẩm vào giỏ.'
                    : 'Số lượng sản phẩm trong giỏ đã đạt mức tồn kho.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg);
            }

            $currentPrice = $product->effectivePrice($variation);
            $originalPrice = $variation ? (float) $variation->price : (float) $product->price;
            $variationLabel = $variation
                ? collect([$variation->sku, $variation->color, $variation->size_value ? rtrim(rtrim($variation->size_value, '0'), '.') . $variation->size_unit : null, $variation->storage])->filter()->implode(' · ')
                : null;

            $sessionCart[$key] = [
                'name' => $product->name,
                'price' => $currentPrice,
                'original_price' => $originalPrice,
                'promotion_label' => $currentPrice < $originalPrice ? 'Flash sale' : null,
                'quantity' => $currentQuantity + $quantity,
                'image' => $product->image,
                'category' => $product->category?->name ?? 'Chưa phân loại',
                'product_id' => $product->id,
                'variation_id' => $variation?->id,
                'variation' => $variationLabel,
            ];
            session()->put('cart', $sessionCart);
            $cartData = $sessionCart;
        }

        $totalCount = collect($cartData)->sum('quantity');
        $subtotal = collect($cartData)->sum(fn (array $i) => $i['price'] * $i['quantity']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                'cart_count' => $totalCount,
                'subtotal' => $subtotal,
                'item' => $cartData[$key] ?? null,
                'cart' => $cartData,
                'redirect_url' => null,
            ]);
        }

        return back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    public function update(Request $request, $id)
    {
        $quantity = max(1, (int) $request->input('quantity', 1));
        $user = Auth::user();
        $parts = explode(':', (string) $id);
        $productId = (int) $parts[0];
        $variationId = isset($parts[1]) ? (int) $parts[1] : null;

        if ($user) {
            $cartModel = $this->cartService->forUser($user);
            $query = $cartModel->items()->where('product_id', $productId);
            $query = $variationId ? $query->where('variation_id', $variationId) : $query->whereNull('variation_id');
            $item = $query->first();

            if ($item) {
                $stock = $item->variation?->stock ?? $item->product?->quantity ?? 0;
                if ($quantity > $stock) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Số lượng vượt quá tồn kho hiện tại.'], 422);
                    }
                    return back()->with('error', 'Số lượng vượt quá tồn kho hiện tại.');
                }
                $item->update(['quantity' => $quantity]);
            }
            $cartData = $this->cartService->syncSession($user);
        } else {
            $sessionCart = session()->get('cart', []);
            if (isset($sessionCart[$id])) {
                $product = Product::find($productId);
                $variation = $variationId ? $product?->variations()->find($variationId) : null;
                $stock = $variation ? $variation->stock : ($product->quantity ?? 0);

                if ($quantity > $stock) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Số lượng vượt quá tồn kho hiện tại.'], 422);
                    }
                    return back()->with('error', 'Số lượng vượt quá tồn kho hiện tại.');
                }
                $sessionCart[$id]['quantity'] = $quantity;
                session()->put('cart', $sessionCart);
            }
            $cartData = session()->get('cart', []);
        }

        $totalCount = collect($cartData)->sum('quantity');
        $subtotal = collect($cartData)->sum(fn (array $i) => $i['price'] * $i['quantity']);
        $itemTotal = isset($cartData[$id]) ? $cartData[$id]['price'] * $cartData[$id]['quantity'] : 0;

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật số lượng thành công!',
                'cart_count' => $totalCount,
                'subtotal' => $subtotal,
                'item_total' => $itemTotal,
                'item_quantity' => $quantity,
                'cart' => $cartData,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng thành công!');
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        if ($user) {
            $parts = explode(':', (string) $id);
            $query = $this->cartService->forUser($user)->items()->where('product_id', (int) $parts[0]);
            $query = isset($parts[1]) ? $query->where('variation_id', (int) $parts[1]) : $query->whereNull('variation_id');
            $query->delete();
            $cartData = $this->cartService->syncSession($user);
        } else {
            $sessionCart = session()->get('cart', []);
            if (isset($sessionCart[$id])) {
                unset($sessionCart[$id]);
                session()->put('cart', $sessionCart);
            }
            $cartData = $sessionCart;
        }

        $totalCount = collect($cartData)->sum('quantity');
        $subtotal = collect($cartData)->sum(fn (array $i) => $i['price'] * $i['quantity']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                'cart_count' => $totalCount,
                'subtotal' => $subtotal,
                'cart' => $cartData,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function clear()
    {
        $user = Auth::user();
        if ($user) {
            $this->cartService->clear($user);
        } else {
            session()->forget('cart');
        }
        session()->forget(['voucher', 'voucher_discount', 'voucher_shipping']);

        return redirect()->route('cart.index')->with('success', 'Đã làm trống giỏ hàng.');
    }

    public function applyVoucher(Request $request)
    {
        $voucher = Voucher::where('code', strtoupper((string) $request->voucher_code))->first();
        if (!$voucher) {
            return back()->withInput()->with('error', 'Mã giảm giá không hợp lệ hoặc không tồn tại!');
        }
        if ($voucher->user_id && $voucher->user_id !== Auth::id()) {
            return back()->withInput()->with('error', 'Voucher này không thuộc tài khoản của bạn.');
        }
        if (!$voucher->isAvailable()) {
            return back()->withInput()->with('error', $voucher->expires_at && $voucher->expires_at->isBefore(today())
                ? 'Mã giảm giá đã hết hạn.'
                : 'Mã giảm giá đã hết lượt sử dụng.');
        }

        $cart = $this->resolveCurrentCart($request);
        $total = collect($cart)->sum(fn (array $details) => $details['price'] * $details['quantity']);
        if ($total < $voucher->min_order_value) {
            return back()->withInput()->with('error', 'Đơn hàng chưa đạt mức tối thiểu ' . number_format($voucher->min_order_value, 0, ',', '.') . 'đ để áp dụng mã này.');
        }

        session()->put('voucher', [
            'code' => $voucher->code,
            'type' => $voucher->type,
            'value' => $voucher->value ?? 0,
        ]);
        $slot = $voucher->type === 'free_shipping' ? 'shipping' : 'discount';
        session()->put('voucher_' . $slot, [
            'code' => $voucher->code,
            'type' => $voucher->type,
            'value' => $voucher->value ?? 0,
        ]);

        return back()->withInput()->with('success', $voucher->type === 'free_shipping'
            ? 'Đã áp dụng mã miễn phí vận chuyển!'
            : 'Áp dụng mã giảm giá thành công!');
    }

    public function applyVouchers(Request $request)
    {
        $codes = collect($request->input('voucher_codes', []))
            ->map(fn ($code) => strtoupper(trim((string) $code)))
            ->filter()
            ->unique()
            ->values();

        if ($codes->isEmpty() || $codes->count() > 2) {
            return back()->withInput()->with('error', 'Vui lòng chọn voucher hợp lệ.');
        }

        $cart = $this->resolveCurrentCart($request);
        $total = collect($cart)->sum(fn (array $details) => $details['price'] * $details['quantity']);
        $selected = [];

        foreach ($codes as $code) {
            $voucher = Voucher::where('code', $code)->first();
            if (!$voucher || ($voucher->user_id && $voucher->user_id !== Auth::id()) || !$voucher->isAvailable()) {
                return back()->withInput()->with('error', 'Một voucher đã chọn không còn hợp lệ.');
            }
            if ($total < $voucher->min_order_value) {
                return back()->withInput()->with('error', 'Đơn hàng chưa đạt mức tối thiểu của mã ' . $voucher->code . '.');
            }

            $slot = $voucher->type === 'free_shipping' ? 'shipping' : 'discount';
            if (isset($selected[$slot])) {
                return back()->with('error', 'Chỉ được chọn một mã giảm tiền và một mã free ship.');
            }
            $selected[$slot] = [
                'code' => $voucher->code,
                'type' => $voucher->type,
                'value' => $voucher->value ?? 0,
            ];
        }

        foreach (['discount', 'shipping'] as $slot) {
            if (isset($selected[$slot])) {
                session()->put('voucher_' . $slot, $selected[$slot]);
            } else {
                session()->forget('voucher_' . $slot);
            }
        }
        session()->forget('voucher');

        return back()->withInput()->with('success', 'Đã áp dụng đồng thời các voucher đã chọn.');
    }

    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Vui lòng đăng nhập để tiến hành thanh toán.');
        }

        $isBuyNow = false;
        $cart = $this->resolveCurrentCart($request, $isBuyNow);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $total = collect($cart)->sum(fn (array $item) => $item['price'] * $item['quantity']);
        $vouchers = Voucher::where(function ($query) {
                $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->where('scope', 'platform')
            ->latest()
            ->get();
        $addresses = Auth::user()->addresses()->orderByDesc('is_default')->latest('id')->get();

        return view('cart.checkout', compact('cart', 'total', 'vouchers', 'addresses', 'isBuyNow'));
    }

    protected function resolveCurrentCart(Request $request = null, bool &$isBuyNow = false): array
    {
        // Chỉ kích hoạt chế độ Mua ngay khi URL rõ ràng có ?buy_now=1
        // Nếu vào /checkout thông thường (từ mini cart, giỏ hàng), bỏ qua session is_buy_now cũ
        $hasBuyNowParam = $request && $request->query('buy_now') == '1';
        $isBuyNow = $hasBuyNowParam && session()->has('buy_now_item');

        if ($isBuyNow) {
            $buyNowData = session('buy_now_item');
            session(['is_buy_now' => true]);
            return [
                $buyNowData['key'] => $buyNowData['item'],
            ];
        }

        // Xoá trạng thái Mua ngay nếu không dùng đến
        session()->forget(['is_buy_now', 'buy_now_item']);
        $user = Auth::user();
        $cart = $user ? $this->cartService->syncSession($user) : session()->get('cart', []);

        if ($request && $request->filled('selected_items')) {
            $selectedKeys = explode(',', (string) $request->input('selected_items'));
            $cart = collect($cart)->filter(fn ($item, $key) => in_array((string)$key, $selectedKeys, true))->all();
            session()->put('selected_cart_keys', $selectedKeys);
        } elseif (session()->has('selected_cart_keys')) {
            $selectedKeys = session('selected_cart_keys');
            $cart = collect($cart)->filter(fn ($item, $key) => in_array((string)$key, $selectedKeys, true))->all();
        }

        return $cart;
    }

    public function saveForLater(Request $request, $key)
    {
        $user = Auth::user();
        $parts = explode(':', (string) $key);
        $productId = (int) $parts[0];
        $variationId = isset($parts[1]) ? (int) $parts[1] : null;

        if ($user) {
            $cartModel = $this->cartService->forUser($user);
            $query = $cartModel->items()->where('product_id', $productId);
            $query = $variationId ? $query->where('variation_id', $variationId) : $query->whereNull('variation_id');
            $item = $query->first();
            if ($item) {
                $item->update(['saved_for_later' => true]);
            }
            $this->cartService->syncSession($user);
        } else {
            $saved = session()->get('saved_for_later', []);
            $cart = session()->get('cart', []);
            if (isset($cart[$key])) {
                $saved[$key] = $cart[$key];
                unset($cart[$key]);
                session()->put('cart', $cart);
                session()->put('saved_for_later', $saved);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Đã lưu sản phẩm để mua sau.']);
        }

        return redirect()->route('cart.index')->with('success', 'Đã lưu sản phẩm để mua sau.');
    }

    public function moveToCart(Request $request, $key)
    {
        $user = Auth::user();
        $parts = explode(':', (string) $key);
        $productId = (int) $parts[0];
        $variationId = isset($parts[1]) ? (int) $parts[1] : null;

        if ($user) {
            $cartModel = $this->cartService->forUser($user);
            $query = $cartModel->items()->where('product_id', $productId);
            $query = $variationId ? $query->where('variation_id', $variationId) : $query->whereNull('variation_id');
            $item = $query->first();
            if ($item) {
                $item->update(['saved_for_later' => false]);
            }
            $this->cartService->syncSession($user);
        } else {
            $saved = session()->get('saved_for_later', []);
            $cart = session()->get('cart', []);
            if (isset($saved[$key])) {
                $cart[$key] = $saved[$key];
                unset($saved[$key]);
                session()->put('cart', $cart);
                session()->put('saved_for_later', $saved);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Đã chuyển sản phẩm lại vào giỏ hàng.']);
        }

        return redirect()->route('cart.index')->with('success', 'Đã chuyển sản phẩm lại vào giỏ hàng.');
    }
}
