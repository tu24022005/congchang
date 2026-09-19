@extends('layouts.app')
@section('title', 'Sản phẩm yêu thích')

@section('content')
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <span class="text-danger fw-bold small text-uppercase">Bộ sưu tập riêng của bạn</span>
        <h1 class="fw-bold mb-1">Sản phẩm yêu thích</h1>
        <p class="text-muted mb-0">Lưu lại những món bạn muốn mua vào một ngày thật phù hợp.</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill"><i class="bi bi-bag-heart me-1"></i>Khám phá sản phẩm</a>
</div>

<div class="row g-4">
    @forelse($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card product-card text-center h-100 shadow-sm">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="mb-3">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded product-list-image">
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2 product-icon-placeholder"><i class="bi bi-bag-heart text-primary fs-3"></i></div>
                        @endif
                    </div>
                    <span class="badge text-primary rounded-pill mb-2 mx-auto product-category-badge">{{ $product->category->name ?? 'Mỹ phẩm chăm sóc da' }}</span>
                    <h5 class="fw-bold text-dark mb-2">{{ $product->name }}</h5>
                    <p class="text-muted small mb-3 flex-grow-1">{{ $product->description ?? 'Sản phẩm chăm sóc cá nhân chất lượng cho vẻ đẹp rạng ngời mỗi ngày.' }}</p>
                    <h5 class="fw-bold text-danger mb-3">{{ number_format($product->price, 0, ',', '.') }} ₫</h5>
                    <div class="d-flex gap-2 mt-auto">
                        <a href="{{ route('products.show', $product) }}" class="btn btn-cyan flex-grow-1 rounded-pill py-2">XEM SẢN PHẨM</a>
                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST">@csrf<button type="submit" class="btn btn-danger rounded-circle" title="Bỏ khỏi yêu thích" aria-label="Bỏ khỏi yêu thích"><i class="bi bi-heart-fill"></i></button></form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5"><i class="bi bi-heart text-muted fs-1"></i><h4 class="text-muted mt-3">Danh sách yêu thích đang trống.</h4><a href="{{ route('products.index') }}" class="btn btn-outline-primary mt-2 rounded-pill">Tìm sản phẩm yêu thích</a></div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-5">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection
