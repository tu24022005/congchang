@extends('layouts.app')
@section('title', 'Tất cả sản phẩm - BeatyCare 🌸')
@section('canonical', route('products.index'))
@if(request()->filled('search') || request()->has('page'))
    @push('head')
        <meta name="robots" content="noindex,follow">
    @endpush
@endif

@section('content')
<!-- LỜI CHÀO -->
<div class="text-center mb-4 animate__animated animate__fadeInDown">
    <h1 class="fw-bold product-page-title fs-2">BEATYCARE 🌸 KHÁM PHÁ SẢN PHẨM</h1>
    <p class="text-muted">Khám phá mỹ phẩm và sản phẩm chăm sóc cá nhân chính hãng dành cho bạn</p>
</div>

<!-- DANH MỤC SẢN PHẨM NHANH -->
<section class="product-categories mb-4" aria-labelledby="product-categories-title">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 id="product-categories-title" class="fw-bold mb-0 storefront-title fs-5">Danh mục sản phẩm</h2>
        <a href="{{ route('products.index') }}" class="small text-decoration-none category-view-all">Xem tất cả</a>
    </div>
    <div class="row g-3 reveal-stagger">
        @foreach($categories as $category)
            <div class="col-6 col-md-3">
                <a href="{{ route('products.index', ['category' => $category->id]) }}" class="category-card {{ (string) request('category') === (string) $category->id ? 'active' : '' }}">
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

<!-- NÚT MỞ BỘ LỌC TRÊN MOBILE -->
<div class="d-md-none mb-3 d-flex justify-content-between align-items-center">
    <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-semibold" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterOffcanvas" aria-controls="mobileFilterOffcanvas">
        <i class="bi bi-sliders me-1"></i> Bộ lọc & Sắp xếp
    </button>
    <span class="text-muted small fw-semibold" id="mobile-product-count">Tìm thấy {{ $products->total() }} sản phẩm</span>
</div>

<!-- FORM BỘ LỌC (DESKTOP BAR + MOBILE OFFCANVAS) -->
<div class="offcanvas-md offcanvas-start" tabindex="-1" id="mobileFilterOffcanvas" aria-labelledby="mobileFilterLabel">
    <div class="offcanvas-header d-md-none border-bottom">
        <h5 class="offcanvas-title fw-bold" id="mobileFilterLabel"><i class="bi bi-funnel text-danger me-2"></i>Bộ lọc sản phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#mobileFilterOffcanvas" aria-label="Đóng"></button>
    </div>
    <div class="offcanvas-body p-md-0">
        <form id="ajax-filter-form" method="GET" action="{{ route('products.index') }}" class="card border-0 shadow-sm p-3 p-md-4 mb-4 w-100 rounded-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-dark">Từ khóa tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" name="search" id="filter-search" class="form-control" value="{{ request('search') }}" maxlength="120" placeholder="Tên, mã SP, thương hiệu...">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-dark">Danh mục</label>
                    <select name="category" id="filter-category" class="form-select">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-dark">Thương hiệu</label>
                    <select name="brand" id="filter-brand" class="form-select">
                        <option value="">Tất cả thương hiệu</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected((string) request('brand') === (string) $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-dark">Sắp xếp</label>
                    <select name="sort" id="filter-sort" class="form-select">
                        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Mới nhất</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Giá tăng dần</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Giá giảm dần</option>
                        <option value="rating_desc" @selected(request('sort') === 'rating_desc')>Đánh giá cao nhất</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-dark">Tình trạng</label>
                    <select name="availability" id="filter-availability" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="in_stock" @selected(request('availability') === 'in_stock')>Còn hàng</option>
                        <option value="out_of_stock" @selected(request('availability') === 'out_of_stock')>Hết hàng</option>
                    </select>
                </div>
                
                <!-- DÒNG KHOẢNG GIÁ & ĐÁNH GIÁ -->
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1 d-flex justify-content-between">
                        <span>Khoảng giá (VNĐ)</span>
                        <span class="text-danger small" id="price-slider-label"></span>
                    </label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" name="min_price" id="filter-min-price" class="form-control form-control-sm" min="0" step="50000" value="{{ request('min_price') }}" placeholder="Từ 0đ">
                        <span class="text-muted">–</span>
                        <input type="number" name="max_price" id="filter-max-price" class="form-control form-control-sm" min="0" step="50000" value="{{ request('max_price') }}" placeholder="Đến tối đa">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-bold text-dark">Đánh giá tối thiểu</label>
                    <select name="rating" id="filter-rating" class="form-select form-select-sm">
                        <option value="">Tất cả đánh giá</option>
                        @for($rating = 5; $rating >= 1; $rating--)
                            <option value="{{ $rating }}" @selected((string) request('rating') === (string) $rating)>{{ $rating }} sao trở lên</option>
                        @endfor
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1 btn-sm fw-semibold">
                        <i class="bi bi-funnel me-1"></i>Lọc
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3" id="btn-reset-filters">
                        Đặt lại
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ACTIVE FILTER CHIPS -->
<div id="filter-chips-container" class="mb-3 d-flex flex-wrap align-items-center gap-2">
    <span class="text-muted small fw-semibold me-1 d-none d-md-inline" id="desktop-product-count">
        Tìm thấy <strong class="text-danger" id="total-count-display">{{ $products->total() }}</strong> sản phẩm
    </span>
    <div id="active-chips" class="d-inline-flex flex-wrap gap-1">
        <!-- Rendered by JS -->
    </div>
</div>

<!-- DANH SÁCH SẢN PHẨM (LƯỚI AJAX) -->
<div id="products-grid-container" class="row g-4 reveal-stagger transition-fade">
    @include('products._grid', ['products' => $products, 'wishlistProductIds' => $wishlistProductIds])
</div>

<!-- NÚT TẢI THÊM (LOAD MORE) -->
<div class="text-center mt-5 mb-4" id="load-more-section">
    @if($products->hasMorePages())
        <button type="button" id="btn-load-more" class="btn btn-outline-danger rounded-pill px-5 py-2 fw-semibold shadow-sm" data-next-page="{{ $products->currentPage() + 1 }}">
            <span class="load-more-text"><i class="bi bi-arrow-down-circle me-2"></i>Xem thêm sản phẩm</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
    @endif
</div>

<!-- PHÂN TRANG ẨN (SEO & NO-JS FALLBACK) -->
<noscript>
    <div class="d-flex justify-content-center mt-4 custom-pagination">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</noscript>
<div id="seo-pagination" class="visually-hidden">
    {{ $products->links('pagination::bootstrap-5') }}
</div>

@push('scripts')
<script src="{{ asset_v('js/views/products-index-blade-php.js') }}" defer></script>
@endpush
@endsection