@extends('admin.layouts.app')
@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-dark text-white">
        <h4 class="mb-0">Thêm sản phẩm mới</h4>
    </div>
    
    <div class="card-body">
        <!-- BẮT BUỘC PHẢI CÓ enctype ĐỂ UPLOAD ĐƯỢC ẢNH -->
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Tên sản phẩm</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="product_code" class="form-label fw-bold">Mã sản phẩm</label>
                    <input type="text" name="product_code" id="product_code" class="form-control @error('product_code') is-invalid @enderror" value="{{ old('product_code') }}" placeholder="SP001">
                    @error('product_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="brand_id" class="form-label fw-bold">Thương hiệu</label>
                    <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                        <option value="">-- Chọn thương hiệu --</option>
                        @foreach($brands as $brand)<option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>{{ $brand->name }}</option>@endforeach
                    </select>
                    @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label fw-bold">Danh mục</label>
                <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', request('category_id')) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
<!-- Ảnh đại diện chính (Như cũ) -->
<div class="mb-3">
    <label>Ảnh đại diện chính</label>
    <input type="file" name="image" class="form-control">
</div>

<!-- THÊM ĐOẠN NÀY: Bộ sưu tập ảnh phụ -->
<div class="mb-3">
    <label>Bộ sưu tập ảnh (Chọn nhiều ảnh cùng lúc)</label>
    <input type="file" name="gallery[]" class="form-control" multiple>
</div>
            <!-- Ô CHỌN ẢNH SẢN PHẨM -->
            <div class="mb-3">
                <label for="image" class="form-label fw-bold">Hình ảnh sản phẩm</label>
                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-bold">Mô tả</label>
                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label fw-bold">Số lượng</label>
                <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required min="0">
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="border rounded-3 p-3 mb-4 bg-warning-subtle">
                <h5 class="fw-bold mb-1"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Flash sale</h5>
                <small class="text-muted d-block mb-3">Giá flash sale áp dụng cho toàn bộ biến thể trong khung giờ đã chọn.</small>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label">Giá flash sale</label><input type="number" min="0" name="flash_sale_price" value="{{ old('flash_sale_price') }}" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Bắt đầu</label><input type="datetime-local" name="flash_sale_starts_at" value="{{ old('flash_sale_starts_at') }}" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Kết thúc</label><input type="datetime-local" name="flash_sale_ends_at" value="{{ old('flash_sale_ends_at') }}" class="form-control"></div>
                </div>
            </div>

            @php $variationRows = old('variations', [[]]); @endphp
            <div class="border rounded-3 p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div><h5 class="fw-bold mb-1">Mã và phân loại sản phẩm</h5><small class="text-muted">Giá và tồn kho được quản lý riêng cho từng mã loại.</small></div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-variation"><i class="bi bi-plus-lg me-1"></i>Thêm dòng</button>
                </div>
                <div id="variations-list">
                    @foreach($variationRows as $index => $variation)
                        <div class="row g-2 align-items-end variation-row mb-2">
                            <div class="col-md-2"><label class="form-label small">Mã SKU</label><input name="variations[{{ $index }}][sku]" class="form-control" value="{{ $variation['sku'] ?? '' }}" placeholder="SON-RED-01"></div>
                            <div class="col-md-2"><label class="form-label small">Màu</label><input name="variations[{{ $index }}][color]" class="form-control" value="{{ $variation['color'] ?? '' }}" placeholder="Đỏ"></div>
                            <div class="col-md-2"><label class="form-label small">Bộ nhớ / loại</label><input name="variations[{{ $index }}][storage]" class="form-control" value="{{ $variation['storage'] ?? '' }}" placeholder="256GB"></div>
                            <div class="col-md-2"><label class="form-label small">Khối lượng</label><input type="number" step="0.01" min="0" name="variations[{{ $index }}][size_value]" class="form-control" value="{{ $variation['size_value'] ?? '' }}" placeholder="250"></div>
                            <div class="col-md-1"><label class="form-label small">Đơn vị</label><select name="variations[{{ $index }}][size_unit]" class="form-select"><option value="">-</option>@foreach(['g', 'kg', 'ml', 'l'] as $unit)<option value="{{ $unit }}" @selected(($variation['size_unit'] ?? '') === $unit)>{{ $unit }}</option>@endforeach</select></div>
                            <div class="col-md-1"><label class="form-label small">Giá bán</label><input type="number" min="0" name="variations[{{ $index }}][price]" class="form-control" value="{{ $variation['price'] ?? '' }}" required></div>
                            <div class="col-md-1"><label class="form-label small">Tồn</label><input type="number" min="0" name="variations[{{ $index }}][stock]" class="form-control" value="{{ $variation['stock'] ?? 0 }}"></div>
                            <div class="col-md-2"><label class="form-label small">Ảnh biến thể</label><input type="file" name="variations[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*"></div>
                            <div class="col-md-1 variation-actions"><button type="button" class="btn btn-outline-primary duplicate-variation" title="Nhân bản dòng"><i class="bi bi-copy"></i></button><button type="button" class="btn btn-outline-danger remove-variation" title="Xóa dòng"><i class="bi bi-trash"></i></button></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại</a>
                <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('variations-list');
    let index = list.querySelectorAll('.variation-row').length;
    document.getElementById('add-variation').addEventListener('click', function () {
        list.insertAdjacentHTML('beforeend', `<div class="row g-2 align-items-end variation-row mb-2"><div class="col-md-2"><label class="form-label small">Mã SKU</label><input name="variations[${index}][sku]" class="form-control" placeholder="SON-RED-01"></div><div class="col-md-2"><label class="form-label small">Màu</label><input name="variations[${index}][color]" class="form-control" placeholder="Đỏ"></div><div class="col-md-2"><label class="form-label small">Bộ nhớ / loại</label><input name="variations[${index}][storage]" class="form-control" placeholder="256GB"></div><div class="col-md-2"><label class="form-label small">Khối lượng</label><input type="number" step="0.01" min="0" name="variations[${index}][size_value]" class="form-control" placeholder="250"></div><div class="col-md-1"><label class="form-label small">Đơn vị</label><select name="variations[${index}][size_unit]" class="form-select"><option value="">-</option><option value="g">g</option><option value="kg">kg</option><option value="ml">ml</option><option value="l">l</option></select></div><div class="col-md-1"><label class="form-label small">Giá bán</label><input type="number" min="0" name="variations[${index}][price]" class="form-control" required></div><div class="col-md-1"><label class="form-label small">Tồn</label><input type="number" min="0" name="variations[${index}][stock]" class="form-control" value="0" required></div><div class="col-md-2"><label class="form-label small">Ảnh biến thể</label><input type="file" name="variations[${index}][image]" class="form-control form-control-sm" accept="image/*"></div><div class="col-md-1 variation-actions"><button type="button" class="btn btn-outline-primary duplicate-variation" title="Nhân bản dòng"><i class="bi bi-copy"></i></button><button type="button" class="btn btn-outline-danger remove-variation" title="Xóa dòng"><i class="bi bi-trash"></i></button></div></div>`);
        index++;
    });
    list.addEventListener('click', event => {
        const row = event.target.closest('.variation-row');
        if (!row) return;
        if (event.target.closest('.remove-variation')) row.remove();
        if (event.target.closest('.duplicate-variation')) list.appendChild(row.cloneNode(true));
        list.querySelectorAll('.variation-row').forEach((currentRow, rowIndex) => currentRow.querySelectorAll('[name]').forEach(input => input.name = input.name.replace(/variations\[\d+\]/, `variations[${rowIndex}]`)));
        index = list.querySelectorAll('.variation-row').length;
    });
});
</script>
@endsection