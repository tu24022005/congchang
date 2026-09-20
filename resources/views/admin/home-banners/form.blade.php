@csrf
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
        <input name="title" value="{{ old('title', $banner->title ?? '') }}" class="form-control" required maxlength="180">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Nhãn nhỏ</label>
        <input name="badge" value="{{ old('badge', $banner->badge ?? '') }}" class="form-control" maxlength="100">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Mô tả</label>
        <textarea name="description" class="form-control" rows="3" maxlength="1000">{{ old('description', $banner->description ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Ảnh upload</label>
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        <small class="text-muted">Tối đa 5MB. Nếu upload ảnh, ảnh URL sẽ bị bỏ qua.</small>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Hoặc URL ảnh</label>
        <input type="url" name="image_url" value="{{ old('image_url', $banner->image_url ?? '') }}" class="form-control" maxlength="2048">
    </div>
    <div class="col-md-8">
        <label class="form-label fw-semibold">Link nút bấm</label>
        <input name="button_url" value="{{ old('button_url', $banner->button_url ?? '') }}" class="form-control" maxlength="2048" placeholder="/products">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Tên nút</label>
        <input name="button_text" value="{{ old('button_text', $banner->button_text ?? '') }}" class="form-control" maxlength="80">
    </div>
    <div class="col-md-8">
        <label class="form-label fw-semibold">Alt ảnh</label>
        <input name="alt_text" value="{{ old('alt_text', $banner->alt_text ?? '') }}" class="form-control" maxlength="180">
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">Thứ tự</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="form-control" min="0" max="9999" required>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Hiển thị</label>
        </div>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary rounded-pill"><i class="bi bi-save me-1"></i> Lưu banner</button>
    <a href="{{ route('admin.home-banners.index') }}" class="btn btn-outline-secondary rounded-pill">Hủy</a>
</div>
