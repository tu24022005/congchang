<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAlertSubscription;
use Illuminate\Http\Request;

class StockAlertController extends Controller
{
    public function store(Request $request, Product $product)
    {
        if ($product->quantity > 0) {
            return back()->with('error', 'Sản phẩm hiện đang còn hàng.');
        }

        StockAlertSubscription::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Đã đăng ký báo khi sản phẩm có hàng.');
    }

    public function destroy(Request $request, Product $product)
    {
        StockAlertSubscription::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Đã hủy đăng ký báo có hàng.');
    }
}
