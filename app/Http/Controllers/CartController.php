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
        $cart = $this->cartService->syncSession(Auth::user());
        $total = collect($cart)->sum(fn (array $item) => $item['price'] * $item['quantity']);
        $vouchers = Voucher::where(function ($query) {
                $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            // Chỉ voucher toàn sàn được công khai trong modal.
            // Voucher shop riêng vẫn được tìm và áp dụng khi khách tự nhập code.
            ->where('scope', 'platform')
            ->latest()
            ->get();

        return view('cart.index', compact('cart', 'total', 'vouchers'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::with(['category', 'variations'])->findOrFail($id);
        $variation = $request->filled('variation_id')
            ? $product->variations->firstWhere('id', $request->integer('variation_id'))
            : null;

        if ($request->filled('variation_id') && !$variation) {
            return back()->with('error', 'Phiên bản sản phẩm không hợp lệ.');
        }

        $quantity = max(1, (int) $request->input('quantity', 1));
        $availableStock = $variation ? $variation->stock : $product->quantity;
        if ($availableStock < 1) {
            return back()->with('error', 'Sản phẩm này hiện đã hết hàng.');
        }

        $cart = $this->cartService->forUser(Auth::user());
        $query = $cart->items()->where('product_id', $product->id);
        $query = $variation ? $query->where('variation_id', $variation->id) : $query->whereNull('variation_id');
        $item = $query->first();
        $currentQuantity = $item?->quantity ?? 0;

        if ($currentQuantity + $quantity > $availableStock) {
            $available = max(0, $availableStock - $currentQuantity);
            return back()->with('error', $available > 0
                ? 'Bạn chỉ có thể thêm thêm ' . $available . ' sản phẩm vào giỏ.'
                : 'Số lượng sản phẩm trong giỏ đã đạt mức tồn kho.');
        }

        if (!$item) {
            $item = new CartItem([
                'product_id' => $product->id,
                'variation_id' => $variation?->id,
                'quantity' => $quantity,
                'price' => $product->effectivePrice($variation),
            ]);
            $cart->items()->save($item);
        } else {
            $item->increment('quantity', $quantity);
        }

        $this->cartService->syncSession(Auth::user());

        if ($request->boolean('buy_now')) {
            return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
        }

        return back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    public function update(Request $request, $id)
    {
        $cart = $this->cartService->forUser(Auth::user());
        $parts = explode(':', (string) $id);
        $query = $cart->items()->where('product_id', (int) $parts[0]);
        $query = isset($parts[1]) ? $query->where('variation_id', (int) $parts[1]) : $query->whereNull('variation_id');
        $item = $query->first();

        if ($item) {
            $quantity = max(1, (int) $request->input('quantity', 1));
            $stock = $item->variation?->stock ?? $item->product?->quantity ?? 0;
            if ($quantity > $stock) {
                return back()->with('error', 'Số lượng vượt quá tồn kho hiện tại.');
            }
            $item->update(['quantity' => $quantity]);
        }

        $this->cartService->syncSession(Auth::user());
        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng thành công!');
    }

    public function destroy($id)
    {
        $parts = explode(':', (string) $id);
        $query = $this->cartService->forUser(Auth::user())->items()->where('product_id', (int) $parts[0]);
        $query = isset($parts[1]) ? $query->where('variation_id', (int) $parts[1]) : $query->whereNull('variation_id');
        $query->delete();
        $this->cartService->syncSession(Auth::user());

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function clear()
    {
        $this->cartService->clear(Auth::user());
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

        $cart = $this->cartService->syncSession(Auth::user());
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

        $cart = $this->cartService->syncSession(Auth::user());
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

    public function checkout()
    {
        $cart = $this->cartService->syncSession(Auth::user());
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $total = collect($cart)->sum(fn (array $item) => $item['price'] * $item['quantity']);
        $addresses = Auth::user()->addresses()->orderByDesc('is_default')->latest('id')->get();
        return view('cart.checkout', compact('cart', 'total', 'addresses'));
    }
}
