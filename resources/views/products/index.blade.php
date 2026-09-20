@extends('layouts.app')
@section('title', 'Mỹ phẩm & chăm sóc cá nhân - Aloha Beauty')

@section('content')
<!-- LỜI CHÀO -->
<div class="text-center mb-5 animate__animated animate__fadeInDown">
    <h2 class="fw-bold product-page-title">ALOHA! KHÁM PHÁ SẢN PHẨM</h2>
    <p class="text-muted">Khám phá mỹ phẩm và sản phẩm chăm sóc cá nhân dành cho bạn</p>
</div>

<!-- DANH MỤC SẢN PHẨM -->
<section class="product-categories mb-5" aria-labelledby="product-categories-title">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 id="product-categories-title" class="fw-bold mb-0 storefront-title">Danh mục sản phẩm</h4>
        <a href="{{ route('products.index') }}" class="small text-decoration-none category-view-all">Xem tất cả</a>
    </div>
    <div class="row g-3">
        @foreach($categories as $category)
            <div class="col-6 col-md-3">
                <a href="{{ route('products.index', ['category' => $category->id]) }}" class="category-card {{ (string) request('category') === (string) $category->id ? 'active' : '' }}">
                    <span class="category-card-icon"><i class="bi bi-bag-heart"></i></span>
                    <span class="category-card-name">{{ $category->name }}</span>
                    <small>{{ $category->products_count }} sản phẩm</small>
                </a>
            </div>
        @endforeach
    </div>
</section>

<form method="GET" action="{{ route('products.index') }}" class="card border-0 shadow-sm p-3 mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-bold">Danh mục</label>
            <select name="category" class="form-select">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold">Giá từ</label>
            <input type="number" name="min_price" class="form-control" min="0" value="{{ request('min_price') }}" placeholder="0">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold">Giá đến</label>
            <input type="number" name="max_price" class="form-control" min="0" value="{{ request('max_price') }}" placeholder="Không giới hạn">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold">Đánh giá tối thiểu</label>
            <select name="rating" class="form-select">
                <option value="">Tất cả</option>
                @for($rating = 5; $rating >= 1; $rating--)
                    <option value="{{ $rating }}" @selected((string) request('rating') === (string) $rating)>{{ $rating }} sao trở lên</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Sắp xếp</label>
            <select name="sort" class="form-select">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Mới nhất</option>
                <option value="price_asc" @selected(request('sort') === 'price_asc')>Giá tăng dần</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>Giá giảm dần</option>
                <option value="rating_desc" @selected(request('sort') === 'rating_desc')>Đánh giá cao nhất</option>
            </select>
        </div>
        <div class="col-12 d-flex gap-2">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button class="btn btn-primary rounded-pill px-4"><i class="bi bi-funnel me-1"></i>Áp dụng</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Xóa lọc</a>
        </div>
    </div>
</form>

<!-- DANH SÁCH SẢN PHẨM -->
<div class="row g-4">
    @forelse($products as $product)
    <div class="col-lg-3 col-md-4 col-sm-6">
        @php
            $variationPrices = $product->variations->pluck('price')->map(fn ($price) => (float) $price);
            $displayMinPrice = $variationPrices->isNotEmpty() ? $variationPrices->min() : (float) $product->price;
            $displayMaxPrice = $variationPrices->isNotEmpty() ? $variationPrices->max() : (float) $product->price;
        @endphp
        <div class="card product-card text-center h-100 shadow-sm">
            <div class="card-body p-4 d-flex flex-column">
                
                <div class="mb-3">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded product-list-image">
                    @else
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2 product-icon-placeholder">
                            <i class="bi bi-bag-heart text-primary fs-3"></i>
                        </div>
                    @endif
                </div>
                
                <span class="badge text-primary rounded-pill mb-2 mx-auto product-category-badge">
                    {{ $product->category->name ?? 'Mỹ phẩm chăm sóc da' }}
                </span>

                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <h5 class="fw-bold text-dark mb-0">{{ $product->name }}</h5>
                    @auth
                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $wishlistProductIds->contains($product->id) ? 'btn-danger' : 'btn-outline-danger' }} rounded-circle" title="{{ $wishlistProductIds->contains($product->id) ? 'Bỏ khỏi yêu thích' : 'Lưu vào yêu thích' }}" aria-label="{{ $wishlistProductIds->contains($product->id) ? 'Bỏ khỏi yêu thích' : 'Lưu vào yêu thích' }}"><i class="bi bi-heart{{ $wishlistProductIds->contains($product->id) ? '-fill' : '' }}"></i></button>
                        </form>
                    @endauth
                </div>
                <p class="text-muted small mb-3 flex-grow-1 product-description">
                    {{ $product->description ?? 'Sản phẩm chăm sóc cá nhân chất lượng cho vẻ đẹp rạng ngời mỗi ngày.' }}
                </p>

                <h5 class="fw-bold text-danger mb-1">
                    @if($displayMinPrice < $displayMaxPrice)
                        {{ number_format($displayMinPrice, 0, ',', '.') }} - {{ number_format($displayMaxPrice, 0, ',', '.') }} ₫
                    @else
                        {{ number_format($displayMinPrice, 0, ',', '.') }} ₫
                    @endif
                </h5>
                <div class="small text-warning mb-2">
                    <i class="bi bi-star-fill"></i>
                    {{ $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : 'Chưa có' }}
                    @if($product->reviews_avg_rating)<span class="text-muted">/ 5</span>@endif
                </div>
                <p class="text-muted small mb-3"><i class="bi bi-box-seam me-1"></i>Còn lại: {{ $product->quantity > 0 ? $product->quantity : 'Hết hàng' }}</p>

                <a href="{{ route('products.show', $product->id) }}" class="btn btn-cyan w-100 rounded-pill py-2 mt-auto">KHÁM PHÁ NGAY</a>
            </div>
        </div>
    </div>
    @empty
    <!-- Hiển thị khi không tìm thấy kết quả -->
    <div class="col-12 text-center py-5">
        <i class="bi bi-search text-muted mb-3 empty-search-icon"></i>
        <h4 class="text-muted">Không tìm thấy sản phẩm nào phù hợp với từ khóa của bạn.</h4>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary mt-3 rounded-pill px-4">Xem tất cả sản phẩm</a>
    </div>
    @endforelse
</div>

<!-- THANH CHUYỂN TRANG -->
<div class="d-flex justify-content-center mt-5 mb-4 custom-pagination">
    {{ $products->links('pagination::bootstrap-5') }}
</div>


@endsection