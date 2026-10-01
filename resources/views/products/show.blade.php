@extends('layouts.app')
@section('title', $product->name . ' - BeatyCare')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?: ($product->name . ' chính hãng tại BeatyCare')), 155))
@section('og_title', $product->name)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?: ($product->name . ' chính hãng tại BeatyCare')), 155))
@section('og_image', $product->image ? asset('storage/' . $product->image) : asset('images/placeholder.svg'))
@section('og_type', 'product')
@section('canonical', route('products.show', ['product' => $product->slug]))

@section('structured_data')
@php
    $galleryUrls = [];
    if ($product->image) {
        $galleryUrls[] = asset('storage/' . $product->image);
    }
    foreach ($product->images as $img) {
        if ($img->image_path) {
            $galleryUrls[] = asset('storage/' . $img->image_path);
        }
    }
    if (empty($galleryUrls)) {
        $galleryUrls[] = asset('images/placeholder.svg');
    }

    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'image' => array_values(array_unique($galleryUrls)),
        'description' => \Illuminate\Support\Str::limit(strip_tags($product->description ?: ($product->name . ' chính hãng tại BeatyCare')), 200),
        'sku' => $product->product_code ?: ($product->sku ?? ('BC-' . $product->id)),
        'mpn' => 'BC-' . $product->id,
        'brand' => [
            '@type' => 'Brand',
            'name' => $product->brand?->name ?? 'Aloha Beauty',
        ],
        'offers' => [
            '@type' => 'Offer',
            'url' => url()->current(),
            'priceCurrency' => 'VND',
            'price' => (float)$product->effectivePrice(),
            'priceValidUntil' => $product->flash_sale_ends_at ? $product->flash_sale_ends_at->toIso8601String() : now()->addMonths(3)->toIso8601String(),
            'availability' => $product->quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ],
    ];

    if ($product->reviews->isNotEmpty()) {
        $reviewCount = $product->reviews->count();
        $ratingAvg = round($product->reviews->avg('rating'), 1);
        $productSchema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => (string)$ratingAvg,
            'reviewCount' => (string)$reviewCount,
            'bestRating' => '5',
            'worstRating' => '1',
        ];

        $latestReviews = [];
        foreach ($product->reviews->take(5) as $rev) {
            $latestReviews[] = [
                '@type' => 'Review',
                'author' => [
                    '@type' => 'Person',
                    'name' => $rev->reviewer_name ?: ($rev->user?->name ?: 'Khách hàng BeatyCare'),
                ],
                'datePublished' => $rev->created_at->toDateString(),
                'reviewBody' => $rev->comment ?: ('Đánh giá ' . $rev->rating . ' sao'),
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => (string)$rev->rating,
                    'bestRating' => '5',
                    'worstRating' => '1',
                ],
            ];
        }
        $productSchema['review'] = $latestReviews;
    }

    $breadcrumbElements = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Trang chủ',
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Sản phẩm',
            'item' => route('products.index'),
        ],
    ];
    $pos = 3;
    if ($product->category) {
        $breadcrumbElements[] = [
            '@type' => 'ListItem',
            'position' => $pos++,
            'name' => $product->category->name,
            'item' => route('products.index', ['category' => $product->category->id]),
        ];
    }
    $breadcrumbElements[] = [
        '@type' => 'ListItem',
        'position' => $pos,
        'name' => $product->name,
        'item' => url()->current(),
    ];

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbElements,
    ];
@endphp
<template class="jsonld-template">
@json([$productSchema, $breadcrumbSchema], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
</template>
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset_v('css/views/products-show-blade-php.css') }}">

<div class="shop-detail container py-4 py-lg-5">
    <nav class="detail-breadcrumb mb-3" aria-label="Đường dẫn trang">
        <a href="{{ route('welcome') }}"><i class="bi bi-house-door me-1"></i>Trang chủ</a>
        <span class="breadcrumb-separator">&gt;</span>
        <a href="{{ route('products.index') }}">Sản phẩm</a>
        @if($product->category)
            <span class="breadcrumb-separator">&gt;</span>
            <a href="{{ route('products.index', ['category' => $product->category->id]) }}">{{ $product->category->name }}</a>
        @endif
        <span class="breadcrumb-separator">&gt;</span>
        <span class="breadcrumb-current" aria-current="page">{{ $product->name }}</span>
    </nav>

    <div class="detail-shell p-3 p-lg-5">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-6">
                <div class="detail-gallery">
                    <div class="product-image-zoom position-relative view-inline-1" onclick="window.openGalleryModal()">
                        @if($product->image)
                            <img id="detail-main-image" src="{{ asset('storage/' . $product->image) }}" class="detail-main-image" alt="{{ $product->name }}" fetchpriority="high" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                        @else
                            <div id="detail-main-image" class="detail-main-image d-grid place-items-center text-muted"><i class="bi bi-image fs-1"></i></div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 m-3 badge bg-dark bg-opacity-75 rounded-pill px-2.5 py-1.5 text-white view-inline-2">
                            <i class="bi bi-arrows-fullscreen me-1"></i>Phóng to ảnh
                        </span>
                    </div>
                    @php
                        $shownGalleryImages = $product->image ? [$product->image] : [];
                    @endphp
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @if($product->image)<img class="detail-thumb active" src="{{ asset('storage/' . $product->image) }}" data-image="{{ asset('storage/' . $product->image) }}" alt="Ảnh chính" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">@endif
                        @foreach($product->images as $image)
                            @if($image->image_path && !in_array($image->image_path, $shownGalleryImages, true))
                                <img class="detail-thumb" src="{{ asset('storage/' . $image->image_path) }}" data-image="{{ asset('storage/' . $image->image_path) }}" alt="Ảnh sản phẩm" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                                @php
                                    $shownGalleryImages[] = $image->image_path;
                                @endphp
                            @endif
                        @endforeach
                        @foreach($product->variations as $variation)
                            @if($variation->image && !in_array($variation->image, $shownGalleryImages, true))
                                <img class="detail-thumb" src="{{ asset('storage/' . $variation->image) }}" data-image="{{ asset('storage/' . $variation->image) }}" data-variation="{{ $variation->id }}" alt="Ảnh {{ $variation->sku ?: 'biến thể' }}" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                                @php
                                    $shownGalleryImages[] = $variation->image;
                                @endphp
                            @endif
                        @endforeach
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3 flex-wrap">
                        <span class="text-muted small fw-semibold"><i class="bi bi-share me-1"></i>Chia sẻ:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-circle view-inline-3" title="Chia sẻ Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://sp.zalo.me/plugins/share?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info rounded-circle view-inline-4" title="Chia sẻ Zalo">
                            Z
                        </a>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" id="copy-product-link"><i class="bi bi-link-45deg me-1"></i>Sao chép liên kết</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="detail-kicker mb-2">{{ $product->category?->name ?? 'BeatyCare 🌸' }}</div>
                <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                    <h1 class="detail-title fw-bold mb-0">{{ $product->name }}</h1>
                    @auth
                        @if(in_array(Auth::user()->role, ['admin', 'manager', 'warehouse_staff'], true))
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary rounded-pill px-3 flex-shrink-0"><i class="bi bi-pencil-square me-1"></i>Chỉnh sửa sản phẩm</a>
                        @else
                            <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                <button type="submit" class="btn {{ $isWishlisted ? 'btn-danger' : 'btn-outline-danger' }} rounded-circle" title="{{ $isWishlisted ? 'Bỏ khỏi yêu thích' : 'Lưu vào yêu thích' }}" aria-label="{{ $isWishlisted ? 'Bỏ khỏi yêu thích' : 'Lưu vào yêu thích' }}"><i class="bi bi-heart{{ $isWishlisted ? '-fill' : '' }}"></i></button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-danger rounded-circle flex-shrink-0" title="Đăng nhập để lưu yêu thích" aria-label="Đăng nhập để lưu yêu thích"><i class="bi bi-heart"></i></a>
                    @endauth
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4"><span class="detail-rating"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i></span><span class="text-muted small">Được lựa chọn bởi khách hàng BeatyCare</span></div>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4">@if($product->isFlashSaleActive())<span class="badge bg-danger"><i class="bi bi-lightning-charge-fill"></i> FLASH SALE</span><span class="detail-price">{{ number_format($product->effectivePrice(), 0, ',', '.') }} đ</span><span class="text-muted text-decoration-line-through">{{ number_format($product->price, 0, ',', '.') }} đ</span><small class="text-danger">Đến {{ $product->flash_sale_ends_at->format('d/m H:i') }}</small>@else<span class="detail-price">{{ number_format($product->price, 0, ',', '.') }} đ</span>@endif<span class="{{ $product->quantity > 0 ? 'detail-stock' : 'detail-stock out' }}"><i class="bi bi-{{ $product->quantity > 0 ? 'check-circle' : 'x-circle' }} me-1"></i>{{ $product->quantity > 0 ? 'Còn ' . $product->quantity . ' sản phẩm' : 'Hết hàng' }}</span></div>
                <p class="detail-copy mb-4">{{ $product->description ?: 'Một lựa chọn chăm sóc cá nhân dịu nhẹ, phù hợp cho chu trình làm đẹp hằng ngày.' }}</p>

                @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'manager', 'warehouse_staff'], true))
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Đây là trang xem sản phẩm. Sử dụng nút quản lý phía trên để cập nhật thông tin hoặc tồn kho.
                    </div>
                @else
                    @if($product->quantity > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" id="detail-cart-form">
                            @csrf
                            @if($product->variations->isNotEmpty())
                                <div class="variation-picker mb-3">
                                    <div class="variation-picker-row"><span class="variation-picker-label">Phân loại hàng</span><div class="variation-options">
                                        @foreach($product->variations as $index => $variation)
                                            @php $variationLabel = collect([$variation->color, $variation->size_value ? rtrim(rtrim($variation->size_value, '0'), '.') . $variation->size_unit : null, $variation->storage])->filter()->implode(' · '); @endphp
                                            <label class="variation-option">
                                                <input type="radio" name="variation_id" value="{{ $variation->id }}" data-price="{{ $product->effectivePrice($variation) }}" data-stock="{{ $variation->stock }}" data-image="{{ $variation->image ? asset('storage/' . $variation->image) : '' }}" data-label="{{ $variationLabel ?: 'Mặc định' }}" @checked($index === 0)>
                                                <span class="variation-code">{{ $variation->sku ?: 'Mã chưa đặt' }}</span><span class="variation-name">{{ $variationLabel ?: 'Mặc định' }}</span>
                                            </label>
                                        @endforeach
                                    </div></div>
                                </div>
                            @endif
                            <label class="form-label fw-bold text-dark mb-2">Số lượng</label>
                            <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                                <div class="quantity-picker"><button type="button" data-quantity-step="-1" aria-label="Giảm số lượng">−</button><input type="number" name="quantity" id="detail-quantity" value="1" min="1" max="{{ max(1, $product->quantity) }}" aria-label="Số lượng"><button type="button" data-quantity-step="1" aria-label="Tăng số lượng">+</button></div>
                                <small class="text-muted" id="detail-stock-note">Tối đa {{ $product->quantity }} sản phẩm</small>
                            </div>
                            <input type="hidden" name="buy_now" id="detail-buy-now-input" value="0">
                            <div class="d-flex gap-2">
                                <button type="submit" id="btn-add-to-cart" class="aloha-cart-button flex-grow-1"><i class="bi bi-bag-plus"></i><span>Thêm vào giỏ hàng</span></button>
                                <button type="submit" id="btn-buy-now" name="buy_now" value="1" class="btn btn-dark detail-buy flex-grow-1"><i class="bi bi-lightning-charge me-2"></i>Mua ngay</button>
                            </div>
                        </form>
                    @else
                        @auth
                            @if($isStockAlertSubscribed)
                                <form action="{{ route('products.stock-alert.destroy', $product) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-secondary detail-buy w-100"><i class="bi bi-bell-slash me-2"></i>Hủy báo khi có hàng</button>
                                </form>
                            @else
                                <form action="{{ route('products.stock-alert.store', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning detail-buy w-100"><i class="bi bi-bell me-2"></i>Báo khi có hàng</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-warning detail-buy w-100"><i class="bi bi-bell me-2"></i>Đăng nhập để được báo khi có hàng</a>
                        @endauth
                    @endif
                @endif

                <div class="detail-benefit row g-3">
                    <div class="col-sm-6"><div class="detail-benefit-item"><i class="bi bi-shield-check"></i><span><strong>Chính hãng</strong><br>Kiểm tra nguồn gốc rõ ràng</span></div></div>
                    <div class="col-sm-6"><div class="detail-benefit-item"><i class="bi bi-truck"></i><span><strong>Đóng gói cẩn thận</strong><br>Giao hàng an toàn đến bạn</span></div></div>
                </div>
            </div>
        </div>
    </div>

    <section class="product-information" aria-labelledby="product-information-title">
        <h2 id="product-information-title" class="product-information-title">Chi tiết sản phẩm</h2>
        <table class="product-detail-table">
            <tbody>
                <tr><th>Thương hiệu</th><td>{{ $product->brand?->name ?? 'Chưa cập nhật' }}</td></tr>
                <tr><th>Xuất xứ</th><td>{{ $product->origin ?: 'Chính hãng' }}</td></tr>
                <tr><th>Hạn sử dụng</th><td>{{ $product->expiry_info ?: 'Xem trên bao bì sản phẩm' }}</td></tr>
                @if(!empty($product->skin_types))
                    @php
                        $skinTypeNames = [
                            'da_dau' => 'Da dầu',
                            'da_kho' => 'Da khô',
                            'da_hon_hop' => 'Da hỗn hợp',
                            'da_nhay_cam' => 'Da nhạy cảm',
                            'moi_loai_da' => 'Mọi loại da',
                        ];
                    @endphp
                    <tr>
                        <th>Phù hợp loại da</th>
                        <td>
                            @foreach((array)$product->skin_types as $st)
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1 me-1 mb-1 fw-semibold">
                                    <i class="bi bi-check2 me-1"></i>{{ $skinTypeNames[$st] ?? $st }}
                                </span>
                            @endforeach
                        </td>
                    </tr>
                @endif
                <tr><th>Danh mục</th><td>{{ $product->category?->name ?? 'Mỹ phẩm & chăm sóc cá nhân' }}</td></tr>
                <tr><th>Mã sản phẩm</th><td><span class="product-detail-chip">{{ $product->product_code ?: 'Chưa có mã sản phẩm' }}</span></td></tr>
                <tr><th>Mã biến thể / SKU</th><td>@forelse($product->variations as $variation)<span class="product-detail-chip">{{ $variation->sku ?: 'Chưa có SKU' }}</span>@empty<span>Chưa có mã biến thể</span>@endforelse</td></tr>
                <tr><th>Màu / phân loại</th><td>@php $colors = $product->variations->pluck('color')->filter()->unique(); @endphp @forelse($colors as $color)<span class="product-detail-chip">{{ $color }}</span>@empty<span>Phân loại mặc định</span>@endforelse</td></tr>
                <tr><th>Khối lượng / dung tích</th><td>@php $sizes = $product->variations->map(fn ($variation) => $variation->size_value ? rtrim(rtrim($variation->size_value, '0'), '.') . $variation->size_unit : $variation->storage)->filter()->unique(); @endphp @forelse($sizes as $size)<span class="product-detail-chip">{{ $size }}</span>@empty<span>Chưa cập nhật</span>@endforelse</td></tr>
                <tr><th>Số phiên bản</th><td>{{ $product->variations->count() ?: 1 }} phiên bản</td></tr>
                <tr><th>Tình trạng</th><td>{{ $product->quantity > 0 ? 'Còn hàng' : 'Hết hàng' }}</td></tr>
            </tbody>
        </table>

        @if($product->ingredients)
            <div class="mt-4 pt-3 border-top">
                <h3 class="h6 fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-droplet-half text-primary"></i>Thành phần chi tiết (Ingredients)
                </h3>
                <div class="p-3 bg-light rounded-3 text-secondary small view-inline-5">
                    {{ $product->ingredients }}
                </div>
            </div>
        @endif

        @if($product->usage_instructions)
            <div class="mt-3 pt-3 border-top">
                <h3 class="h6 fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-magic text-danger"></i>Hướng dẫn sử dụng (Usage Instructions)
                </h3>
                <div class="p-3 bg-light rounded-3 text-secondary small view-inline-5">
                    {{ $product->usage_instructions }}
                </div>
            </div>
        @endif

        <div class="product-description-block">
            <h2 class="product-information-title">Mô tả sản phẩm</h2>
            <p class="mb-0 mt-3">{{ $product->description ?: 'Một lựa chọn chăm sóc cá nhân dịu nhẹ, phù hợp cho chu trình làm đẹp hằng ngày.' }}</p>
        </div>
    </section>

    @php
        $reviewCount = $product->reviews->count();
        $ratingAverage = $reviewCount ? round($product->reviews->avg('rating'), 1) : 0;
        $ratingCounts = collect(range(1, 5))->mapWithKeys(fn ($rating) => [$rating => $product->reviews->where('rating', $rating)->count()]);
        $mediaReviewCount = $product->reviews->filter(fn ($review) => !empty($review->media_paths))->count();
        $commentReviewCount = $product->reviews->filter(fn ($review) => filled($review->comment))->count();
    @endphp
    <section class="product-reviews" aria-labelledby="product-reviews-title">
        <h2 id="product-reviews-title" class="product-information-title">Đánh giá sản phẩm</h2>
        <div class="review-summary">
            <div class="review-score"><strong>{{ number_format($ratingAverage, 1) }}</strong><span>/5</span><div class="review-stars mt-2">★★★★★</div><small class="text-muted">{{ $reviewCount }} đánh giá</small></div>
            <div class="review-distribution" aria-label="Phân bố đánh giá">
                @for($rating = 5; $rating >= 1; $rating--)
                    @php $ratingPercent = $reviewCount ? round(($ratingCounts[$rating] / $reviewCount) * 100) : 0; @endphp
                    <div class="review-distribution-row"><span>{{ $rating }} <i class="bi bi-star-fill text-warning"></i></span><div class="review-distribution-bar"><span data-inline-width="{{ $ratingPercent }}" class="inline-dynamic-width"></span></div><strong>{{ $ratingPercent }}%</strong></div>
                @endfor
            </div>
            <div class="review-filters">
                <button type="button" class="review-filter active" data-review-filter="all">Tất cả</button>
                @for($rating = 5; $rating >= 1; $rating--)
                    <button type="button" class="review-filter" data-review-filter="{{ $rating }}">{{ $rating }} Sao ({{ $ratingCounts[$rating] }})</button>
                @endfor
                <button type="button" class="review-filter" data-review-filter="comment">Có Bình luận ({{ $commentReviewCount }})</button>
                <button type="button" class="review-filter" data-review-filter="media">Có Hình ảnh / Video ({{ $mediaReviewCount }})</button>
            </div>
        </div>

        <div id="review-list">
            @forelse($product->reviews as $review)
                <article class="review-item" data-review-rating="{{ $review->rating }}" data-review-media="{{ !empty($review->media_paths) ? '1' : '0' }}" data-review-comment="{{ filled($review->comment) ? '1' : '0' }}">
                    <div class="review-avatar">{{ strtoupper(substr($review->reviewer_name ?: $review->user?->name ?: 'A', 0, 1)) }}</div>
                    <div class="flex-grow-1">
                        <strong>{{ $review->reviewer_name ?: $review->user?->name ?: 'Khách hàng BeatyCare' }}</strong>
                        <div class="review-stars">{{ str_repeat('★', $review->rating) }}<span class="text-muted">{{ str_repeat('★', 5 - $review->rating) }}</span></div>
                        <div class="review-meta">{{ $review->created_at->format('d/m/Y H:i') }} @if($review->variant_label) | Phân loại hàng: {{ $review->variant_label }} @endif</div>
                        @if($review->is_verified_purchase)<div class="review-verified"><i class="bi bi-patch-check-fill me-1"></i>Đã mua hàng</div>@endif
                        <p class="review-comment">{{ $review->comment }}</p>
                        @if($review->media_paths)
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($review->media_paths as $path)
                                    @php $mediaUrl = asset('storage/' . ltrim($path, '/')); $isVideo = in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['mp4', 'mov', 'webm'], true); @endphp
                                    @if($isVideo)
                                        <div class="review-video-wrap"><video src="{{ $mediaUrl }}" controls preload="metadata" aria-label="Video đánh giá"></video><span><i class="bi bi-play-circle me-1"></i>Video</span></div>
                                    @else
                                        <button type="button" class="review-image-button" data-review-image="{{ $mediaUrl }}" aria-label="Xem ảnh đánh giá"><img src="{{ $mediaUrl }}" alt="Ảnh đánh giá" onerror="this.closest('.review-image-button').style.display='none'"></button>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2.5 btn-vote-helpful {{ $review->isHelpfulVotedBy(Auth::user()) ? 'active text-primary border-primary bg-primary-subtle' : '' }}" data-review-id="{{ $review->id }}" class="view-inline-6">
                                <i class="bi bi-hand-thumbs-up{{ $review->isHelpfulVotedBy(Auth::user()) ? '-fill' : '' }} me-1"></i>
                                Hữu ích (<span class="helpful-count">{{ $review->helpfulVotes->count() }}</span>)
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <p class="text-muted text-center py-4 mb-0">Chưa có đánh giá nào cho sản phẩm này.</p>
            @endforelse
        </div>

    </section>

    <!-- HỎI & ĐÁP SẢN PHẨM (PROMPT 3.5 & 3.7) -->
    <section class="product-questions mt-4 p-4 bg-white rounded-4 border shadow-sm" aria-labelledby="product-qa-title">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h3 id="product-qa-title" class="h5 fw-bold mb-1 text-dark">
                    <i class="bi bi-chat-dots-fill text-primary me-2"></i>Hỏi & Đáp về sản phẩm
                </h3>
                <small class="text-muted">Bạn có thắc mắc về thành phần hay cách dùng? Hãy đặt câu hỏi để Aloha Beauty giải đáp ngay!</small>
            </div>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-semibold">
                <span id="qa-count">{{ $product->questions->count() }}</span> câu hỏi
            </span>
        </div>

        <!-- Form đặt câu hỏi -->
        <form id="product-question-form" class="mb-4 p-3 bg-light rounded-3 border" data-product-slug="{{ $product->slug }}" data-question-url="{{ route('products.questions.store', $product) }}" onsubmit="handleQuestionSubmit(event)">
            @csrf
            <div class="mb-2">
                <textarea id="qa-question-input" name="question" rows="2" class="form-control" placeholder="Viết câu hỏi của bạn về sản phẩm này (tối thiểu 5 ký tự)..." required minlength="5" maxlength="1000"></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">
                    @auth
                        Gửi dưới tên: <strong class="text-dark">{{ Auth::user()->name }}</strong>
                    @else
                        Bạn đang gửi với tư cách: <span class="badge bg-secondary-subtle text-secondary">Khách vãng lai</span>
                    @endauth
                </small>
                <button type="submit" id="btn-submit-question" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                    <i class="bi bi-send me-1"></i>Gửi câu hỏi
                </button>
            </div>
        </form>

        <!-- Danh sách câu hỏi -->
        <div id="qa-list" class="d-flex flex-column gap-3">
            @forelse($product->questions as $qa)
                <div class="p-3 rounded-3 bg-light border qa-item" id="qa-item-{{ $qa->id }}">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="fw-bold text-dark">
                            <i class="bi bi-question-circle-fill text-warning me-1"></i>
                            {{ $qa->user?->name ?: 'Khách hàng' }}
                        </div>
                        <small class="text-muted">{{ $qa->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-2 text-dark">{{ $qa->question }}</p>

                    @if($qa->answer)
                        <div class="p-2.5 rounded-3 bg-primary-subtle text-dark border-start border-3 border-primary ms-3">
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <span class="badge bg-primary text-white view-inline-7">
                                    <i class="bi bi-patch-check-fill me-1"></i>Aloha Beauty phản hồi
                                </span>
                                @if($qa->answered_at)
                                    <small class="text-muted ms-auto view-inline-8">{{ $qa->answered_at->diffForHumans() }}</small>
                                @endif
                            </div>
                            <div class="small view-inline-9">{{ $qa->answer }}</div>
                        </div>
                    @else
                        <div class="ms-3 text-muted small fst-italic">
                            <i class="bi bi-hourglass-split me-1"></i>Đang chờ chuyên viên Aloha Beauty phản hồi...
                        </div>
                    @endif

                    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'manager', 'customer_service'], true))
                        <div class="mt-2 pt-2 border-top ms-3">
                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 view-inline-10" onclick="toggleAnswerForm({{ $qa->id }})">
                                <i class="bi bi-reply-fill me-1"></i>{{ $qa->answer ? 'Sửa câu trả lời' : 'Trả lời ngay' }}
                            </button>
                            <form id="answer-form-{{ $qa->id }}" action="{{ route('admin.questions.answer', $qa) }}" method="POST" class="d-none mt-2">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="text" name="answer" class="form-control" placeholder="Nhập câu trả lời..." value="{{ $qa->answer }}" required>
                                    <button class="btn btn-success" type="submit">Lưu</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div id="qa-empty-msg" class="text-center text-muted py-3">
                    <i class="bi bi-chat-square-dots fs-3 d-block text-secondary mb-1"></i>
                    Chưa có câu hỏi nào cho sản phẩm này. Hãy là người đầu tiên đặt câu hỏi!
                </div>
            @endforelse
        </div>
    </section>

    <div id="reviewImageModal" class="review-image-lightbox" aria-hidden="true" role="dialog" aria-label="Ảnh đánh giá phóng to">
        <button type="button" class="btn btn-light rounded-pill px-3 review-image-back-button" onclick="window.closeReviewImage(event)"><i class="bi bi-arrow-left me-1"></i>Quay lại</button>
        <button type="button" class="btn-close btn-close-white review-image-close" aria-label="Đóng" title="Đóng ảnh" onclick="window.closeReviewImage(event)"></button>
        <img id="reviewImagePreview" src="" alt="Ảnh đánh giá phóng to" class="review-image-preview">
    </div>

    @if(isset($recommendations) && $recommendations->count() > 0)
        <section class="recommendation-band">
            <div class="d-flex align-items-end justify-content-between mb-3"><div><div class="detail-kicker">Có thể bạn sẽ thích</div><h2 class="h4 fw-bold mb-0">Thường được mua cùng</h2></div><span class="badge bg-danger rounded-pill"><i class="bi bi-stars me-1"></i>Gợi ý thông minh</span></div>
            <div class="row g-3">
                @foreach($recommendations as $rec)
                    @php
                        $recommendationPrices = $rec->variations->map(fn ($variation) => $rec->effectivePrice($variation));
                        $recommendationMinPrice = $recommendationPrices->isNotEmpty() ? $recommendationPrices->min() : $rec->effectivePrice();
                        $recommendationMaxPrice = $recommendationPrices->isNotEmpty() ? $recommendationPrices->max() : $rec->effectivePrice();
                    @endphp
                    <div class="col-6 col-md-3">
                        <div class="recommendation-card h-100">
                            <a href="{{ route('products.show', ['product' => $rec->slug]) }}">
                                @if($rec->image)
                                    <img src="{{ asset('storage/' . $rec->image) }}" class="recommendation-image" alt="{{ $rec->name }}">
                                @else
                                    <div class="recommendation-placeholder"><i class="bi bi-image text-muted fs-2"></i></div>
                                @endif
                            </a>
                            <div class="p-3 d-flex flex-column">
                                <a href="{{ route('products.show', ['product' => $rec->slug]) }}" class="text-decoration-none text-dark fw-bold small mb-2">{{ $rec->name }}</a>
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <span class="text-danger fw-bold small">
                                        @if($recommendationMinPrice < $recommendationMaxPrice)
                                            {{ number_format($recommendationMinPrice, 0, ',', '.') }} - {{ number_format($recommendationMaxPrice, 0, ',', '.') }} đ
                                        @else
                                            {{ number_format($recommendationMinPrice, 0, ',', '.') }} đ
                                        @endif
                                    </span>
                                    <a href="{{ route('products.show', ['product' => $rec->slug]) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Xem sản phẩm"><i class="bi bi-arrow-up-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <x-recently-viewed currentSlug="{{ $product->slug }}" />
</div>

<!-- STICKY ADD TO CART BAR (PROMPT 3.5) -->
<div id="sticky-add-to-cart" class="sticky-add-to-cart" aria-hidden="true">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3 min-w-0">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/placeholder.svg') }}" class="rounded-2 object-fit-cover flex-shrink-0 view-inline-11" alt="{{ $product->name }}">
            <div class="text-truncate">
                <div class="fw-bold text-dark text-truncate small view-inline-12">{{ $product->name }}</div>
                <div class="text-danger fw-bold fs-6">{{ number_format($product->effectivePrice(), 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($product->quantity > 0)
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-1 shadow-sm" onclick="document.getElementById('btn-add-to-cart')?.click()">
                    <i class="bi bi-bag-plus-fill"></i>
                    <span class="d-none d-sm-inline">Thêm vào giỏ</span>
                </button>
            @else
                <button class="btn btn-secondary rounded-pill px-4 py-2 fw-bold disabled" disabled>Hết hàng</button>
            @endif
        </div>
    </div>
</div>

<!-- GALLERY LIGHTBOX MODAL (PROMPT 3.5) -->
<div id="productGalleryModal" class="gallery-modal-overlay" aria-hidden="true" role="dialog" aria-label="Bộ sưu tập ảnh sản phẩm">
    <button type="button" class="btn-close btn-close-white position-fixed top-0 end-0 m-4 view-inline-13" onclick="window.closeGalleryModal()" aria-label="Đóng"></button>
    <button type="button" class="gallery-modal-nav prev" onclick="window.navGallery(-1)" aria-label="Ảnh trước"><i class="bi bi-chevron-left"></i></button>
    <button type="button" class="gallery-modal-nav next" onclick="window.navGallery(1)" aria-label="Ảnh kế tiếp"><i class="bi bi-chevron-right"></i></button>
    
    <div class="gallery-modal-stage">
        <img id="galleryModalMainImg" src="" alt="{{ $product->name }}">
    </div>

    <div class="text-white-50 small mt-2 mb-1" id="galleryModalCounter">1 / 1</div>

    <div class="gallery-modal-thumbs" id="galleryModalThumbs"></div>
</div>

<script src="{{ asset_v('js/views/products-show-blade-php.js') }}" defer></script>
@endsection