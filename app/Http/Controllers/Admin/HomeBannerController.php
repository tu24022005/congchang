<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeBannerController extends Controller
{
    public function index(): View
    {
        $banners = HomeBanner::orderBy('sort_order')->orderByDesc('id')->get();

        return view('admin.home-banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.home-banners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $banner = new HomeBanner($this->validated($request));
        $this->storeImage($request, $banner);
        $banner->save();

        return redirect()->route('admin.home-banners.index')->with('success', 'Đã thêm banner trang chủ.');
    }

    public function edit(HomeBanner $homeBanner): View
    {
        return view('admin.home-banners.edit', ['banner' => $homeBanner]);
    }

    public function update(Request $request, HomeBanner $homeBanner): RedirectResponse
    {
        $homeBanner->fill($this->validated($request));
        $this->storeImage($request, $homeBanner);
        $homeBanner->save();

        return redirect()->route('admin.home-banners.index')->with('success', 'Đã cập nhật banner trang chủ.');
    }

    public function destroy(HomeBanner $homeBanner): RedirectResponse
    {
        if ($homeBanner->image_path) {
            Storage::disk('public')->delete($homeBanner->image_path);
        }

        $homeBanner->delete();

        return back()->with('success', 'Đã xóa banner trang chủ.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'badge' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:2048', 'required_without:image'],
            'alt_text' => ['nullable', 'string', 'max:180'],
            'button_text' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function storeImage(Request $request, HomeBanner $banner): void
    {
        if (!$request->hasFile('image')) {
            return;
        }

        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->image_path = $request->file('image')->store('home-banners', 'public');
        $banner->image_url = null;
    }
}
