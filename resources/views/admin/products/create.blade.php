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

            <!-- THÔNG TIN MỸ PHẨM -->
            <div class="border rounded-3 p-3 mb-4 bg-light">
                <h5 class="fw-bold mb-2 text-primary"><i class="bi bi-flower1 me-1"></i>Thông tin mỹ phẩm & làm đẹp</h5>
                <small class="text-muted d-block mb-3">Hiển thị trong bảng thông số chi tiết sản phẩm và tư vấn làm đẹp.</small>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="origin" class="form-label fw-bold">Xuất xứ / Nơi sản xuất</label>
                        <input type="text" name="origin" id="origin" class="form-control" value="{{ old('origin') }}" placeholder="VD: Hàn Quốc, Nhật Bản, Pháp...">
                    </div>
                    <div class="col-md-6">
                        <label for="expiry_info" class="form-label fw-bold">Hạn sử dụng</label>
                        <input type="text" name="expiry_info" id="expiry_info" class="form-control" value="{{ old('expiry_info') }}" placeholder="VD: 36 tháng kể từ NSX, 12 tháng sau mở nắp">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Phù hợp loại da</label>
                    <div class="d-flex flex-wrap gap-3">
                        @php
                            $availableSkinTypes = [
                                'da_dau' => 'Da dầu',
                                'da_kho' => 'Da khô',
                                'da_hon_hop' => 'Da hỗn hợp',
                                'da_nhay_cam' => 'Da nhạy cảm',
                                'moi_loai_da' => 'Mọi loại da',
                            ];
                            $oldSkinTypes = old('skin_types', []);
                        @endphp
                        @foreach($availableSkinTypes as $key => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="skin_types[]" value="{{ $key }}" id="skin_type_{{ $key }}" @checked(in_array($key, $oldSkinTypes, true))>
                                <label class="form-check-label" for="skin_type_{{ $key }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mb-3">
                    <label for="ingredients" class="form-label fw-bold">Thành phần chi tiết (Ingredients)</label>
                    <textarea name="ingredients" id="ingredients" rows="3" class="form-control" placeholder="VD: Niacinamide 10%, Zinc PCA 1%, Aqua, Glycerin...">{{ old('ingredients') }}</textarea>
                </div>
                <div class="mb-2">
                    <label for="usage_instructions" class="form-label fw-bold">Hướng dẫn sử dụng (Usage Instructions)</label>
                    <textarea name="usage_instructions" id="usage_instructions" rows="3" class="form-control" placeholder="VD: Sử dụng sau bước toner, lấy 2-3 giọt thoa đều lên da mặt...">{{ old('usage_instructions') }}</textarea>
                </div>
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
<script src="{{ asset_v('js/views/admin-products-create-blade-php.js') }}" defer></script>
@endsection