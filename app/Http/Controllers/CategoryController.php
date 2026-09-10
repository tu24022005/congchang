<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // ==================================================
    // KHU VỰC ADMIN
    // ==================================================

    /**
     * 1. Hiển thị danh sách danh mục trong Admin
     */
    public function index()
    {
        $categories = Category::paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * 2. Hiển thị form thêm mới danh mục
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * 3. Xử lý lưu danh mục mới vào database
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validatedData);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Thêm danh mục thành công.');
    }

    /**
     * 4. Hiển thị chi tiết danh mục trong Admin
     */
    public function show(Category $category)
    {
        $category->load('products');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * 5. Hiển thị form chỉnh sửa danh mục
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * 6. Xử lý cập nhật danh mục
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            // Lưu ảnh mới
            $validatedData['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validatedData);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục thành công.');
    }

    /**
     * 7. Xóa danh mục
     */
    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Không thể xóa danh mục đang chứa sản phẩm.');
        }

        // Xóa file ảnh trong storage nếu có
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Xóa danh mục thành công.');
    }

    // ==================================================
    // KHU VỰC USER THƯỜNG
    // ==================================================

    /**
     * 8. Hiển thị danh sách danh mục cho User thường
     */
    public function indexNormal()
    {
        $categories = Category::paginate(10);
        return view('categories.index', compact('categories'));
    }

    /**
     * 9. Hiển thị chi tiết danh mục cho User thường
     */
    public function showNormal(Category $category)
    {
        return view('categories.show', compact('category'));
    }
}