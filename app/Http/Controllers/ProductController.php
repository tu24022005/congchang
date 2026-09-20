<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 
use App\Services\ActivityLogService;

class ProductController extends Controller
{
    // ==========================================
    // KHU VỰC QUẢN LÝ DÀNH CHO ADMIN (Resource Methods)
    // ==========================================
    
    public function index(Request $request)
    {
        // Hàm này dành cho trang "Kho (Admin)" - Route: admin.products.index
        $products = Product::with(['category', 'variations'])->latest()->paginate(10);
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
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gallery.*' => 'nullable|image|max:2048', 
            'variations' => 'required|array|min:1',
            'variations.*.id' => 'nullable|integer|exists:product_variations,id',
            'variations.*.sku' => 'nullable|string|max:100|distinct',
            'variations.*.color' => 'nullable|string|max:100',
            'variations.*.storage' => 'nullable|string|max:100',
            'variations.*.size_value' => 'nullable|numeric|min:0',
            'variations.*.size_unit' => 'nullable|string|max:20',
            'variations.*.price' => 'required|numeric|min:0',
            'variations.*.stock' => 'required|integer|min:0',
            'variations.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);
        $validatedData['price'] = (float) $request->input('variations.0.price');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validatedData['image'] = $imagePath;
        }

        $product = Product::create($validatedData);
        $this->syncVariations($product, $request->input('variations', []), $request->file('variations', []));
        $product->update(['quantity' => $product->variations()->sum('stock')]);

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
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gallery.*' => 'nullable|image|max:2048', 
            'variations' => 'required|array|min:1',
            'variations.*.id' => 'nullable|integer|exists:product_variations,id',
            'variations.*.sku' => 'nullable|string|max:100|distinct',
            'variations.*.color' => 'nullable|string|max:100',
            'variations.*.storage' => 'nullable|string|max:100',
            'variations.*.size_value' => 'nullable|numeric|min:0',
            'variations.*.size_unit' => 'nullable|string|max:20',
            'variations.*.price' => 'required|numeric|min:0',
            'variations.*.stock' => 'required|integer|min:0',
            'variations.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);
        $validatedData['price'] = (float) $request->input('variations.0.price');

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validatedData['image'] = $imagePath;
        }

        $product->update($validatedData);
        $this->syncVariations($product, $request->input('variations', []), $request->file('variations', []));
        $product->update(['quantity' => $product->variations()->sum('stock')]);

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
        foreach ($product->variations as $variation) {
            if ($variation->image && Storage::disk('public')->exists($variation->image)) {
                Storage::disk('public')->delete($variation->image);
            }
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    public function updateStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);
        $before = ['quantity' => $product->quantity];
        $product->update(['quantity' => $data['quantity']]);
        ActivityLogService::record('product.stock.updated', 'Đã cập nhật tồn kho sản phẩm ' . $product->name . '.', $product, $before, ['quantity' => $product->quantity]);

        return back()->with('success', 'Đã cập nhật tồn kho sản phẩm.');
    }

    public function updateVariationStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'variations' => 'required|array|min:1',
            'variations.*.id' => 'required|integer',
            'variations.*.stock' => 'required|integer|min:0',
            'variations.*.received' => 'nullable|integer|min:0',
        ]);

        $before = [];
        $after = [];
        DB::transaction(function () use ($data, $product, &$before, &$after) {
            foreach ($data['variations'] as $variationData) {
                $variation = $product->variations()->whereKey($variationData['id'])->lockForUpdate()->firstOrFail();
                $oldStock = (int) $variation->stock;
                $newStock = (int) $variationData['stock'] + (int) ($variationData['received'] ?? 0);
                $before[$variation->id] = ['stock' => $oldStock];
                $variation->update(['stock' => $newStock]);
                $after[$variation->id] = ['stock' => $variation->stock];
                InventoryLog::record($variation, $oldStock, $newStock, 'Cập nhật tồn kho thủ công');
            }

            $product->update(['quantity' => $product->variations()->sum('stock')]);
        });
        ActivityLogService::record('product.variation-stock.updated', 'Đã cập nhật tồn kho biến thể của ' . $product->name . '.', $product, $before, $after);

        return back()->with('success', 'Đã cập nhật tồn kho từng mã loại thành công.');
    }

    // ==========================================
    // KHU VỰC DÀNH CHO KHÁCH HÀNG
    // ==========================================
    
    // Đã cấu hình lại: Xử lý hiển thị danh sách sản phẩm VÀ Tìm kiếm
    public function userIndex(Request $request)
    {
        $validated = $request->validate([
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'gte:min_price'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'sort' => ['nullable', 'in:newest,price_asc,price_desc,rating_desc'],
        ]);

        $query = Product::with(['category', 'variations'])
            ->withAvg('reviews', 'rating')
            ->withMin('variations', 'price');
        $categories = Category::withCount('products')->orderBy('name')->get();

        if (!empty($validated['category'])) {
            $query->where('category_id', $validated['category']);
        }

        if ($request->filled('search')) {
            $searchTerm = trim((string) $request->input('search'));
            $query->where(function ($searchQuery) use ($searchTerm): void {
                $searchQuery->where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $priceExpression = 'COALESCE((SELECT MIN(pv.price) FROM product_variations pv WHERE pv.product_id = products.id), products.price)';
        if (array_key_exists('min_price', $validated)) {
            $query->whereRaw($priceExpression . ' >= ?', [$validated['min_price']]);
        }
        if (array_key_exists('max_price', $validated)) {
            $query->whereRaw($priceExpression . ' <= ?', [$validated['max_price']]);
        }
        if (!empty($validated['rating'])) {
            $query->whereRaw('(SELECT COALESCE(AVG(pr.rating), 0) FROM product_reviews pr WHERE pr.product_id = products.id) >= ?', [$validated['rating']]);
        }

        match ($validated['sort'] ?? 'newest') {
            'price_asc' => $query->orderByRaw($priceExpression . ' ASC'),
            'price_desc' => $query->orderByRaw($priceExpression . ' DESC'),
            'rating_desc' => $query->orderByDesc('reviews_avg_rating')->orderByDesc('products.created_at'),
            default => $query->latest('products.created_at'),
        };

        $products = $query->paginate(8)->withQueryString();
        $wishlistProductIds = $request->user()->wishlistProducts()->pluck('products.id');

        return view('products.index', compact('products', 'categories', 'wishlistProductIds'));
    }

    // ĐÃ TÍCH HỢP AI GỢI Ý VÀO HÀM NÀY CHO NGƯỜI DÙNG XEM
    public function show_normal(Request $request, Product $product)
    {
        $product->load(['category', 'images', 'variations']); 
        $isWishlisted = $request->user()->wishlistProducts()->whereKey($product->id)->exists();

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

        return view('products.show', compact('product', 'recommendations', 'isWishlisted'));
    }

    private function syncVariations(Product $product, array $variations, array $variationFiles = []): void
    {
        $keptIds = [];

        foreach ($variations as $thisIndex => $variation) {
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
                    $oldStock = (int) $existing->stock;
                    $file = $variationFiles[$thisIndex]['image'] ?? null;
                    if ($file) {
                        if ($existing->image && Storage::disk('public')->exists($existing->image)) {
                            Storage::disk('public')->delete($existing->image);
                        }
                        $attributes['image'] = $file->store('products/variations', 'public');
                    }
                    $existing->update($attributes);
                    InventoryLog::record($existing, $oldStock, (int) $existing->stock, 'Cập nhật sản phẩm');
                    $keptIds[] = $existing->id;
                    continue;
                }
            }

            $file = $variationFiles[$thisIndex]['image'] ?? null;
            if ($file) {
                $attributes['image'] = $file->store('products/variations', 'public');
            }
            $created = $product->variations()->create($attributes);
            InventoryLog::record($created, 0, (int) $created->stock, 'Tạo biến thể và nhập tồn ban đầu');
            $keptIds[] = $created->id;
        }

        $removed = $product->variations()->whereNotIn('id', $keptIds ?: [0])->get();
        foreach ($removed as $variation) {
            InventoryLog::record($variation, (int) $variation->stock, 0, 'Xóa biến thể');
            if ($variation->image && Storage::disk('public')->exists($variation->image)) {
                Storage::disk('public')->delete($variation->image);
            }
            $variation->delete();
        }
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
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $isCompletedPurchase = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.id', $validated['order_id'])
            ->where('orders.user_id', auth()->id())
            ->where('orders.status', 'completed')
            ->where('order_items.product_id', $validated['product_id'])
            ->exists();

        if (!$isCompletedPurchase) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá sản phẩm sau khi đơn hàng đã hoàn thành.');
        }

        /* 
        * Yêu cầu bạn phải có bảng 'reviews' trong Database. 
        * Lệnh tạo bảng: php artisan make:model Review -m
        */
        \DB::table('reviews')->insert([
            'user_id' => auth()->id(),
            'product_id' => $validated['product_id'],
            'order_id' => $validated['order_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Cảm ơn bạn! Đánh giá đã được ghi nhận.');
    }
}