<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Voucher;
use App\Models\Category;
use App\Models\Product;

class VoucherController extends Controller
{
    // Hiển thị danh sách và form tạo mới
    public function index()
    {
        $vouchers = Voucher::with(['category', 'product'])->latest()->get();
        $categories = Category::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('admin.vouchers.index', compact('vouchers', 'categories', 'products'));
    }

    // Xử lý lưu mã vào Database
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_-]+$/', 'unique:vouchers,code'],
            'scope' => 'required|in:shop,platform',
            'applies_to' => 'required|in:all,category,product',
            'category_id' => 'nullable|required_if:applies_to,category|exists:categories,id',
            'product_id' => 'nullable|required_if:applies_to,product|exists:products,id',
            'type' => 'required|in:fixed,percent,free_shipping',
            'value' => ['required_unless:type,free_shipping', 'nullable', 'numeric', 'min:1', Rule::when($request->input('type') === 'percent', 'max:100')],
            'min_order_value' => 'required|integer|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after_or_equal:today',
        ]);

        Voucher::create([
            'code' => strtoupper($data['code']),
            'scope' => $data['scope'],
            'applies_to' => $data['applies_to'],
            'category_id' => $data['applies_to'] === 'category' ? $data['category_id'] : null,
            'product_id' => $data['applies_to'] === 'product' ? $data['product_id'] : null,
            'type' => $data['type'],
            'value' => $data['type'] === 'free_shipping' ? 0 : $data['value'],
            'min_order_value' => $data['min_order_value'],
            'usage_limit' => $data['usage_limit'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        return back()->with('success', 'Đã tạo mã giảm giá mới thành công!');
    }

    // Xóa mã
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('success', 'Đã xóa mã giảm giá!');
    }
}