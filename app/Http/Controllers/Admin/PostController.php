<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('category')->latest();
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('excerpt', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('status') && in_array($request->status, ['draft', 'published'], true)) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('post_category_id', $request->category);
        }

        $posts = $query->paginate(12)->withQueryString();
        $categories = PostCategory::withCount('posts')->orderBy('name')->get();
        $stats = [
            'total' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'drafts' => Post::where('status', 'draft')->count(),
        ];
        return view('admin.posts.index', compact('posts', 'categories', 'stats'));
    }

    public function create()
    {
        return view('admin.posts.create', ['categories' => PostCategory::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['user_id'] = Auth::id();
        $data['published_at'] = $data['status'] === 'published' ? ($data['published_at'] ?? now()) : null;
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }
        Post::create($data);
        return redirect()->route('admin.posts.index')->with('success', 'Đã tạo bài viết.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', ['post' => $post, 'categories' => PostCategory::orderBy('name')->get()]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validated($request);
        if ($data['title'] !== $post->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $post->id);
        }
        $data['published_at'] = $data['status'] === 'published' ? ($data['published_at'] ?? $post->published_at ?? now()) : null;
        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }
        $post->update($data);
        return redirect()->route('admin.posts.index')->with('success', 'Đã cập nhật bài viết.');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();
        return back()->with('success', 'Đã xóa bài viết.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'post_category_id' => ['nullable', 'exists:post_categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);
    }

    private function uniqueSlug(string $title, ?int $ignore = null): string
    {
        $base = Str::slug($title) ?: 'bai-viet';
        $slug = $base;
        $counter = 2;
        while (Post::where('slug', $slug)->when($ignore, fn ($query) => $query->where('id', '!=', $ignore))->exists()) {
            $slug = $base . '-' . $counter++;
        }
        return $slug;
    }
}
