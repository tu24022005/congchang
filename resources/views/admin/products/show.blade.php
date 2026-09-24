@extends('admin.layouts.app')
@section('title', 'Chi tiết sản phẩm')

@section('content')
<style>
    .product-overview { --ink: #203047; --line: #e6ebf2; }
    .overview-hero { background: linear-gradient(135deg, #183b56, #267d8f); color: #fff; border-radius: 18px; padding: 1.5rem 1.7rem; }
    .overview-panel { border: 1px solid var(--line); border-radius: 16px; background: #fff; box-shadow: 0 12px 30px rgba(32, 48, 71, .07); }
    .main-product-image { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 16px; background: #f4f7fa; }
    .overview-thumb { width: 74px; height: 74px; object-fit: cover; border-radius: 10px; border: 2px solid transparent; cursor: pointer; }
    .overview-thumb:hover, .overview-thumb.active { border-color: #27a7bd; }
    .metric-card { border: 1px solid var(--line); border-radius: 14px; padding: 1rem; height: 100%; }
    .metric-label { color: #718096; font-size: .78rem; text-transform: uppercase; font-weight: 800; letter-spacing: .04em; }
    .detail-label { width: 34%; color: #718096; font-size: .85rem; }
    .review-stars { color: #f59e0b; letter-spacing: .08em; }
</style>

<div class="product-overview py-2">
    <div class="overview-hero d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="small text-white-50 mb-1"><i class="bi bi-box-seam me-1"></i> KHO / CHI TIẾT SẢN PHẨM #{{ $product->id }}</div>
            <h1 class="h3 fw-bold mb-1">{{ $product->name }}</h1>
            <p class="mb-0 text-white-50">Thông tin tổng quan, tồn kho và bộ sưu tập ảnh.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-light rounded-pill"><i class="bi bi-arrow-left me-1"></i>Kho sản phẩm</a>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-light rounded-pill"><i class="bi bi-pencil-square me-1"></i>Chỉnh sửa</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="overview-panel p-3">
                @if($product->image)
                    <img id="main-product-image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="main-product-image">
                @else
                    <div id="main-product-image" class="main-product-image d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image fs-1"></i></div>
                @endif
                @if($product->images->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @if($product->image)<img class="overview-thumb active" src="{{ asset('storage/' . $product->image) }}" data-image="{{ asset('storage/' . $product->image) }}" alt="Ảnh chính">@endif
                        @foreach($product->images as $image)
                            <img class="overview-thumb" src="{{ asset('storage/' . $image->image_path) }}" data-image="{{ asset('storage/' . $image->image_path) }}" alt="Ảnh gallery">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-7">
            <div class="overview-panel p-4 h-100">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div><span class="badge bg-info-subtle text-info-emphasis rounded-pill mb-2">{{ $product->category->name ?? 'Chưa phân loại' }}</span>@if($product->brand)<span class="badge bg-light text-dark rounded-pill mb-2 ms-1">{{ $product->brand->name }}</span>@endif<h2 class="h4 fw-bold mb-1">{{ $product->name }}</h2><small class="text-muted">Mã sản phẩm: {{ $product->product_code ?: '#' . $product->id }}</small></div>
                    <span class="badge rounded-pill {{ $product->quantity > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">{{ $product->quantity > 0 ? 'Đang bán' : 'Hết hàng' }}</span>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6"><div class="metric-card"><div class="metric-label">Giá bán</div><div class="fs-4 fw-bold text-danger mt-1">{{ number_format($product->price, 0, ',', '.') }} đ</div></div></div>
                    <div class="col-sm-6"><div class="metric-card"><div class="metric-label">Tồn kho</div><div class="fs-4 fw-bold {{ $product->quantity > 0 ? 'text-success' : 'text-danger' }} mt-1">{{ number_format($product->quantity) }} <small class="fs-6">sản phẩm</small></div></div></div>
                </div>
                <div class="mb-4"><h3 class="h6 fw-bold border-bottom pb-2">Mô tả</h3><p class="text-muted mb-0">{{ $product->description ?: 'Sản phẩm chưa có mô tả.' }}</p></div>
                <table class="table table-sm align-middle mb-0"><tbody>
                    <tr><td class="detail-label">Ngày tạo</td><td>{{ $product->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</td></tr>
                    <tr><td class="detail-label">Cập nhật gần nhất</td><td>{{ $product->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</td></tr>
                    <tr><td class="detail-label">Số ảnh gallery</td><td>{{ $product->images->count() }} ảnh</td></tr>
                    <tr><td class="detail-label">Số biến thể</td><td>{{ $product->variations->count() }} biến thể</td></tr>
                </tbody></table>
            </div>
        </div>
    </div>

    <div class="overview-panel p-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="h5 fw-bold mb-1">Biến thể sản phẩm</h2><small class="text-muted">SKU, màu sắc, khối lượng/dung tích, giá và tồn kho theo từng phiên bản.</small></div><span class="badge bg-light text-dark">{{ $product->variations->count() }} phiên bản</span></div>
        @if($product->variations->isNotEmpty())
            <div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>SKU</th><th>Biến thể</th><th>Giá</th><th>Tồn kho</th><th>Trạng thái</th></tr></thead><tbody>
                @foreach($product->variations as $variation)
                    @php $variationLabel = collect([$variation->color, $variation->size_value ? rtrim(rtrim($variation->size_value, '0'), '.') . $variation->size_unit : null, $variation->storage])->filter()->implode(' · '); @endphp
                    <tr><td class="fw-semibold">{{ $variation->sku ?: 'Chưa có SKU' }}</td><td>{{ $variationLabel ?: 'Mặc định' }}</td><td class="text-danger fw-semibold">{{ number_format($variation->price, 0, ',', '.') }} đ</td><td>{{ number_format($variation->stock) }}</td><td><span class="badge rounded-pill {{ $variation->stock > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $variation->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}</span></td></tr>
                @endforeach
            </tbody></table></div>
        @else
            <div class="text-center py-4 text-muted"><i class="bi bi-layers fs-2 d-block mb-2"></i>Sản phẩm này chưa có biến thể.</div>
        @endif
    </div>

    <div class="overview-panel p-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div><h2 class="h5 fw-bold mb-1">Đánh giá sản phẩm</h2><small class="text-muted">Nhận xét và số sao từ khách hàng đã mua hàng.</small></div>
            <span class="badge bg-warning-subtle text-warning-emphasis">{{ $product->reviews->count() }} đánh giá</span>
        </div>
        @if($product->reviews->isNotEmpty())
            @php $averageRating = round($product->reviews->avg('rating'), 1); @endphp
            <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-3">
                <strong class="fs-3 text-danger">{{ number_format($averageRating, 1) }}/5</strong>
                <span class="review-stars">{{ str_repeat('★', (int) round($averageRating)) }}<span class="text-muted">{{ str_repeat('★', 5 - (int) round($averageRating)) }}</span></span>
            </div>
            <div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Khách hàng</th><th>Đánh giá</th><th>Nội dung</th><th>Thời gian</th></tr></thead><tbody>
                @foreach($product->reviews as $review)
                    <tr><td class="fw-semibold">{{ $review->reviewer_name ?: $review->user?->name ?: 'Khách hàng' }}<small class="d-block text-success">Đã mua hàng</small></td><td><span class="review-stars">{{ str_repeat('★', $review->rating) }}<span class="text-muted">{{ str_repeat('★', 5 - $review->rating) }}</span></span><small class="d-block text-muted">{{ $review->rating }}/5</small></td><td>{{ $review->comment ?: 'Không có nhận xét.' }}@if($review->media_paths)<div class="d-flex gap-1 mt-2">@foreach($review->media_paths as $path)<img src="{{ asset('storage/' . $path) }}" alt="Ảnh đánh giá" style="width:48px;height:48px;object-fit:cover;border-radius:6px">@endforeach</div>@endif</td><td class="text-muted">{{ $review->created_at?->format('d/m/Y H:i') }}</td></tr>
                @endforeach
            </tbody></table></div>
        @else
            <div class="text-center py-4 text-muted"><i class="bi bi-chat-square-text fs-2 d-block mb-2"></i>Chưa có đánh giá nào cho sản phẩm này.</div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainImage = document.getElementById('main-product-image');
    document.querySelectorAll('.overview-thumb').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            if (mainImage.tagName === 'IMG') mainImage.src = this.dataset.image;
            document.querySelectorAll('.overview-thumb').forEach(item => item.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
@endsection
