@php($value = fn ($key, $default = '') => old($key, $post?->$key ?? $default))
<div class="row g-3">
    <div class="col-lg-8"><label class="form-label fw-bold">Tiêu đề *</label><input name="title" class="form-control @error('title') is-invalid @enderror" value="{{ $value('title') }}" required>@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-lg-4"><label class="form-label fw-bold">Danh mục</label><select name="post_category_id" class="form-select"><option value="">-- Chọn danh mục --</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected($value('post_category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <div class="col-12"><label class="form-label fw-bold">Mô tả ngắn</label><textarea name="excerpt" class="form-control" rows="2" maxlength="500">{{ $value('excerpt') }}</textarea></div>
    <div class="col-12"><label class="form-label fw-bold">Nội dung *</label><textarea name="content" class="form-control admin-content-editor" rows="14" required>{{ $value('content') }}</textarea><small class="text-muted">Có thể nhập HTML đơn giản để định dạng bài viết.</small></div>
    <div class="col-lg-6"><label class="form-label fw-bold">Ảnh đại diện</label><input type="file" name="featured_image" id="featured_image" class="form-control" accept="image/*"><div id="image-preview" class="mt-2">@if($post?->featured_image)<img src="{{ asset('storage/'.$post->featured_image) }}" class="rounded-3" style="width:180px;height:110px;object-fit:cover">@endif</div></div>
    <div class="col-md-3"><label class="form-label fw-bold">Trạng thái</label><select name="status" class="form-select"><option value="draft" @selected($value('status', 'draft') === 'draft')>Bản nháp</option><option value="published" @selected($value('status') === 'published')>Đăng ngay</option></select></div>
    <div class="col-md-3"><label class="form-label fw-bold">Ngày đăng</label><input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', $post?->published_at?->format('Y-m-d\TH:i')) }}"></div>
</div>
<script>
document.getElementById('featured_image')?.addEventListener('change', function (event) {
    const file = event.target.files[0], preview = document.getElementById('image-preview');
    if (!file) return;
    const reader = new FileReader();
    reader.onload = () => { preview.innerHTML = '<img src="' + reader.result + '" class="rounded-3" style="width:180px;height:110px;object-fit:cover" alt="Ảnh xem trước">'; };
    reader.readAsDataURL(file);
});
</script>
