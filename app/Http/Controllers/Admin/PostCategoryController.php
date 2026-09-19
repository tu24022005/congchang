<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostCategoryController extends Controller
{
    public function index()
    {
        $categories = PostCategory::withCount('posts')->latest()->get();
        return view('admin.post-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        if (PostCategory::where('slug', $data['slug'])->exists()) {
            return back()->withInput()->withErrors(['name' => 'Tên danh mục đã tồn tại.']);
        }
        PostCategory::create($data);
        return back()->with('success', 'Đã tạo danh mục bài viết.');
    }

    public function update(Request $request, PostCategory $postCategory)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        if (PostCategory::where('slug', $data['slug'])->where('id', '!=', $postCategory->id)->exists()) {
            return back()->withInput()->withErrors(['name' => 'Tên danh mục đã tồn tại.']);
        }
        $postCategory->update($data);
        return back()->with('success', 'Đã cập nhật danh mục bài viết.');
    }

    public function destroy(PostCategory $postCategory)
    {
        $postCategory->delete();
        return back()->with('success', 'Đã xóa danh mục bài viết.');
    }
}
