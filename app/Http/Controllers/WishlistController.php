<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $products = $request->user()
            ->wishlistProducts()
            ->with('category')
            ->latest('wishlists.created_at')
            ->paginate(8);

        return view('wishlist.index', compact('products'));
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $wishlist = $request->user()->wishlistProducts();

        if ($wishlist->whereKey($product->id)->exists()) {
            $wishlist->detach($product->id);
            $message = 'Đã bỏ sản phẩm khỏi danh sách yêu thích.';
        } else {
            $wishlist->syncWithoutDetaching([$product->id]);
            $message = 'Đã lưu sản phẩm vào danh sách yêu thích.';
        }

        return back()->with('success', $message);
    }
}
