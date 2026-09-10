<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 

class ProductController extends Controller
{
    // ==========================================
    // KHU VỰC QUẢN LÝ DÀNH CHO ADMIN (Resource Methods)
    // ==========================================
    
    public function index(Request $request)
    {
        // Hàm này dành cho trang "Kho (Admin)" - Route: admin.products.index
        $products = Product::latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gallery.*' => 'nullable|image|max:2048', 
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|integer|exists:product_variations,id',
            'variations.*.sku' => 'nullable|string|max:100|distinct',
            'variations.*.color' => 'nullable|string|max:100',
            'variations.*.storage' => 'nullable|string|max:100',
            'variations.*.size_value' => 'nullable|numeric|min:0',
            'variations.*.size_unit' => 'nullable|string|max:20',
            'variations.*.price' => 'required_with:variations.*|numeric|min:0',
            'variations.*.stock' => 'required_with:variations.*|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validatedData['image'] = $imagePath;
        }

        $product = Product::create($validatedData);
        $this->syncVariations($product, $request->input('variations', []));

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('products/gallery', 'public');
                $product->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function show(Product $product)
    {
        // Hàm show này đang dành cho Admin (hiển thị ở views admin.products.show)
        $product->load(['category', 'images', 'variations', 'reviews.user']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gallery.*' => 'nullable|image|max:2048', 
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|integer|exists:product_variations,id',
            'variations.*.sku' => 'nullable|string|max:100|distinct',
            'variations.*.color' => 'nullable|string|max:100',
            'variations.*.storage' => 'nullable|string|max:100',
            'variations.*.size_value' => 'nullable|numeric|min:0',
            'variations.*.size_unit' => 'nullable|string|max:20',
            'variations.*.price' => 'required_with:variations.*|numeric|min:0',
            'variations.*.stock' => 'required_with:variations.*|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validatedData['image'] = $imagePath;
        }

        $product->update($validatedData);
        $this->syncVariations($product, $request->input('variations', []));

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('products/gallery', 'public');
                $product->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    // ==========================================
    // KHU VỰC DÀNH CHO KHÁCH HÀNG
    // ==========================================
    
    // Đã cấu hình lại: Xử lý hiển thị danh sách sản phẩm VÀ Tìm kiếm
    public function userIndex(Request $request)
    {
        $query = Product::with('category');
        $categories = Category::withCount('products')->orderBy('name')->get();

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        // Logic Tìm kiếm khi nhập vào thanh Search trên Header
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
        }

        // Phân 8 sản phẩm 1 trang và đưa lên giao diện
        $products = $query->latest()->paginate(8);
        $products->appends(['search' => $request->search]);

        return view('products.index', compact('products', 'categories'));
    }

    // ĐÃ TÍCH HỢP AI GỢI Ý VÀO HÀM NÀY CHO NGƯỜI DÙNG XEM
    public function show_normal(Product $product)
    {
        $product->load(['category', 'images', 'variations']); 

        // =========================================================
        // THUẬT TOÁN APRIORI - KHAI PHÁ DỮ LIỆU ĐƠN HÀNG (AI TỰ HỌC)
        // =========================================================
        
        $orderIds = DB::table('order_items')
            ->where('product_id', $product->id)
            ->pluck('order_id');

        $relatedProductIds = DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->where('product_id', '!=', $product->id) 
            ->select('product_id', DB::raw('count(*) as frequency'))
            ->groupBy('product_id')
            ->orderByDesc('frequency') 
            ->take(4) 
            ->pluck('product_id');

        if ($relatedProductIds->isNotEmpty()) {
            $recommendations = Product::whereIn('id', $relatedProductIds)
                ->orderByRaw("FIELD(id, " . $relatedProductIds->implode(',') . ")")
                ->get();
        } else {
            $recommendations = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->inRandomOrder()
                ->take(4)
                ->get();
        }

        return view('products.show', compact('product', 'recommendations'));
    }

    private function syncVariations(Product $product, array $variations): void
    {
        $keptIds = [];

        foreach ($variations as $variation) {
            if (blank($variation['sku'] ?? null) && blank($variation['color'] ?? null) && blank($variation['storage'] ?? null) && blank($variation['size_value'] ?? null)) {
                continue;
            }

            $attributes = [
                'sku' => filled($variation['sku'] ?? null) ? $variation['sku'] : null,
                'color' => filled($variation['color'] ?? null) ? $variation['color'] : null,
                'storage' => filled($variation['storage'] ?? null) ? $variation['storage'] : null,
                'size_value' => filled($variation['size_value'] ?? null) ? $variation['size_value'] : null,
                'size_unit' => filled($variation['size_unit'] ?? null) ? $variation['size_unit'] : null,
                'price' => $variation['price'] ?? $product->price,
                'stock' => $variation['stock'] ?? 0,
            ];

            if (!empty($variation['id'])) {
                $existing = $product->variations()->whereKey($variation['id'])->first();
                if ($existing) {
                    $existing->update($attributes);
                    $keptIds[] = $existing->id;
                    continue;
                }
            }

            $keptIds[] = $product->variations()->create($attributes)->id;
        }

        $product->variations()->whereNotIn('id', $keptIds ?: [0])->delete();
    }

    // HÀM TÌM KIẾM TRỰC TIẾP (LIVE SEARCH API)
    public function suggestions(Request $request)
    {
        $search = $request->get('query');
        if ($search == '') return response()->json([]);

        // Tìm tối đa 5 sản phẩm khớp với từ khóa
        $products = Product::where('name', 'LIKE', '%' . $search . '%')->take(5)->get();

        // Gắn thêm đường dẫn ảnh, giá tiền định dạng sẵn để JS dễ hiển thị
        foreach ($products as $p) {
            $p->image_url = $p->image ? asset('storage/' . $p->image) : null;
            $p->formatted_price = number_format($p->price, 0, ',', '.') . ' ₫';
            $p->detail_url = route('products.show', $p->id);
        }

        return response()->json($products);
    }

    // HÀM LƯU ĐÁNH GIÁ SẢN PHẨM (REVIEW)
    public function storeReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        /* 
        * Yêu cầu bạn phải có bảng 'reviews' trong Database. 
        * Lệnh tạo bảng: php artisan make:model Review -m
        */
        \DB::table('reviews')->insert([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Cảm ơn bạn! Đánh giá đã được ghi nhận.');
    }
}