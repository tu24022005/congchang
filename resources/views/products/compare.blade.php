@extends('layouts.app')
@section('title', 'So sánh sản phẩm - BeatyCare 🌸')
@section('canonical', route('products.compare'))

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Sản phẩm</a></li>
                    <li class="breadcrumb-item active" aria-current="page">So sánh sản phẩm</li>
                </ol>
            </nav>
            <h1 class="fw-bold fs-3 text-dark mb-0">So sánh chi tiết sản phẩm ⚖️</h1>
        </div>
        <div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="bi bi-plus-lg me-1"></i>Thêm sản phẩm khác
            </a>
            <button type="button" class="btn btn-outline-danger rounded-pill btn-sm px-3 ms-2" onclick="window.clearCompareList()">
                <i class="bi bi-trash3 me-1"></i>Xóa danh sách
            </button>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-5">
            <x-empty-state
                type="search"
                title="Chưa có sản phẩm nào để so sánh"
                description="Hãy chọn ít nhất 2 sản phẩm từ danh mục hoặc trang sản phẩm để đặt lên bàn cân so sánh chi tiết nhé."
                action-label="Khám phá sản phẩm ngay"
                action-url="{{ route('products.index') }}"
            />
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 compare-matrix-table">
                    <thead class="table-light">
                        <tr>
                            <th class="view-inline-1 fw-bold text-dark sticky-col">Đặc tính / Sản phẩm</th>
                            @foreach($products as $product)
                                <th data-inline-columns="{{ $products->count() }}" class="inline-dynamic-columns text-center bg-white">
                                    <div class="position-relative d-inline-block mb-3 view-inline-2">
                                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/placeholder.svg') }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover rounded-3 shadow-sm" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                                    </div>
                                    <h2 class="fs-6 fw-bold text-dark mb-2 text-truncate" title="{{ $product->name }}">
                                        <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="text-decoration-none text-dark">
                                            {{ $product->name }}
                                        </a>
                                    </h2>
                                    <div class="text-danger fw-bold fs-5 mb-2">
                                        {{ number_format($product->effectivePrice(), 0, ',', '.') }} ₫
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill btn-quick-add px-3" data-quick-add-id="{{ $product->id }}" data-quick-add-slug="{{ $product->slug }}" data-has-variations="{{ $product->variations->isNotEmpty() ? 'true' : 'false' }}">
                                        <i class="bi bi-bag-plus me-1"></i>Thêm vào giỏ
                                    </button>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold text-muted sticky-col bg-light-subtle">Thương hiệu</td>
                            @foreach($products as $product)
                                <td class="text-center">{{ $product->brand->name ?? 'BeatyCare Chính Hãng' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted sticky-col bg-light-subtle">Danh mục</td>
                            @foreach($products as $product)
                                <td class="text-center">{{ $product->category->name ?? 'Mỹ phẩm' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted sticky-col bg-light-subtle">Đánh giá trung bình</td>
                            @foreach($products as $product)
                                <td class="text-center">
                                    @php $avgRating = $product->reviews->where('is_visible', true)->avg('rating'); @endphp
                                    @if($avgRating)
                                        <span class="text-warning"><i class="bi bi-star-fill"></i> {{ number_format($avgRating, 1) }}/5</span>
                                        <small class="text-muted">({{ $product->reviews->where('is_visible', true)->count() }} bài)</small>
                                    @else
                                        <span class="text-muted">Chưa có đánh giá</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted sticky-col bg-light-subtle">Tình trạng tồn kho</td>
                            @foreach($products as $product)
                                <td class="text-center">
                                    @if($product->quantity > 0)
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Còn hàng ({{ $product->quantity }})</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1">Hết hàng</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted sticky-col bg-light-subtle">Loại da phù hợp</td>
                            @foreach($products as $product)
                                <td class="text-center">
                                    @if(!empty($product->skin_types))
                                        <div class="d-flex flex-wrap justify-content-center gap-1">
                                            @foreach((array) $product->skin_types as $skin)
                                                <span class="badge bg-info-subtle text-info-emphasis rounded-pill">{{ $skin }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">Mọi loại da</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted sticky-col bg-light-subtle">Dung tích / Phân loại</td>
                            @foreach($products as $product)
                                <td class="text-center">
                                    @if($product->variations->isNotEmpty())
                                        <div class="small text-muted">
                                            {{ $product->variations->map(fn($v) => collect([$v->color, $v->size_value ? $v->size_value.$v->size_unit : null, $v->storage])->filter()->implode(' · '))->filter()->implode(', ') }}
                                        </div>
                                    @else
                                        <span class="text-muted">Quy cách chuẩn</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted sticky-col bg-light-subtle">Hạn sử dụng & Xuất xứ</td>
                            @foreach($products as $product)
                                <td class="text-center small">
                                    <div><strong>HSD:</strong> {{ $product->expiry_info ?? '36 tháng kể từ NSX' }}</div>
                                    <div class="text-muted"><strong>Xuất xứ:</strong> {{ $product->origin ?? 'Chính hãng' }}</div>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted sticky-col bg-light-subtle">Bảng thành phần</td>
                            @foreach($products as $product)
                                <td class="small text-muted view-inline-3">
                                    {{ $product->ingredients ?? 'Chiết xuất từ thiên nhiên lành tính, an toàn và dịu nhẹ cho làn da.' }}
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="{{ asset_v('js/views/products-compare-blade-php.js') }}" defer></script>
@endpush
@endsection
