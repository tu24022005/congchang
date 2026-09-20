@extends('layouts.app')
@section('title', $product->name)

@section('content')
<style>
    .shop-detail { --shop-ink: #1f2d3d; --shop-muted: #718096; --shop-line: #e5eaf0; }
    .detail-breadcrumb { display: flex; flex-wrap: wrap; align-items: center; gap: .35rem; color: #64748b; font-size: .82rem; }
    .detail-breadcrumb a { color: #1677c8; text-decoration: none; }
    .detail-breadcrumb a:hover { color: #ee4d2d; text-decoration: underline; }
    .detail-breadcrumb .breadcrumb-separator { color: #a0aec0; }
    .detail-breadcrumb .breadcrumb-current { max-width: min(55vw, 620px); overflow: hidden; color: #526170; text-overflow: ellipsis; white-space: nowrap; }
    .detail-shell { background: rgba(255,255,255,.9); border: 1px solid rgba(255,255,255,.95); border-radius: 22px; box-shadow: 0 18px 50px rgba(38, 60, 80, .1); }
    .detail-gallery { position: sticky; top: 1rem; }
    .detail-main-image { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 18px; background: #f6f8fb; }
    .detail-thumb { width: 72px; height: 72px; object-fit: cover; border-radius: 12px; border: 2px solid transparent; cursor: pointer; transition: border-color .2s, transform .2s; }
    .detail-thumb:hover, .detail-thumb.active { border-color: #08a9d2; transform: translateY(-2px); }
    .detail-kicker { color: #0082c8; font-size: .72rem; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .detail-title { color: var(--shop-ink); font-size: clamp(1.7rem, 3vw, 2.65rem); line-height: 1.1; letter-spacing: -.04em; }
    .detail-rating { color: #f59e0b; }
    .detail-price { color: #e63950; font-size: 2rem; font-weight: 800; }
    .detail-stock { color: #16865b; background: #eaf8f0; border-radius: 999px; padding: .45rem .75rem; font-size: .78rem; font-weight: 800; }
    .detail-stock.out { color: #c0394b; background: #fff0f2; }
    .detail-copy { color: #65717c; line-height: 1.75; white-space: pre-line; }
    .variation-picker { padding: 1rem; background: #fff8f8; border: 1px solid #f5dddd; border-radius: 12px; }
    .variation-picker-row { display: flex; align-items: flex-start; gap: 1rem; }
    .variation-picker-label { flex: 0 0 105px; padding-top: .55rem; color: #64748b; font-size: .85rem; }
    .variation-options { display: flex; flex: 1; flex-wrap: wrap; gap: .55rem; }
    .variation-option { position: relative; display: inline-flex; min-width: 100px; flex-direction: column; gap: .15rem; padding: .5rem .75rem; color: #334155; background: #fff; border: 1px solid #d8dee8; border-radius: 4px; cursor: pointer; }
    .variation-option:has(input:checked) { color: #ee4d2d; background: #fff; border-color: #ee4d2d; box-shadow: 0 0 0 1px #ee4d2d; }
    .variation-option input { position: absolute; width: 1px; height: 1px; opacity: 0; }
    .variation-code { font-weight: 700; }
    .variation-name { color: #64748b; font-size: .75rem; }
    .detail-divider { border-color: var(--shop-line); }
    .quantity-picker { display: inline-flex; align-items: center; border: 1px solid #dbe4ef; border-radius: 12px; overflow: hidden; height: 48px; }
    .quantity-picker button { width: 44px; height: 100%; border: 0; color: #1f6578; background: #f2fbfd; font-size: 1.2rem; }
    .quantity-picker input { width: 52px; height: 100%; border: 0; text-align: center; font-weight: 800; outline: 0; }
    .detail-buy { min-height: 48px; border-radius: 12px; font-weight: 800; }
    .detail-benefit { border-top: 1px solid var(--shop-line); padding-top: 1rem; margin-top: 1.35rem; }
    .detail-benefit-item { display: flex; gap: .65rem; color: #526170; font-size: .82rem; }
    .detail-benefit-item i { color: #08a9d2; font-size: 1.1rem; }
    .recommendation-band { margin-top: 2rem; }
    .recommendation-card { border: 1px solid rgba(229,234,240,.9); border-radius: 16px; overflow: hidden; background: rgba(255,255,255,.9); transition: transform .2s, box-shadow .2s; }
    .recommendation-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(38,60,80,.12); }
    .recommendation-image, .recommendation-placeholder { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; }
    .recommendation-placeholder { display: grid; place-items: center; background: #f5f7fa; }
    .product-information { margin-top: 1.5rem; padding: 1.5rem; background: rgba(255,255,255,.92); border: 1px solid #e5eaf0; border-radius: 18px; box-shadow: 0 12px 30px rgba(38,60,80,.06); }
    .product-information-title { margin: 0; padding-bottom: 1rem; color: #263238; font-size: 1.1rem; font-weight: 800; text-transform: uppercase; border-bottom: 1px solid #edf0f3; }
    .product-detail-table { width: 100%; margin: 1rem 0 0; }
    .product-detail-table th { width: 190px; padding: .55rem 1rem .55rem 0; color: #87909a; font-weight: 400; vertical-align: top; }
    .product-detail-table td { padding: .55rem 0; color: #334155; }
    .product-detail-chip { display: inline-block; margin: 0 .35rem .35rem 0; padding: .3rem .6rem; color: #526170; background: #f6f8fa; border: 1px solid #e2e8f0; border-radius: 4px; font-size: .86rem; }
    .product-description-block { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #edf0f3; }
    .product-description-block p { color: #526170; line-height: 1.8; white-space: pre-line; }
    .product-reviews { margin-top: 1.5rem; padding: 1.5rem; background: rgba(255,255,255,.92); border: 1px solid #e5eaf0; border-radius: 18px; box-shadow: 0 12px 30px rgba(38,60,80,.06); }
    .review-summary { display: flex; flex-wrap: wrap; gap: 1.5rem; margin-top: 1rem; padding: 1.2rem; background: #fff8f8; border: 1px solid #f5dddd; }
    .review-score { min-width: 130px; color: #d73211; text-align: center; }
    .review-score strong { display: block; font-size: 2.2rem; line-height: 1; }
    .review-stars { color: #ee4d2d; letter-spacing: .08em; }
    .review-filters { display: flex; flex: 1; flex-wrap: wrap; align-content: center; gap: .5rem; }
    .review-filter { padding: .45rem .7rem; color: #526170; background: #fff; border: 1px solid #d8dee8; border-radius: 4px; }
    .review-filter.active, .review-filter:hover { color: #ee4d2d; border-color: #ee4d2d; }
    .review-item { display: flex; gap: .75rem; padding: 1.2rem 0; border-bottom: 1px solid #edf0f3; }
    .review-image-button { padding: 0; background: transparent; border: 0; cursor: zoom-in; }
    .review-image-button img { width: 76px; height: 76px; object-fit: cover; border: 1px solid #e2e8f0; border-radius: 8px; transition: transform .2s, box-shadow .2s; }
    .review-image-button:hover img { transform: scale(1.04); box-shadow: 0 8px 18px rgba(38,60,80,.18); }
    .review-image-preview { display: block; max-width: min(92vw, 1100px); max-height: 82vh; margin: 0 auto; border-radius: 12px; object-fit: contain; }
        .review-image-lightbox { display: none; position: fixed; inset: 0; z-index: 1080; align-items: center; justify-content: center; padding: 3rem 1rem 1rem; background: rgba(0,0,0,.72); }
        .review-image-lightbox.is-open { display: flex; }
        .review-image-back-button { position: fixed; top: 1rem; right: 3.5rem; z-index: 1082; color: #263238; background: #fff; border: 0; box-shadow: 0 6px 18px rgba(0,0,0,.2); }
        .review-image-lightbox .review-image-close { position: fixed; top: 1.2rem; right: 1.2rem; z-index: 1082; }
    .review-avatar { display: grid; flex: 0 0 38px; place-items: center; width: 38px; height: 38px; color: #0b5961; background: #dff5f2; border-radius: 50%; font-weight: 700; }
    .review-meta { color: #8a959f; font-size: .78rem; }
    .review-verified { color: #15966a; font-size: .78rem; }
    .review-comment { margin: .45rem 0 0; color: #334155; line-height: 1.6; }
    @media (max-width: 991.98px) { .detail-gallery { position: static; } }
</style>

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
                    @if($product->image)
                        <img id="detail-main-image" src="{{ asset('storage/' . $product->image) }}" class="detail-main-image" alt="{{ $product->name }}">
                    @else
                        <div id="detail-main-image" class="detail-main-image d-grid place-items-center text-muted"><i class="bi bi-image fs-1"></i></div>
                    @endif
                    @php
                        $shownGalleryImages = $product->image ? [$product->image] : [];
                    @endphp
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @if($product->image)<img class="detail-thumb active" src="{{ asset('storage/' . $product->image) }}" data-image="{{ asset('storage/' . $product->image) }}" alt="Ảnh chính">@endif
                        @foreach($product->images as $image)
                            @if($image->image_path && !in_array($image->image_path, $shownGalleryImages, true))
                                <img class="detail-thumb" src="{{ asset('storage/' . $image->image_path) }}" data-image="{{ asset('storage/' . $image->image_path) }}" alt="Ảnh sản phẩm">
                                @php
                                    $shownGalleryImages[] = $image->image_path;
                                @endphp
                            @endif
                        @endforeach
                        @foreach($product->variations as $variation)
                            @if($variation->image && !in_array($variation->image, $shownGalleryImages, true))
                                <img class="detail-thumb" src="{{ asset('storage/' . $variation->image) }}" data-image="{{ asset('storage/' . $variation->image) }}" data-variation="{{ $variation->id }}" alt="Ảnh {{ $variation->sku ?: 'biến thể' }}">
                                @php
                                    $shownGalleryImages[] = $variation->image;
                                @endphp
                            @endif
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill mt-3" id="copy-product-link"><i class="bi bi-link-45deg me-1"></i>Chia sẻ sản phẩm</button>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="detail-kicker mb-2">{{ $product->category?->name ?? 'Aloha Beauty' }}</div>
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
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4"><span class="detail-rating"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i></span><span class="text-muted small">Được lựa chọn bởi khách hàng Aloha</span></div>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4"><span class="detail-price">{{ number_format($product->price, 0, ',', '.') }} đ</span><span class="{{ $product->quantity > 0 ? 'detail-stock' : 'detail-stock out' }}"><i class="bi bi-{{ $product->quantity > 0 ? 'check-circle' : 'x-circle' }} me-1"></i>{{ $product->quantity > 0 ? 'Còn ' . $product->quantity . ' sản phẩm' : 'Hết hàng' }}</span></div>
                <p class="detail-copy mb-4">{{ $product->description ?: 'Một lựa chọn chăm sóc cá nhân dịu nhẹ, phù hợp cho chu trình làm đẹp hằng ngày.' }}</p>

                @auth
                    @if(in_array(Auth::user()->role, ['admin', 'manager', 'warehouse_staff'], true))
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
                                                <input type="radio" name="variation_id" value="{{ $variation->id }}" data-price="{{ $variation->price }}" data-stock="{{ $variation->stock }}" data-image="{{ $variation->image ? asset('storage/' . $variation->image) : '' }}" data-label="{{ $variationLabel ?: 'Mặc định' }}" @checked($index === 0)>
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
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success detail-buy flex-grow-1"><i class="bi bi-bag-plus me-2"></i>Thêm vào giỏ hàng</button>
                                <button type="submit" name="buy_now" value="1" class="btn btn-dark detail-buy flex-grow-1"><i class="bi bi-lightning-charge me-2"></i>Mua ngay</button>
                            </div>
                        </form>
                    @else
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
                    @endif
                    @endif
                @else
                    @if($product->quantity > 0)
                        <a href="{{ route('login') }}" class="btn btn-warning detail-buy w-100"><i class="bi bi-person me-2"></i>Đăng nhập để mua hàng</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-warning detail-buy w-100"><i class="bi bi-bell me-2"></i>Đăng nhập để được báo khi có hàng</a>
                    @endif
                @endauth

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
                <tr><th>Danh mục</th><td>{{ $product->category?->name ?? 'Mỹ phẩm & chăm sóc cá nhân' }}</td></tr>
                <tr><th>Mã sản phẩm</th><td>@forelse($product->variations as $variation)<span class="product-detail-chip">{{ $variation->sku ?: 'Chưa có SKU' }}</span>@empty<span>Chưa có mã biến thể</span>@endforelse</td></tr>
                <tr><th>Màu / phân loại</th><td>@php $colors = $product->variations->pluck('color')->filter()->unique(); @endphp @forelse($colors as $color)<span class="product-detail-chip">{{ $color }}</span>@empty<span>Phân loại mặc định</span>@endforelse</td></tr>
                <tr><th>Khối lượng / dung tích</th><td>@php $sizes = $product->variations->map(fn ($variation) => $variation->size_value ? rtrim(rtrim($variation->size_value, '0'), '.') . $variation->size_unit : $variation->storage)->filter()->unique(); @endphp @forelse($sizes as $size)<span class="product-detail-chip">{{ $size }}</span>@empty<span>Chưa cập nhật</span>@endforelse</td></tr>
                <tr><th>Số phiên bản</th><td>{{ $product->variations->count() ?: 1 }} phiên bản</td></tr>
                <tr><th>Tình trạng</th><td>{{ $product->quantity > 0 ? 'Còn hàng' : 'Hết hàng' }}</td></tr>
            </tbody>
        </table>
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
    @endphp
    <section class="product-reviews" aria-labelledby="product-reviews-title">
        <h2 id="product-reviews-title" class="product-information-title">Đánh giá sản phẩm</h2>
        <div class="review-summary">
            <div class="review-score"><strong>{{ number_format($ratingAverage, 1) }}</strong><span>trên 5</span><div class="review-stars mt-2">★★★★★</div><small class="text-muted">{{ $reviewCount }} đánh giá</small></div>
            <div class="review-filters">
                <button type="button" class="review-filter active" data-review-filter="all">Tất cả</button>
                @for($rating = 5; $rating >= 1; $rating--)
                    <button type="button" class="review-filter" data-review-filter="{{ $rating }}">{{ $rating }} Sao ({{ $ratingCounts[$rating] }})</button>
                @endfor
                <button type="button" class="review-filter" data-review-filter="comment">Có Bình luận ({{ $reviewCount }})</button>
                <button type="button" class="review-filter" data-review-filter="media">Có Hình ảnh / Video ({{ $mediaReviewCount }})</button>
            </div>
        </div>

        <div id="review-list">
            @forelse($product->reviews as $review)
                <article class="review-item" data-review-rating="{{ $review->rating }}" data-review-media="{{ !empty($review->media_paths) ? '1' : '0' }}">
                    <div class="review-avatar">{{ strtoupper(substr($review->reviewer_name ?: $review->user?->name ?: 'A', 0, 1)) }}</div>
                    <div class="flex-grow-1">
                        <strong>{{ $review->reviewer_name ?: $review->user?->name ?: 'Khách hàng Aloha' }}</strong>
                        <div class="review-stars">{{ str_repeat('★', $review->rating) }}<span class="text-muted">{{ str_repeat('★', 5 - $review->rating) }}</span></div>
                        <div class="review-meta">{{ $review->created_at->format('d/m/Y H:i') }} @if($review->variant_label) | Phân loại hàng: {{ $review->variant_label }} @endif</div>
                        @if($review->is_verified_purchase)<div class="review-verified"><i class="bi bi-patch-check-fill me-1"></i>Đã mua hàng</div>@endif
                        <p class="review-comment">{{ $review->comment }}</p>
                        @if($review->media_paths)
                            <div class="d-flex flex-wrap gap-2 mt-2">@foreach($review->media_paths as $path)<button type="button" class="review-image-button" data-review-image="{{ request()->getSchemeAndHttpHost() . '/storage/' . ltrim($path, '/') }}" aria-label="Xem ảnh đánh giá"><img src="{{ request()->getSchemeAndHttpHost() . '/storage/' . ltrim($path, '/') }}" alt="Ảnh đánh giá" onerror="this.closest('.review-image-button').style.display='none'"></button>@endforeach</div>
                        @endif
                    </div>
                </article>
            @empty
                <p class="text-muted text-center py-4 mb-0">Chưa có đánh giá nào cho sản phẩm này.</p>
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
                    <div class="col-6 col-md-3"><div class="recommendation-card h-100"><a href="{{ route('products.show', ['product' => $rec->slug]) }}">@if($rec->image)<img src="{{ asset('storage/' . $rec->image) }}" class="recommendation-image" alt="{{ $rec->name }}">@else<div class="recommendation-placeholder"><i class="bi bi-image text-muted fs-2"></i></div>@endif</a><div class="p-3 d-flex flex-column h-100"><a href="{{ route('products.show', ['product' => $rec->slug]) }}" class="text-decoration-none text-dark fw-bold small mb-2">{{ $rec->name }}</a><div class="mt-auto d-flex justify-content-between align-items-center"><span class="text-danger fw-bold small">{{ number_format($rec->price, 0, ',', '.') }} đ</span><a href="{{ route('products.show', ['product' => $rec->slug]) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Xem sản phẩm"><i class="bi bi-arrow-up-right"></i></a></div></div></div></div>
                @endforeach
            </div>
        </section>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // TRICK GIẢI CỨU MÀN HÌNH ĐEN: Đưa Lightbox tự code ra ngoài thẻ body
    const reviewLightbox = document.getElementById('reviewImageModal');
    if (reviewLightbox) {
        document.body.appendChild(reviewLightbox);
    }

    const mainImage = document.getElementById('detail-main-image');
    document.querySelectorAll('.detail-thumb').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            if (mainImage?.tagName === 'IMG') mainImage.src = this.dataset.image;
            document.querySelectorAll('.detail-thumb').forEach(item => item.classList.remove('active'));
            this.classList.add('active');
        });
    });

    const quantityInput = document.getElementById('detail-quantity');
    const priceElement = document.querySelector('.detail-price');
    const stockElement = document.querySelector('.detail-stock');
    const stockNote = document.getElementById('detail-stock-note');
    document.querySelectorAll('input[name="variation_id"]').forEach(function (option) {
        option.addEventListener('change', function () {
            const stock = Number(this.dataset.stock || 0);
            quantityInput.max = Math.max(1, stock);
            quantityInput.value = Math.min(Number(quantityInput.value || 1), Math.max(1, stock));
            priceElement.textContent = Number(this.dataset.price).toLocaleString('vi-VN') + ' đ';
            stockNote.textContent = 'Tối đa ' + stock + ' sản phẩm';
            stockElement.classList.toggle('out', stock < 1);
            stockElement.innerHTML = stock > 0 ? '<i class="bi bi-check-circle me-1"></i>Còn ' + stock + ' sản phẩm' : '<i class="bi bi-x-circle me-1"></i>Hết hàng';
            if (this.dataset.image && mainImage?.tagName === 'IMG') {
                mainImage.src = this.dataset.image;
                document.querySelectorAll('.detail-thumb').forEach(item => item.classList.toggle('active', item.dataset.variation === this.value));
            }
            document.querySelectorAll('.detail-buy').forEach(button => {
                button.disabled = stock < 1;
            });
        });
    });
    document.querySelector('input[name="variation_id"]:checked')?.dispatchEvent(new Event('change'));

    document.querySelectorAll('[data-review-filter]').forEach(function (filter) {
        filter.addEventListener('click', function () {
            document.querySelectorAll('.review-filter').forEach(item => item.classList.remove('active'));
            this.classList.add('active');
            const selected = this.dataset.reviewFilter;
            document.querySelectorAll('[data-review-rating]').forEach(function (review) {
                const visible = selected === 'all'
                    || selected === 'comment'
                    || (selected === 'media' && review.dataset.reviewMedia === '1')
                    || review.dataset.reviewRating === selected;
                review.classList.toggle('d-none', !visible);
            });
        });
    });

    const reviewImageModal = document.getElementById('reviewImageModal');
    const reviewImagePreview = document.getElementById('reviewImagePreview');
    document.querySelectorAll('[data-review-image]').forEach(function (imageButton) {
        imageButton.addEventListener('click', function () {
            reviewImagePreview.src = this.dataset.reviewImage;
            reviewImageModal.classList.add('is-open');
            reviewImageModal.setAttribute('aria-hidden', 'false');
        });
    });
    reviewImageModal?.addEventListener('click', function (event) {
        if (!event.target.closest('.review-image-preview')) {
            window.closeReviewImage(event);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && reviewImageModal?.classList.contains('is-open')) {
            window.closeReviewImage(event);
        }
    });

    window.closeReviewImage = function (event) {
        event?.stopPropagation();
        if (!reviewImageModal) return;
        reviewImageModal.classList.remove('is-open');
        reviewImageModal.setAttribute('aria-hidden', 'true');
        reviewImagePreview?.removeAttribute('src');
    };

    document.querySelectorAll('[data-quantity-step]').forEach(function (button) {
        button.addEventListener('click', function () {
            const step = Number(this.dataset.quantityStep);
            const next = Math.max(Number(quantityInput.min), Math.min(Number(quantityInput.max), Number(quantityInput.value || 1) + step));
            quantityInput.value = next;
        });
    });

    document.getElementById('copy-product-link')?.addEventListener('click', async function () {
        await navigator.clipboard.writeText(window.location.href);
        const original = this.innerHTML;
        this.innerHTML = '<i class="bi bi-check2 me-1"></i>Đã sao chép liên kết';
        setTimeout(() => this.innerHTML = original, 1800);
    });
});
</script>
@endsection