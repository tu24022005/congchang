<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\HomeBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class WelcomeController extends Controller
{
/**
* Hiển thị trang chủ cửa hàng cho khách hàng
*/
public function index()
{
// Lấy danh sách sản phẩm mới nhất, kèm theo thông tin Danh mục và phân trang 6 sản phẩm/trang
$products = Product::with(['category', 'variations'])
->latest()
->paginate(6);
$categories = Category::withCount('products')->orderBy('name')->get();
$hotProductIds = DB::table('order_items')
->select('product_id', DB::raw('SUM(quantity) as sold_quantity'))
->groupBy('product_id')
->orderByDesc('sold_quantity')
->limit(8)
->pluck('product_id');
$flashSaleProducts = Product::with(['category', 'variations'])
    ->whereNotNull('flash_sale_price')
    ->whereNotNull('flash_sale_starts_at')
    ->whereNotNull('flash_sale_ends_at')
    ->where('flash_sale_starts_at', '<=', now())
    ->where('flash_sale_ends_at', '>', now())
    ->whereColumn('flash_sale_price', '<', 'price')
    ->orderBy('flash_sale_ends_at')
    ->limit(8)
    ->get();
$remainingSlots = max(0, 8 - $flashSaleProducts->count());
$hotProducts = $remainingSlots > 0 && $hotProductIds->isNotEmpty()
    ? Product::with(['category', 'variations'])
        ->whereIn('id', $hotProductIds)
        ->whereNotIn('id', $flashSaleProducts->pluck('id'))
        ->orderByRaw('FIELD(id, ' . $hotProductIds->implode(',') . ')')
        ->limit($remainingSlots)
        ->get()
    : collect();

if ($hotProducts->count() < $remainingSlots) {
    $fallbackProducts = Product::with(['category', 'variations'])
        ->whereNotIn('id', $flashSaleProducts->pluck('id')->merge($hotProducts->pluck('id')))
        ->latest()
        ->limit($remainingSlots - $hotProducts->count())
        ->get();
    $hotProducts = $hotProducts->concat($fallbackProducts);
}

$hotProducts = $flashSaleProducts->concat($hotProducts);
$banners = HomeBanner::where('is_active', true)->orderBy('sort_order')->orderByDesc('id')->get();
// Trả về view 'welcome' và truyền biến $products sang cho view
return view('welcome', compact('products', 'categories', 'hotProducts', 'banners'));
}
}