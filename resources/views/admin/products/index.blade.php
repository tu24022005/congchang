@extends('layouts.app')
@section('title', 'Quản lý Sản phẩm')

@section('content')
<div class="container-fluid py-4">
    <!-- Tiêu đề & Nút Thêm mới -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Quản lý Sản phẩm</h2>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-circle me-1"></i> Thêm mới
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bảng Dữ liệu -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3" width="5%">STT</th>
                            <th class="py-3" width="10%">Hình ảnh</th>
                            <th class="py-3 text-start">Tên Sản phẩm</th>
                            <th class="py-3">Mã SP / Thương hiệu</th>
                            <th class="py-3">Danh mục</th>
                            <th class="py-3">Giá bán</th>
                            <th class="py-3" width="20%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $key => $product)
                            @php
                                $variationPrices = $product->variations->pluck('price')->map(fn ($price) => (float) $price);
                                $displayMinPrice = $variationPrices->isNotEmpty() ? $variationPrices->min() : (float) $product->price;
                                $displayMaxPrice = $variationPrices->isNotEmpty() ? $variationPrices->max() : (float) $product->price;
                            @endphp
                            <tr>
                                <td class="fw-bold text-muted">{{ $key + 1 }}</td>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail rounded-3 shadow-sm admin-thumb-image">
                                    @else
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted admin-thumb-placeholder">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-start fw-semibold fs-6 text-dark">{{ $product->name }}</td>
                                <td><strong>{{ $product->product_code ?: '—' }}</strong><small class="d-block text-muted">{{ $product->brand->name ?? 'Chưa có thương hiệu' }}</small></td>
                                <td><span class="badge bg-secondary rounded-pill px-3 py-2">{{ $product->category->name ?? 'N/A' }}</span></td>
                                <td class="text-danger fw-bold">
                                    @if($displayMinPrice < $displayMaxPrice)
                                        {{ number_format($displayMinPrice, 0, ',', '.') }} - {{ number_format($displayMaxPrice, 0, ',', '.') }} đ
                                    @else
                                        {{ number_format($displayMinPrice, 0, ',', '.') }} đ
                                    @endif
                                </td>
                                <td>
                                    <!-- Nút Hành động dạng Icon thu gọn -->
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(Auth::user()->role === 'admin')
                                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#variationStock{{ $product->id }}" title="Quản lý tồn kho từng mã loại"><i class="bi bi-boxes"></i></button>
                                        @endif
                                        @if(Auth::user()->role === 'admin')
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @if(Auth::user()->role === 'warehouse_staff')
                                <div class="modal fade" id="variationStock{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content border-0 rounded-4">
                                        <form action="{{ route('admin.products.variation-stock', $product) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <div class="modal-header"><h5 class="modal-title fw-bold">Tồn kho từng mã loại: {{ $product->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body"><p class="small text-muted">Nhập số tồn thực tế và số lượng hàng mới nhập. Giá bán không thể chỉnh ở vai trò kho.</p>
                                                @foreach($product->variations as $variation)
                                                    <div class="row g-2 align-items-center mb-3">
                                                        <div class="col-md-5"><strong>{{ $variation->sku ?: 'Mã loại #' . $variation->id }}</strong><small class="d-block text-muted">{{ collect([$variation->color, $variation->storage, $variation->size_value . ' ' . $variation->size_unit])->filter()->join(' · ') ?: 'Mặc định' }}</small></div>
                                                        <div class="col-md-3"><label class="small text-muted">Tồn thực tế</label><input type="hidden" name="variations[{{ $loop->index }}][id]" value="{{ $variation->id }}"><input type="number" name="variations[{{ $loop->index }}][stock]" class="form-control" value="{{ $variation->stock }}" min="0" required></div>
                                                        <div class="col-md-4"><label class="small text-muted">Nhập thêm</label><input type="number" name="variations[{{ $loop->index }}][received]" class="form-control" value="0" min="0"></div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="modal-footer"><button type="submit" class="btn btn-primary rounded-pill">Lưu tồn kho</button></div>
                                        </form>
                                    </div></div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i> Chưa có sản phẩm nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.modal').forEach(function (modal) {
            document.body.appendChild(modal);
        });
    });
</script>
@endsection