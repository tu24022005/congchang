@extends('layouts.app')
@section('title', 'BeatyCare 🌸 - Mỹ phẩm & Chăm sóc sắc đẹp chính hãng Aloha Beauty')
@section('canonical', route('welcome'))

@section('structured_data')
@php
    $siteSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => url('/#organization'),
                'name' => config('shop.seo.site_name', 'Aloha Beauty'),
                'url' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset(config('shop.seo.default_og_image', 'images/og-default.svg')),
                ],
                'contactPoint' => [
                    [
                        '@type' => 'ContactPoint',
                        'telephone' => '+84-900-000-000',
                        'contactType' => 'customer service',
                        'areaServed' => 'VN',
                        'availableLanguage' => 'Vietnamese',
                    ],
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('/#website'),
                'url' => url('/'),
                'name' => 'BeatyCare',
                'description' => config('shop.seo.default_description'),
                'publisher' => [
                    '@id' => url('/#organization'),
                ],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => url('/products') . '?search={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
                'inLanguage' => 'vi',
            ],
        ],
    ];
@endphp
<template class="jsonld-template">
{!! json_encode($siteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</template>
@endsection

@section('content')
@auth
    @if(Auth::user()->role === 'admin')
        <a class="home-admin-chat-dock" href="{{ route('admin.chat.index') }}" title="Mở trung tâm chat khách hàng">
            <i class="bi bi-chat-square-text-fill"></i><span>Chat hỗ trợ</span><b id="home-chat-badge" class="home-chat-badge d-none">0</b>
        </a>
    @endif
@endauth

<!-- BANNER TRƯỢT TỰ ĐỘNG (CAROUSEL) -->
<div id="heroCarousel" class="carousel slide hero-carousel mb-5 animate__animated animate__fadeInDown" data-bs-ride="carousel" data-bs-interval="4000" data-bs-wrap="true">
    <div class="carousel-indicators">
        @foreach($banners as $index => $banner)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></button>
        @endforeach
    </div>

    <div class="carousel-inner">
        @foreach($banners as $index => $banner)
        <div class="carousel-item {{ $index === 0 ? 'active' : '' }} overflow-hidden position-relative" data-banner-id="{{ $banner->id }}" data-has-video="{{ $banner->has_video ? 'true' : 'false' }}">
            @if($banner->has_video)
                <div class="banner-media-container w-100 h-100 position-absolute top-0 start-0">
                    @if($banner->video_type === 'youtube')
                        <div class="banner-video-iframe-wrap w-100 h-100 overflow-hidden">
                            <iframe id="hero-banner-yt-{{ $banner->id }}"
                                    class="banner-video-iframe w-100 h-100"
                                    src="{{ $banner->youtube_embed_url }}"
                                    title="{{ $banner->title }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                    loading="lazy">
                            </iframe>
                        </div>
                    @elseif($banner->video_type === 'vimeo')
                        <div class="banner-video-iframe-wrap w-100 h-100 overflow-hidden">
                            <iframe id="hero-banner-vimeo-{{ $banner->id }}"
                                    class="banner-video-iframe w-100 h-100"
                                    src="{{ $banner->vimeo_embed_url }}"
                                    title="{{ $banner->title }}"
                                    allow="autoplay; fullscreen; picture-in-picture"
                                    allowfullscreen
                                    loading="lazy">
                            </iframe>
                        </div>
                    @else
                        <video id="hero-banner-vid-{{ $banner->id }}"
                               class="banner-video-native w-100 h-100 view-inline-1"
                               {{ $banner->video_autoplay ? 'autoplay' : '' }}
                               {{ $banner->video_muted ? 'muted' : '' }}
                               {{ $banner->video_loop ? 'loop' : '' }}
                               playsinline
                               preload="metadata"
                               poster="{{ $banner->image_source }}">
                            <source src="{{ $banner->video_source }}" type="{{ $banner->video_mime_type }}">
                        </video>
                    @endif
                    <!-- Lớp phủ mờ bảo vệ độ tương phản cho tiêu đề và chữ -->
                    <div class="banner-video-scrim position-absolute top-0 start-0 w-100 h-100 pointer-events-none"></div>

                    <!-- Nút điều khiển âm thanh và phát video cho video trực tiếp -->
                    @if($banner->video_type === 'direct')
                        <div class="banner-video-actions position-absolute bottom-0 end-0 m-3 z-3 d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-dark bg-opacity-75 border-0 rounded-circle banner-sound-btn" data-target="hero-banner-vid-{{ $banner->id }}" aria-label="Bật/Tắt âm thanh" title="Bật/Tắt âm thanh">
                                <i class="bi bi-volume-mute-fill"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-dark bg-opacity-75 border-0 rounded-circle banner-vid-play-btn" data-target="hero-banner-vid-{{ $banner->id }}" aria-label="Tạm dừng video" title="Tạm dừng video">
                                <i class="bi bi-pause-fill"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <img src="{{ $banner->image_source }}" class="ken-burns-bg w-100 h-100 view-inline-1" alt="{{ $banner->alt_text ?: $banner->title }}" {!! $index === 0 ? 'fetchpriority="high"' : 'loading="lazy" decoding="async"' !!} onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
            @endif

            <div class="carousel-caption z-2">
                @if($banner->badge)<span class="badge bg-warning text-dark mb-2 px-3 py-2 fs-6 rounded-pill">{{ $banner->badge }}</span>@endif
                <h1>{{ $banner->title }}</h1>
                @if($banner->description)<p class="mb-4 w-50 d-none d-md-block">{{ $banner->description }}</p>@endif
                @if($banner->button_text && $banner->button_url)
                    <a href="{{ $banner->button_url }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold me-2">{{ $banner->button_text }} <i class="bi bi-chevron-right"></i></a>
                @endif
                @if($banner->has_video && $banner->video_type === 'youtube')
                    <a href="{{ $banner->video_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold btn-sm mt-1" title="Mở video trên YouTube">
                        <i class="bi bi-youtube text-danger me-1"></i> Xem trên YouTube
                    </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon welcome-carousel-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon welcome-carousel-icon" aria-hidden="true"></span>
    </button>

</div>

@push('scripts')
<script src="{{ asset_v('js/views/welcome-blade-php.js') }}" defer></script>
@endpush

<!-- LỜI CHÀO -->
<div class="text-center mb-5 animate__animated animate__fadeInUp">
    <h2 class="fw-bold product-page-title">BEATYCARE 🌸 MÙA HÈ RỰC RỠ</h2>
    <p class="text-muted">Khám phá không gian mua sắm thư giãn cho làn da và cơ thể</p>
</div>

<!-- DANH MỤC SẢN PHẨM -->
<section class="product-categories mb-5 reveal-up" aria-labelledby="home-categories-title">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 id="home-categories-title" class="fw-bold mb-0 storefront-title">Khám phá theo danh mục</h4>
        <a href="{{ route('products.index') }}" class="small text-decoration-none category-view-all">Xem tất cả</a>
    </div>
    <div class="row g-3 reveal-stagger">
        @foreach($categories as $category)
            <div class="col-6 col-md-3">
                <a href="{{ route('products.index', ['category' => $category->id]) }}" class="category-card">
                    @if($category->image)
                        <span class="category-card-cover"><img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';"></span>
                    @else
                        <span class="category-card-icon"><i class="bi bi-bag-heart"></i></span>
                    @endif
                    <span class="category-card-name">{{ $category->name }}</span>
                    <small>{{ $category->products_count }} sản phẩm</small>
                </a>
            </div>
        @endforeach
    </div>
</section>

<!-- FLASH SALE -->
@if($flashSaleProducts->isNotEmpty())
<section class="flash-sale-section mb-5 reveal-up" aria-labelledby="flash-sale-title">
    <div class="flash-sale-heading">
        <div>
            <span class="flash-sale-kicker"><i class="bi bi-lightning-charge-fill me-1"></i> ƯU ĐÃI CÓ HẠN</span>
            <h3 id="flash-sale-title" class="fw-bold mb-0"><i class="bi bi-fire text-danger me-1"></i> FLASH SALE</h3>
        </div>
        <span class="flash-sale-note">Săn deal đẹp, giá siêu hời mỗi ngày</span>
    </div>
    <div class="flash-sale-track reveal-stagger">
        @foreach($flashSaleProducts as $flashProduct)
            @php
                $flashPrice = $flashProduct->effectivePrice();
                $discountPercent = $flashProduct->price > 0
                    ? round((1 - ($flashPrice / (float) $flashProduct->price)) * 100)
                    : 0;
            @endphp
            <a href="{{ route('products.show', ['product' => $flashProduct->slug]) }}" class="flash-sale-card">
                <div class="flash-sale-image">
                    @if($flashProduct->image)
                        <img src="{{ asset('storage/' . $flashProduct->image) }}" alt="{{ $flashProduct->name }}" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                    @else
                        <i class="bi bi-bag-heart"></i>
                    @endif
                    <span class="flash-sale-discount">-{{ $discountPercent }}%</span>
                </div>
                <div class="flash-sale-card-body">
                    <h5>{{ $flashProduct->name }}</h5>
                    <div class="flash-sale-prices">
                        <strong>{{ number_format($flashPrice, 0, ',', '.') }}đ</strong>
                        <del>{{ number_format($flashProduct->price, 0, ',', '.') }}đ</del>
                    </div>
                    <div class="flash-sale-progress-label">
                        <span>Đã bán {{ $flashProduct->sold_percent }}%</span>
                        @if(($flashProduct->sold_percent ?? 0) >= 80 || $flashProduct->quantity <= 5)
                            <span class="text-danger fw-bold"><i class="bi bi-fire text-danger me-1"></i>Sắp cháy hàng</span>
                        @else
                            <span>Còn {{ $flashProduct->quantity }}</span>
                        @endif
                    </div>
                    <div class="flash-sale-progress" role="progressbar" aria-label="Đã bán {{ $flashProduct->sold_percent }}%" aria-valuenow="{{ $flashProduct->sold_percent }}" aria-valuemin="0" aria-valuemax="100">
                        <span data-inline-transform="scaleX({{ max(0.05, min(1, ($flashProduct->sold_percent ?? 0) / 100)) }})" class="inline-dynamic-transform"></span>
                    </div>
                    <div class="flash-sale-countdown" data-countdown="{{ $flashProduct->flash_sale_ends_at->toIso8601String() }}">
                        <i class="bi bi-clock-history me-1"></i><span>Còn: --:--:--</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

<!-- SAN PHAM HOT -->
<section class="hot-products-section mb-5 reveal-up" aria-labelledby="hot-products-title">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div><span class="hot-products-kicker"><i class="bi bi-lightning-charge-fill me-1"></i> FLASH SALE & ĐANG ĐƯỢC QUAN TÂM</span><h3 id="hot-products-title" class="fw-bold mb-0 storefront-title">Ưu đãi nổi bật hôm nay</h3></div>
        <div class="d-flex gap-2"><button type="button" class="btn btn-light border rounded-circle hot-scroll-button" data-direction="-1" aria-label="Xem sản phẩm trước"><i class="bi bi-arrow-left"></i></button><button type="button" class="btn btn-light border rounded-circle hot-scroll-button" data-direction="1" aria-label="Xem sản phẩm tiếp theo"><i class="bi bi-arrow-right"></i></button></div>
    </div>
    <div id="hot-products-track" class="hot-products-track reveal-stagger">
        @foreach($hotProducts as $hotProduct)
            @php
                $hotPrices = $hotProduct->variations->map(fn ($variation) => $hotProduct->effectivePrice($variation));
                $hotMinPrice = $hotPrices->isNotEmpty() ? $hotPrices->min() : $hotProduct->effectivePrice();
                $hotMaxPrice = $hotPrices->isNotEmpty() ? $hotPrices->max() : $hotProduct->effectivePrice();
            @endphp
            <a href="{{ route('products.show', ['product' => $hotProduct->slug]) }}" class="hot-product-card">
                <div class="hot-product-image">@if($hotProduct->image)<img src="{{ asset('storage/' . $hotProduct->image) }}" alt="{{ $hotProduct->name }}" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">@else<i class="bi bi-bag-heart"></i>@endif</div>
                <div class="p-3">
                    @if($hotProduct->isFlashSaleActive())
                        <span class="badge bg-danger rounded-pill mb-2"><i class="bi bi-lightning-charge-fill"></i> FLASH SALE</span>
                    @else
                        <span class="badge bg-info-subtle text-info-emphasis rounded-pill mb-2">{{ $hotProduct->category->name ?? 'Beauty' }}</span>
                    @endif
                    <h5>{{ $hotProduct->name }}</h5>
                    @if($hotProduct->isFlashSaleActive())<small class="text-muted text-decoration-line-through">{{ number_format($hotProduct->price, 0, ',', '.') }} ₫</small><br>@endif
                    <strong>@if($hotMinPrice < $hotMaxPrice){{ number_format($hotMinPrice, 0, ',', '.') }} - {{ number_format($hotMaxPrice, 0, ',', '.') }}@else{{ number_format($hotMinPrice, 0, ',', '.') }}@endif ₫</strong>
                </div>
            </a>
        @endforeach
    </div>
</section>

<!-- DANH SÁCH SẢN PHẨM -->
<div class="row g-4 justify-content-center reveal-stagger">
    @foreach($products as $product)
    @php
        $productPrices = $product->variations->pluck('price')->map(fn ($price) => (float) $price);
        $productMinPrice = $productPrices->isNotEmpty() ? $productPrices->min() : (float) $product->price;
        $productMaxPrice = $productPrices->isNotEmpty() ? $productPrices->max() : (float) $product->price;
        $productStock = $product->available_stock;
    @endphp
    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card product-card text-center h-100 shadow-sm">
            <div class="card-body p-4 d-flex flex-column">
                
                <div class="mb-3 shine-sweep rounded-3 position-relative overflow-hidden view-inline-2">
                    <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="d-block w-100 h-100">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover rounded product-list-image" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                        @else
                            <div class="d-flex align-items-center justify-content-center w-100 h-100 rounded-3 bg-light product-icon-placeholder">
                                <i class="bi bi-bag-heart text-primary fs-3"></i>
                            </div>
                        @endif
                    </a>

                    @if($productStock <= 0)
                        <span class="position-absolute top-0 start-0 m-2 badge bg-danger-subtle text-danger rounded-pill fw-bold z-2">
                            <i class="bi bi-slash-circle me-1"></i>Hết hàng
                        </span>
                    @endif

                    <!-- NÚT TIỆN ÍCH NỔI TRÊN ẢNH: XEM NHANH & SO SÁNH -->
                    <div class="product-card-floating-actions position-absolute top-0 end-0 m-2 d-flex flex-column gap-1 z-2">
                        <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm floating-action-btn btn-quick-view" data-quick-view-slug="{{ $product->slug }}" title="Xem nhanh sản phẩm" aria-label="Xem nhanh {{ $product->name }}">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm floating-action-btn btn-compare-toggle" data-compare-id="{{ $product->id }}" data-compare-name="{{ $product->name }}" data-compare-image="{{ $product->image ? asset('storage/' . $product->image) : asset('images/placeholder.svg') }}" title="So sánh sản phẩm" aria-label="So sánh {{ $product->name }}">
                            <i class="bi bi-arrow-left-right"></i>
                        </button>
                    </div>
                </div>
                
                <span class="badge text-primary rounded-pill mb-2 mx-auto product-category-badge">
                    {{ $product->category->name ?? 'Mỹ phẩm chăm sóc da' }}
                </span>

                <h5 class="fw-bold text-dark mb-2">{{ $product->name }}</h5>
                <p class="text-muted small mb-3 flex-grow-1 product-description">
                    {{ $product->description ?? 'Sản phẩm chăm sóc cá nhân chất lượng cho vẻ đẹp rạng ngời mỗi ngày.' }}
                </p>

                <h5 class="fw-bold text-danger mb-1">@if($productMinPrice < $productMaxPrice){{ number_format($productMinPrice, 0, ',', '.') }} - {{ number_format($productMaxPrice, 0, ',', '.') }}@else{{ number_format($productMinPrice, 0, ',', '.') }}@endif ₫</h5>
                <p class="text-muted small mb-3"><i class="bi bi-box-seam me-1"></i>Còn lại: {{ $productStock > 0 ? $productStock : 'Hết hàng' }}</p>

                <div class="product-card-price-wrap">
                    @if($product->reviews_avg_rating)
                        <div class="product-card-meta-row">
                            <span class="product-card-rating"><i class="bi bi-star-fill"></i> {{ number_format($product->reviews_avg_rating, 1) }} / 5</span>
                            <span class="product-card-reviews"><i class="bi bi-chat-left-text"></i> {{ $product->reviews_count ?? $product->reviews->count() }} đánh giá</span>
                        </div>
                    @else
                        <div class="product-card-meta-row">
                            <span class="product-card-rating muted"><i class="bi bi-star-fill"></i> Chưa có</span>
                            <span class="product-card-reviews"><i class="bi bi-chat-left-text"></i> 0 đánh giá</span>
                        </div>
                    @endif
                </div>

                <!-- ACTION ROW: Nút Thêm vào giỏ hàng tinh gọn -->
                <div class="action-row pt-2 border-top mt-auto">
                    @if($productStock > 0)
                        <button type="button" class="btn btn-sm btn-primary rounded-pill w-100 py-2 fw-semibold btn-quick-add shadow-sm d-flex align-items-center justify-content-center gap-2" data-quick-add-id="{{ $product->id }}" data-quick-add-slug="{{ $product->slug }}" data-has-variations="{{ $product->variations->isNotEmpty() ? 'true' : 'false' }}" title="Thêm vào giỏ" aria-label="Thêm {{ $product->name }} vào giỏ">
                            <i class="bi bi-bag-plus"></i>
                            <span>{{ $product->variations->isNotEmpty() ? 'Chọn mua' : 'Thêm vào giỏ' }}</span>
                        </button>
                    @else
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill w-100 py-2 fw-semibold disabled" disabled>
                            <i class="bi bi-slash-circle me-1"></i>Hết hàng
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection