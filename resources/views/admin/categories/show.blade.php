@extends('admin.layouts.app')
@section('title', 'Chi tiết danh mục')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Chi tiết Danh mục</h4>
            </div>
            
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold text-muted">ID:</label>
                    <p class="fs-5">{{ $category->id }}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-muted">Tên danh mục:</label>
                    <p class="fs-5 fw-bold">{{ $category->name }}</p>
                </div>

                <!-- HIỂN THỊ ẢNH TO Ở TRANG CHI TIẾT -->
                <div class="mb-3">
                    <label class="fw-bold text-muted d-block mb-2">Hình ảnh:</label>
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="Ảnh danh mục" class="img-fluid rounded border admin-category-image">
                    @else
                        <p class="text-muted fst-italic">Danh mục này chưa có hình ảnh.</p>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-muted">Ngày tạo:</label>
                    <p>{{ $category->created_at }}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-muted">Cập nhật lần cuối:</label>
                    <p>{{ $category->updated_at }}</p>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning">Chỉnh sửa</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center mt-4">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
                <div class="card-header bg-light d-flex justify-content-between align-items-center gap-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-box-seam text-primary me-2"></i>
                    Sản phẩm thuộc danh mục: {{ $category->name }}
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-pill">{{ $category->products->count() }} sản phẩm</span>
                    <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}" class="btn btn-sm btn-primary rounded-pill">
                        <i class="bi bi-plus-lg me-1"></i>Thêm sản phẩm
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá bán</th>
                                <th>Tồn kho</th>
                                <th class="text-end pe-4">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($category->products as $product)
                                <tr>
                                    <td class="ps-4">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="admin-product-list-image">
                                        @else
                                            <div class="admin-product-list-placeholder">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $product->name }}</td>
                                    <td class="text-danger fw-bold">{{ number_format($product->price, 0, ',', '.') }} đ</td>
                                    <td>{{ $product->quantity ?? 0 }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-outline-info rounded-pill" title="Xem sản phẩm">
                                            <i class="bi bi-eye me-1"></i>Xem
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-box-seam fs-3 d-block mb-2"></i>
                                        Danh mục này chưa có sản phẩm.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection