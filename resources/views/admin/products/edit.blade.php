@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
<link rel="stylesheet" href="{{ asset_v('css/views/admin-products-edit-blade-php.css') }}">

<div class="product-editor py-2">
    <div class="editor-hero d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="small text-white-50 mb-1"><i class="bi bi-box-seam me-1"></i> KHO / CHỈNH SỬA</div>
            <h1 class="h3 fw-bold mb-1">{{ $product->name }}</h1>
            <p class="mb-0 text-white-50">Cập nhật nội dung, hình ảnh và tồn kho sản phẩm.</p>
        </div>
        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-light rounded-pill px-3"><i class="bi bi-eye me-1"></i>Xem chi tiết</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <strong>Chưa thể lưu thay đổi:</strong>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="editor-panel p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div><h2 class="h5 editor-panel-title mb-1">Thông tin sản phẩm</h2><small class="text-muted">Nội dung hiển thị cho khách hàng.</small></div>
                        <span class="stock-chip {{ $product->quantity > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"><i class="bi bi-circle-fill small me-1"></i>{{ $product->quantity > 0 ? 'Đang bán' : 'Hết hàng' }}</span>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label editor-label">Tên sản phẩm</label>
                        <input type="text" name="name" id="name" class="form-control editor-input @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="product_code" class="form-label editor-label">Mã sản phẩm</label>
                            <input type="text" name="product_code" id="product_code" class="form-control editor-input @error('product_code') is-invalid @enderror" value="{{ old('product_code', $product->product_code) }}" placeholder="SP001">
                            @error('product_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="brand_id" class="form-label editor-label">Thương hiệu</label>
                            <select name="brand_id" id="brand_id" class="form-select editor-input @error('brand_id') is-invalid @enderror">
                                <option value="">-- Chọn thương hiệu --</option>
                                @foreach($brands as $brand)<option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>{{ $brand->name }}</option>@endforeach
                            </select>
                            @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label editor-label">Danh mục</label>
                        <select name="category_id" id="category_id" class="form-select editor-input @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label editor-label">Mô tả sản phẩm</label>
                        <textarea name="description" id="description" rows="5" maxlength="1000" class="form-control editor-input @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                        <div class="text-end text-muted small mt-1"><span id="description-count">0</span>/1000 ký tự</div>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- THÔNG TIN MỸ PHẨM -->
                    <div class="border rounded-3 p-3 my-4 bg-light">
                        <h5 class="fw-bold mb-2 text-primary"><i class="bi bi-flower1 me-1"></i>Thông tin mỹ phẩm & làm đẹp</h5>
                        <small class="text-muted d-block mb-3">Hiển thị trong bảng thông số chi tiết sản phẩm và tư vấn làm đẹp.</small>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="origin" class="form-label editor-label">Xuất xứ / Nơi sản xuất</label>
                                <input type="text" name="origin" id="origin" class="form-control editor-input" value="{{ old('origin', $product->origin) }}" placeholder="VD: Hàn Quốc, Nhật Bản, Pháp...">
                            </div>
                            <div class="col-md-6">
                                <label for="expiry_info" class="form-label editor-label">Hạn sử dụng</label>
                                <input type="text" name="expiry_info" id="expiry_info" class="form-control editor-input" value="{{ old('expiry_info', $product->expiry_info) }}" placeholder="VD: 36 tháng kể từ NSX, 12 tháng sau mở nắp">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label editor-label">Phù hợp loại da</label>
                            <div class="d-flex flex-wrap gap-3">
                                @php
                                    $availableSkinTypes = [
                                        'da_dau' => 'Da dầu',
                                        'da_kho' => 'Da khô',
                                        'da_hon_hop' => 'Da hỗn hợp',
                                        'da_nhay_cam' => 'Da nhạy cảm',
                                        'moi_loai_da' => 'Mọi loại da',
                                    ];
                                    $currentSkinTypes = old('skin_types', $product->skin_types ?: []);
                                @endphp
                                @foreach($availableSkinTypes as $key => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="skin_types[]" value="{{ $key }}" id="edit_skin_type_{{ $key }}" @checked(in_array($key, (array)$currentSkinTypes, true))>
                                        <label class="form-check-label" for="edit_skin_type_{{ $key }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ingredients" class="form-label editor-label">Thành phần chi tiết (Ingredients)</label>
                            <textarea name="ingredients" id="ingredients" rows="3" class="form-control editor-input" placeholder="VD: Niacinamide 10%, Zinc PCA 1%, Aqua, Glycerin...">{{ old('ingredients', $product->ingredients) }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label for="usage_instructions" class="form-label editor-label">Hướng dẫn sử dụng (Usage Instructions)</label>
                            <textarea name="usage_instructions" id="usage_instructions" rows="3" class="form-control editor-input" placeholder="VD: Sử dụng sau bước toner, lấy 2-3 giọt thoa đều lên da mặt...">{{ old('usage_instructions', $product->usage_instructions) }}</textarea>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="quantity" class="form-label editor-label">Tồn kho</label>
                            <div class="input-group"><span class="input-group-text bg-light border-end-0">#</span><input type="number" name="quantity" id="quantity" min="0" class="form-control editor-input border-start-0 @error('quantity') is-invalid @enderror" value="{{ old('quantity', $product->quantity) }}" required></div>
                            @error('quantity')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="border rounded-3 p-3 mt-4 bg-warning-subtle">
                        <h5 class="fw-bold mb-1"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Flash sale</h5>
                        <small class="text-muted d-block mb-3">Giá flash sale áp dụng cho toàn bộ biến thể trong khung giờ đã chọn.</small>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label editor-label">Giá flash sale</label><input type="number" min="0" name="flash_sale_price" value="{{ old('flash_sale_price', $product->flash_sale_price) }}" class="form-control editor-input"></div>
                            <div class="col-md-4"><label class="form-label editor-label">Bắt đầu</label><input type="datetime-local" name="flash_sale_starts_at" value="{{ old('flash_sale_starts_at', optional($product->flash_sale_starts_at)->format('Y-m-d\TH:i')) }}" class="form-control editor-input"></div>
                            <div class="col-md-4"><label class="form-label editor-label">Kết thúc</label><input type="datetime-local" name="flash_sale_ends_at" value="{{ old('flash_sale_ends_at', optional($product->flash_sale_ends_at)->format('Y-m-d\TH:i')) }}" class="form-control editor-input"></div>
                        </div>
                    </div>
                </div>

                @php
                    $variationRows = old('variations', $product->variations->map(fn ($variation) => $variation->toArray())->all());
                @endphp
                <div class="editor-panel p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div><h2 class="h5 editor-panel-title mb-1">Mã và phân loại sản phẩm</h2><small class="text-muted">Giá và tồn kho được quản lý riêng cho từng mã loại.</small></div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-variation"><i class="bi bi-plus-lg me-1"></i>Thêm dòng</button>
                    </div>
                    <div id="variations-list">
                        @foreach($variationRows as $index => $variation)
                            <div class="row g-2 align-items-end variation-row mb-2">
                                <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variation['id'] ?? '' }}">
                                <div class="col-md-2"><label class="form-label small">Mã SKU</label><input name="variations[{{ $index }}][sku]" class="form-control editor-input" value="{{ $variation['sku'] ?? '' }}" placeholder="SON-RED-01"></div>
                                <div class="col-md-2"><label class="form-label small">Màu</label><input name="variations[{{ $index }}][color]" class="form-control editor-input" value="{{ $variation['color'] ?? '' }}" placeholder="Đỏ"></div>
                                <div class="col-md-2"><label class="form-label small">Bộ nhớ / loại</label><input name="variations[{{ $index }}][storage]" class="form-control editor-input" value="{{ $variation['storage'] ?? '' }}" placeholder="256GB"></div>
                                <div class="col-md-2"><label class="form-label small">Khối lượng</label><input type="number" step="0.01" min="0" name="variations[{{ $index }}][size_value]" class="form-control editor-input" value="{{ $variation['size_value'] ?? '' }}" placeholder="250"></div>
                                <div class="col-md-1"><label class="form-label small">Đơn vị</label><select name="variations[{{ $index }}][size_unit]" class="form-select editor-input"><option value="">-</option>@foreach(['g', 'kg', 'ml', 'l'] as $unit)<option value="{{ $unit }}" @selected(($variation['size_unit'] ?? '') === $unit)>{{ $unit }}</option>@endforeach</select></div>
                                <div class="col-md-1"><label class="form-label small">Giá bán</label><input type="number" min="0" name="variations[{{ $index }}][price]" class="form-control editor-input" value="{{ $variation['price'] ?? '' }}" required></div>
                                <div class="col-md-1"><label class="form-label small">Tồn</label><input type="number" min="0" name="variations[{{ $index }}][stock]" class="form-control editor-input" value="{{ $variation['stock'] ?? 0 }}"></div>
                                <div class="col-md-2"><label class="form-label small">Ảnh biến thể</label>@if(!empty($variation['image']))<img src="{{ asset('storage/' . $variation['image']) }}" class="rounded mb-1 view-inline-1" alt="Ảnh hiện tại">@endif<input type="file" name="variations[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*"><small class="text-muted">Để trống để giữ ảnh</small></div>
                                <div class="col-md-1 variation-actions"><button type="button" class="btn btn-outline-primary duplicate-variation" title="Nhân bản dòng"><i class="bi bi-copy"></i></button><button type="button" class="btn btn-outline-danger remove-variation" title="Xóa dòng"><i class="bi bi-trash"></i></button></div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="editor-panel p-4">
                    <h2 class="h5 editor-panel-title mb-1">Bộ sưu tập ảnh</h2>
                    <p class="text-muted small mb-3">Ảnh chính dùng trên danh sách sản phẩm. Chọn thêm nhiều ảnh để tạo gallery.</p>
                    <div class="row g-3 align-items-start">
                        <div class="col-md-4">
                            <label for="image" class="form-label editor-label">Ảnh chính</label>
                            <img id="main-image-preview" class="image-preview mb-2" src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/placeholder.jpg') }}" alt="Ảnh xem trước">
                            <input type="file" name="image" id="image" class="form-control editor-input @error('image') is-invalid @enderror" accept="image/*">
                            @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label for="gallery" class="form-label editor-label">Ảnh bổ sung</label>
                            <input type="file" name="gallery[]" id="gallery" class="form-control editor-input @error('gallery.*') is-invalid @enderror" accept="image/*" multiple>
                            <small class="text-muted d-block mt-2">Có thể chọn nhiều ảnh cùng lúc. Ảnh mới sẽ được thêm vào gallery.</small>
                            <div id="gallery-preview" class="gallery-preview mt-3"></div>
                            @if($product->images->isNotEmpty())
                                <div class="small fw-bold text-muted mt-4 mb-2">Ảnh hiện có</div>
                                <div class="gallery-preview">
                                    @foreach($product->images as $image)
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Ảnh gallery">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="editor-panel p-4 mb-4">
                    <h2 class="h5 editor-panel-title mb-3">Tóm tắt kho</h2>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3"><span class="text-muted">Tồn hiện tại</span><strong class="fs-4 {{ $product->quantity > 0 ? 'text-success' : 'text-danger' }}">{{ number_format($product->quantity) }}</strong></div>
                    <div class="d-flex justify-content-between align-items-center"><span class="text-muted">Giá niêm yết</span><strong class="text-danger">{{ number_format($product->price, 0, ',', '.') }} đ</strong></div>
                    <div class="progress mt-3 view-inline-2"><div class="progress-bar {{ $product->quantity > 10 ? 'bg-success' : 'bg-warning' }}" data-inline-width="{{ min(100, max(4, $product->quantity)) }}" class="inline-dynamic-width"></div></div>
                    <small class="text-muted d-block mt-2">{{ $product->quantity <= 10 ? 'Nên bổ sung hàng sớm.' : 'Mức tồn kho đang ổn định.' }}</small>
                </div>
                <div class="editor-panel p-4">
                    <h2 class="h5 editor-panel-title mb-3">Hoàn tất</h2>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold"><i class="bi bi-check2-circle me-1"></i>Lưu thay đổi</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light border w-100 rounded-pill py-2 mt-2"><i class="bi bi-arrow-left me-1"></i>Quay lại kho</a>
                    <div class="small text-muted mt-3"><i class="bi bi-clock-history me-1"></i>Cập nhật lần cuối: {{ $product->updated_at?->format('d/m/Y H:i') ?? 'Chưa có' }}</div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset_v('js/views/admin-products-edit-blade-php.js') }}" defer></script>
@endsection
