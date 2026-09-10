<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
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
$products = Product::with('category')
->latest()
->paginate(6);
$categories = Category::withCount('products')->orderBy('name')->get();
$hotProductIds = DB::table('order_items')
->select('product_id', DB::raw('SUM(quantity) as sold_quantity'))
->groupBy('product_id')
->orderByDesc('sold_quantity')
->limit(8)
->pluck('product_id');
$hotProducts = $hotProductIds->isNotEmpty()
	? Product::with('category')->whereIn('id', $hotProductIds)->orderByRaw('FIELD(id, ' . $hotProductIds->implode(',') . ')')->get()
	: Product::with('category')->latest()->limit(8)->get();
// Trả về view 'welcome' và truyền biến $products sang cho view
return view('welcome', compact('products', 'categories', 'hotProducts'));
}
}