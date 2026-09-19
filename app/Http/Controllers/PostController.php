<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::published()->with('category')->when(
            $request->filled('category'),
            fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $request->category))
        )->latest('published_at')->paginate(9)->withQueryString();
        $categories = PostCategory::withCount(['posts' => fn ($query) => $query->published()])->orderBy('name')->get();
        return view('posts.index', compact('posts', 'categories'));
    }

    public function show(string $slug)
    {
        $post = Post::published()->with(['category', 'author'])->where('slug', $slug)->firstOrFail();
        $relatedPosts = Post::published()->where('id', '!=', $post->id)->where('post_category_id', $post->post_category_id)->latest('published_at')->take(3)->get();
        return view('posts.show', compact('post', 'relatedPosts'));
    }
}
