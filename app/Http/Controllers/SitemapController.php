<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Sinh sitemap.xml phục vụ tìm kiếm và SEO, cache trong 1 giờ.
     */
    public function index(): Response
    {
        $xml = Cache::remember('sitemap_xml', 3600, function (): string {
            $urls = collect();

            // 1. Trang chủ và trang danh mục tĩnh
            $staticPages = [
                ['url' => route('welcome'), 'priority' => '1.0', 'changefreq' => 'daily', 'lastmod' => now()],
                ['url' => route('products.index'), 'priority' => '0.9', 'changefreq' => 'daily', 'lastmod' => now()],
                ['url' => route('posts.index'), 'priority' => '0.8', 'changefreq' => 'daily', 'lastmod' => now()],
                ['url' => route('pages.about'), 'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => null],
                ['url' => route('pages.contact'), 'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => null],
                ['url' => route('pages.policies'), 'priority' => '0.5', 'changefreq' => 'monthly', 'lastmod' => null],
                ['url' => route('pages.faq'), 'priority' => '0.6', 'changefreq' => 'weekly', 'lastmod' => null],
            ];

            foreach ($staticPages as $page) {
                $urls->push([
                    'loc' => $page['url'],
                    'lastmod' => $page['lastmod'] ? $page['lastmod']->toAtomString() : now()->startOfMonth()->toAtomString(),
                    'changefreq' => $page['changefreq'],
                    'priority' => $page['priority'],
                ]);
            }

            // 2. Danh mục sản phẩm
            Category::query()
                ->select(['id', 'updated_at'])
                ->get()
                ->each(function (Category $category) use ($urls): void {
                    $urls->push([
                        'loc' => route('categories.show', ['category' => $category->id]),
                        'lastmod' => $category->updated_at ? $category->updated_at->toAtomString() : now()->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ]);
                });

            // 3. Sản phẩm đang hiển thị công khai
            Product::query()
                ->select(['slug', 'updated_at'])
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->latest('updated_at')
                ->get()
                ->each(function (Product $product) use ($urls): void {
                    $urls->push([
                        'loc' => route('products.show', ['product' => $product->slug]),
                        'lastmod' => $product->updated_at ? $product->updated_at->toAtomString() : now()->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ]);
                });

            // 4. Bài viết blog đã xuất bản (published)
            Post::published()
                ->select(['slug', 'updated_at'])
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->latest('updated_at')
                ->get()
                ->each(function (Post $post) use ($urls): void {
                    $urls->push([
                        'loc' => route('posts.show', ['slug' => $post->slug]),
                        'lastmod' => $post->updated_at ? $post->updated_at->toAtomString() : now()->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ]);
                });

            return view('sitemap', compact('urls'))->render();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
