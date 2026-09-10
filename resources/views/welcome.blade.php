@extends('layouts.app')
@section('title', 'Trang chủ - Aloha Beauty')

@section('content')
@auth
    @if(Auth::user()->role === 'admin')
        <style>
            .home-admin-chat-dock { position: fixed; z-index: 1040; top: 52%; right: 0; display: flex; align-items: center; gap: .55rem; padding: .75rem .9rem .75rem .8rem; color: #fff; text-decoration: none; background: linear-gradient(135deg, #183b56, #1686a0); border-radius: 14px 0 0 14px; box-shadow: 0 8px 22px rgba(24,59,86,.24); transform: translateY(-50%); }
            .home-admin-chat-dock:hover { color: #fff; padding-right: 1.2rem; }
            .home-admin-chat-dock i { font-size: 1.15rem; }
            .home-admin-chat-dock span { font-size: .78rem; font-weight: 800; }
            .home-admin-chat-dock .home-chat-badge { position: absolute; top: -7px; left: -7px; min-width: 20px; padding: .2rem .35rem; color: #fff; background: #e63950; border: 2px solid #fff; border-radius: 999px; font-size: .65rem; text-align: center; }
        </style>
        <a class="home-admin-chat-dock" href="{{ route('admin.chat.index') }}" title="Mở trung tâm chat khách hàng">
            <i class="bi bi-chat-square-text-fill"></i><span>Chat hỗ trợ</span><b id="home-chat-badge" class="home-chat-badge d-none">0</b>
        </a>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const badge = document.getElementById('home-chat-badge');
                const refreshChatBadge = () => fetch('/chat/users').then(response => response.json()).then(users => {
                    const total = users.reduce((sum, user) => sum + Number(user.unread_messages_count || 0), 0);
                    badge.textContent = total > 99 ? '99+' : total;
                    badge.classList.toggle('d-none', total === 0);
                }).catch(() => {});
                refreshChatBadge();
                setInterval(refreshChatBadge, 15000);
            });
        </script>
    @endif
@endauth
<!-- BANNER TRƯỢT TỰ ĐỘNG (CAROUSEL) -->
<div id="heroCarousel" class="carousel slide hero-carousel mb-5 animate__animated animate__fadeInDown" data-bs-ride="carousel" data-bs-interval="4000" data-bs-wrap="true">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="4"></button>
    </div>

    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?q=80&w=2000&auto=format&fit=crop" alt="Bộ sưu tập mỹ phẩm Aloha Beauty">
            <div class="carousel-caption">
                <span class="badge bg-warning text-dark mb-2 px-3 py-2 fs-6 rounded-pill">Bộ sưu tập mới</span>
                <h1>Beauty Essentials</h1>
                <p class="mb-4 w-50 d-none d-md-block">Những sản phẩm làm đẹp thiết yếu giúp bạn chăm sóc làn da và tỏa sáng mỗi ngày.</p>
                <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold me-2">Mua ngay <i class="bi bi-chevron-right"></i></a>
                <a href="{{ route('products.show', 1) }}" class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold">Xem chi tiết</a>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1556228578-8c89e6adf883?q=80&w=2000&auto=format&fit=crop" alt="Chăm sóc da">
            <div class="carousel-caption">
                <span class="badge bg-danger mb-2 px-3 py-2 fs-6 rounded-pill">Được yêu thích</span>
                <h1>Skincare Ritual</h1>
                <p class="mb-4 w-50 d-none d-md-block">Xây dựng chu trình chăm sóc da dịu lành, hiệu quả với những thành phần được chọn lọc.</p>
                <a href="{{ route('products.index', ['category' => 1]) }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold me-2">Khám phá ngay <i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?q=80&w=2000&auto=format&fit=crop" alt="Chăm sóc cá nhân">
            <div class="carousel-caption">
                <span class="badge bg-info text-dark mb-2 px-3 py-2 fs-6 rounded-pill">Ưu đãi hôm nay</span>
                <h1>Self-care Moment</h1>
                <p class="mb-4 w-50 d-none d-md-block">Tận hưởng những phút giây chăm sóc bản thân với sản phẩm lành tính và tiện dụng.</p>
                <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold me-2">Săn ưu đãi <i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <!-- Slide 4 -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?q=80&w=2000&auto=format&fit=crop" alt="Chăm sóc tóc">
            <div class="carousel-caption">
                <span class="badge bg-success mb-2 px-3 py-2 fs-6 rounded-pill">Hair care edit</span>
                <h1>Tóc mềm, mood xinh</h1>
                <p class="mb-4 w-50 d-none d-md-block">Khám phá routine chăm sóc tóc nhẹ nhàng, thơm dịu và phù hợp cho mỗi ngày.</p>
                <a href="{{ route('products.index', ['category' => 3]) }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold me-2">Xem chăm sóc tóc <i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <!-- Slide 5 -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1556228720-195a672e8a03?q=80&w=2000&auto=format&fit=crop" alt="Routine chăm sóc da">
            <div class="carousel-caption">
                <span class="badge bg-warning text-dark mb-2 px-3 py-2 fs-6 rounded-pill">Routine mới mỗi ngày</span>
                <h1>Chăm mình thật dịu</h1>
                <p class="mb-4 w-50 d-none d-md-block">Chọn những món nhỏ xinh để biến vài phút skincare thành khoảng thời gian dành riêng cho bạn.</p>
                <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold me-2">Khám phá bộ sưu tập <i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon welcome-carousel-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon welcome-carousel-icon" aria-hidden="true"></span>
    </button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hero = document.getElementById('heroCarousel');
    if (hero && window.bootstrap?.Carousel) {
        bootstrap.Carousel.getOrCreateInstance(hero, {
            interval: 4000,
            ride: 'carousel',
            wrap: true,
            pause: false,
            touch: true
        }).cycle();
    }
});
</script>
@endpush

<!-- LỜI CHÀO -->
<div class="text-center mb-5 animate__animated animate__fadeInUp">
    <h2 class="fw-bold product-page-title">ALOHA! MÙA HÈ RỰC RỠ</h2>
    <p class="text-muted">Khám phá không gian mua sắm thư giãn cho làn da và cơ thể</p>
</div>

<!-- DANH MỤC SẢN PHẨM -->
<section class="product-categories mb-5" aria-labelledby="home-categories-title">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 id="home-categories-title" class="fw-bold mb-0 storefront-title">Khám phá theo danh mục</h4>
        <a href="{{ route('products.index') }}" class="small text-decoration-none category-view-all">Xem tất cả</a>
    </div>
    <div class="row g-3">
        @foreach($categories as $category)
            <div class="col-6 col-md-3">
                <a href="{{ route('products.index', ['category' => $category->id]) }}" class="category-card">
                    <span class="category-card-icon"><i class="bi bi-bag-heart"></i></span>
                    <span class="category-card-name">{{ $category->name }}</span>
                    <small>{{ $category->products_count }} sản phẩm</small>
                </a>
            </div>
        @endforeach
    </div>
</section>

<!-- SAN PHAM HOT -->
<section class="hot-products-section mb-5" aria-labelledby="hot-products-title">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div><span class="hot-products-kicker"><i class="bi bi-fire me-1"></i> ĐANG ĐƯỢC QUAN TÂM</span><h3 id="hot-products-title" class="fw-bold mb-0 storefront-title">Sản phẩm hot hôm nay</h3></div>
        <div class="d-flex gap-2"><button type="button" class="btn btn-light border rounded-circle hot-scroll-button" data-direction="-1" aria-label="Xem sản phẩm trước"><i class="bi bi-arrow-left"></i></button><button type="button" class="btn btn-light border rounded-circle hot-scroll-button" data-direction="1" aria-label="Xem sản phẩm tiếp theo"><i class="bi bi-arrow-right"></i></button></div>
    </div>
    <div id="hot-products-track" class="hot-products-track">
        @foreach($hotProducts as $hotProduct)
            <a href="{{ route('products.show', $hotProduct->id) }}" class="hot-product-card">
                <div class="hot-product-image">@if($hotProduct->image)<img src="{{ asset('storage/' . $hotProduct->image) }}" alt="{{ $hotProduct->name }}">@else<i class="bi bi-bag-heart"></i>@endif</div>
                <div class="p-3"><span class="badge bg-info-subtle text-info-emphasis rounded-pill mb-2">{{ $hotProduct->category->name ?? 'Beauty' }}</span><h5>{{ $hotProduct->name }}</h5><strong>{{ number_format($hotProduct->price, 0, ',', '.') }} ₫</strong></div>
            </a>
        @endforeach
    </div>
</section>

<!-- DANH SÁCH SẢN PHẨM -->
<div class="row g-4 justify-content-center">
    @foreach($products as $product)
    <div class="col-lg-3 col-md-4 col-sm-6">
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

                <h5 class="fw-bold text-dark mb-2">{{ $product->name }}</h5>
                <p class="text-muted small mb-3 flex-grow-1 product-description">
                    {{ $product->description ?? 'Sản phẩm chăm sóc cá nhân chất lượng cho vẻ đẹp rạng ngời mỗi ngày.' }}
                </p>

                <h5 class="fw-bold text-danger mb-1">{{ number_format($product->price, 0, ',', '.') }} ₫</h5>
                <p class="text-muted small mb-3"><i class="bi bi-box-seam me-1"></i>Còn lại: {{ $product->quantity > 0 ? $product->quantity : 'Hết hàng' }}</p>

                <a href="{{ route('products.show', $product->id) }}" class="btn btn-cyan w-100 rounded-pill py-2 mt-auto">KHÁM PHÁ NGAY</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const track = document.getElementById('hot-products-track');
    document.querySelectorAll('.hot-scroll-button').forEach(button => button.addEventListener('click', function () {
        track.scrollBy({left: Number(this.dataset.direction) * 300, behavior: 'smooth'});
    }));
    if (track) setInterval(() => track.scrollBy({left: track.clientWidth * .75, behavior: 'smooth'}), 5000);
});
</script>
@endsection