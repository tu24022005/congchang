@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
<style>
    .product-editor { --ink: #203047; --muted: #718096; --line: #e6ebf2; }
    .editor-hero { background: linear-gradient(135deg, #183b56, #267d8f); color: #fff; border-radius: 18px; padding: 1.6rem 1.8rem; }
    .editor-panel { border: 1px solid var(--line); border-radius: 16px; background: #fff; box-shadow: 0 12px 30px rgba(32, 48, 71, .07); }
    .editor-panel-title { color: var(--ink); font-weight: 800; letter-spacing: -.02em; }
    .editor-label { color: var(--ink); font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
    .editor-input { border-color: var(--line); border-radius: 10px; padding: .72rem .85rem; }
    .editor-input:focus { border-color: #27a7bd; box-shadow: 0 0 0 .2rem rgba(39, 167, 189, .12); }
    .image-preview { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 14px; background: #f4f7fa; border: 1px solid var(--line); }
    .gallery-preview { display: grid; grid-template-columns: repeat(auto-fill, minmax(88px, 1fr)); gap: .65rem; }
    .gallery-preview img { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 10px; border: 1px solid var(--line); }
    .stock-chip { border-radius: 999px; padding: .42rem .7rem; font-size: .78rem; font-weight: 800; }
</style>

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
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="quantity" class="form-label editor-label">Tồn kho</label>
                            <div class="input-group"><span class="input-group-text bg-light border-end-0">#</span><input type="number" name="quantity" id="quantity" min="0" class="form-control editor-input border-start-0 @error('quantity') is-invalid @enderror" value="{{ old('quantity', $product->quantity) }}" required></div>
                            @error('quantity')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
                                <div class="col-md-2"><label class="form-label small">Ảnh biến thể</label>@if(!empty($variation['image']))<img src="{{ asset('storage/' . $variation['image']) }}" class="rounded mb-1" style="width:42px;height:42px;object-fit:cover" alt="Ảnh hiện tại">@endif<input type="file" name="variations[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*"><small class="text-muted">Để trống để giữ ảnh</small></div>
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
                    <div class="progress mt-3" style="height: 8px"><div class="progress-bar {{ $product->quantity > 10 ? 'bg-success' : 'bg-warning' }}" style="width: {{ min(100, max(4, $product->quantity)) }}%"></div></div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('main-image-preview');
    const galleryInput = document.getElementById('gallery');
    const galleryPreview = document.getElementById('gallery-preview');
    const description = document.getElementById('description');
    const descriptionCount = document.getElementById('description-count');

    imageInput?.addEventListener('change', function () {
        const file = this.files?.[0];
        if (file) imagePreview.src = URL.createObjectURL(file);
    });

    galleryInput?.addEventListener('change', function () {
        galleryPreview.innerHTML = '';
        Array.from(this.files || []).forEach(function (file) {
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = file.name;
            galleryPreview.appendChild(image);
        });
    });

    const updateCount = () => descriptionCount.textContent = description.value.length;
    description.addEventListener('input', updateCount);
    updateCount();

    const list = document.getElementById('variations-list');
    let variationIndex = list.querySelectorAll('.variation-row').length;
    const reindexRows = () => list.querySelectorAll('.variation-row').forEach((row, index) => row.querySelectorAll('[name]').forEach(input => input.name = input.name.replace(/variations\[\d+\]/, `variations[${index}]`)));
    document.getElementById('add-variation').addEventListener('click', function () {
        const index = list.querySelectorAll('.variation-row').length;
        list.insertAdjacentHTML('beforeend', `<div class="row g-2 align-items-end variation-row mb-2"><div class="col-md-2"><label class="form-label small">Mã SKU</label><input name="variations[${index}][sku]" class="form-control editor-input" placeholder="SON-RED-01"></div><div class="col-md-2"><label class="form-label small">Màu</label><input name="variations[${index}][color]" class="form-control editor-input" placeholder="Đỏ"></div><div class="col-md-2"><label class="form-label small">Bộ nhớ / loại</label><input name="variations[${index}][storage]" class="form-control editor-input" placeholder="256GB"></div><div class="col-md-2"><label class="form-label small">Khối lượng</label><input type="number" step="0.01" min="0" name="variations[${index}][size_value]" class="form-control editor-input" placeholder="250"></div><div class="col-md-1"><label class="form-label small">Đơn vị</label><select name="variations[${index}][size_unit]" class="form-select editor-input"><option value="">-</option><option value="g">g</option><option value="kg">kg</option><option value="ml">ml</option><option value="l">l</option></select></div><div class="col-md-1"><label class="form-label small">Giá</label><input type="number" min="0" name="variations[${index}][price]" class="form-control editor-input"></div><div class="col-md-1"><label class="form-label small">Tồn</label><input type="number" min="0" name="variations[${index}][stock]" class="form-control editor-input" value="0"></div><div class="col-md-2"><label class="form-label small">Ảnh biến thể</label><input type="file" name="variations[${index}][image]" class="form-control form-control-sm" accept="image/*"><small class="text-muted">Để trống để giữ ảnh</small></div><div class="col-md-1 variation-actions"><button type="button" class="btn btn-outline-primary duplicate-variation" title="Nhân bản dòng"><i class="bi bi-copy"></i></button><button type="button" class="btn btn-outline-danger remove-variation" title="Xóa dòng"><i class="bi bi-trash"></i></button></div></div>`);
        variationIndex++;
    });
    list.addEventListener('click', event => {
        const row = event.target.closest('.variation-row');
        if (!row) return;
        if (event.target.closest('.remove-variation')) row.remove();
        if (event.target.closest('.duplicate-variation')) {
            const clone = row.cloneNode(true);
            clone.querySelector('input[name$="[id]"]')?.remove();
            list.appendChild(clone);
        }
        reindexRows();
        variationIndex = list.querySelectorAll('.variation-row').length;
    });
});
</script>
@endsection
