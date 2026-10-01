@csrf
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
        <input name="title" value="{{ old('title', $banner->title ?? '') }}" class="form-control" required maxlength="180">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Nhãn nhỏ</label>
        <input name="badge" value="{{ old('badge', $banner->badge ?? '') }}" class="form-control" maxlength="100" placeholder="VD: Bộ sưu tập mới, Sale sốc...">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Mô tả</label>
        <textarea name="description" class="form-control" rows="2" maxlength="1000">{{ old('description', $banner->description ?? '') }}</textarea>
    </div>

    <!-- HÌNH ẢNH BANNER -->
    <div class="col-12 mt-4">
        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-image me-1"></i> Hình ảnh nền (Poster fallback)</h6>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Ảnh upload</label>
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        <small class="text-muted">Tối đa 5MB. Định dạng: JPG, PNG, WEBP.</small>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Hoặc URL ảnh</label>
        <input type="url" name="image_url" value="{{ old('image_url', $banner->image_url ?? '') }}" class="form-control" maxlength="2048" placeholder="https://...">
    </div>
    @if(!empty($banner?->image_source))
        <div class="col-12">
            <div class="p-2 border rounded-3 bg-light d-inline-block">
                <span class="small fw-semibold d-block mb-1 text-muted">Ảnh hiện tại:</span>
                <img src="{{ $banner->image_source }}" alt="Preview" class="view-inline-1">
            </div>
        </div>
    @endif

    <!-- VIDEO BANNER -->
    <div class="col-12 mt-4">
        <div class="d-flex align-items-center justify-content-between border-top pt-3">
            <h6 class="fw-bold text-danger mb-0"><i class="bi bi-play-btn-fill me-1"></i> Video phát trên Banner (Tùy chọn)</h6>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Mới 🎬</span>
        </div>
        <small class="text-muted d-block mt-1">Khi cấu hình video, banner sẽ tự động phát video sống động trên trang chủ (kèm các nút bật tiếng / tạm dừng).</small>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Upload file video (MP4, WebM)</label>
        <input type="file" name="video" class="form-control" accept=".mp4,.webm,.ogg,.mov">
        <small class="text-muted">Tối đa 50MB. Hỗ trợ MP4 (H.264), WebM.</small>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Hoặc link Video (YouTube / MP4 URL)</label>
        <input type="url" name="video_url" value="{{ old('video_url', $banner->video_url ?? '') }}" class="form-control" maxlength="2048" placeholder="VD: https://www.youtube.com/watch?v=... hoặc https://...video.mp4">
        <small class="text-muted">Hỗ trợ link YouTube (thường hoặc Shorts) hoặc link video .mp4 trực tiếp.</small>
    </div>
    <div class="col-md-4">
        <div class="form-check form-switch mt-1">
            <input type="hidden" name="video_autoplay" value="0">
            <input type="checkbox" name="video_autoplay" value="1" class="form-check-input" id="video_autoplay" {{ old('video_autoplay', $banner->video_autoplay ?? true) ? 'checked' : '' }}>
            <label class="form-check-label small fw-semibold" for="video_autoplay">Tự động phát (Autoplay)</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check form-switch mt-1">
            <input type="hidden" name="video_loop" value="0">
            <input type="checkbox" name="video_loop" value="1" class="form-check-input" id="video_loop" {{ old('video_loop', $banner->video_loop ?? true) ? 'checked' : '' }}>
            <label class="form-check-label small fw-semibold" for="video_loop">Lặp lại liên tục (Loop)</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check form-switch mt-1">
            <input type="hidden" name="video_muted" value="0">
            <input type="checkbox" name="video_muted" value="1" class="form-check-input" id="video_muted" {{ old('video_muted', $banner->video_muted ?? true) ? 'checked' : '' }}>
            <label class="form-check-label small fw-semibold" for="video_muted">Tắt tiếng ban đầu (Muted)</label>
        </div>
    </div>

    @if(!empty($banner?->has_video))
        <div class="col-12">
            <div class="p-3 border rounded-4 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-dark"><i class="bi bi-camera-video me-1"></i> Video hiện tại ({{ strtoupper($banner->video_type) }}):</span>
                    <div class="form-check">
                        <input type="checkbox" name="remove_video" value="1" class="form-check-input text-danger" id="remove_video">
                        <label class="form-check-label small text-danger fw-semibold" for="remove_video">Xóa video này</label>
                    </div>
                </div>
                <div class="ratio ratio-16x9 rounded-3 overflow-hidden view-inline-2">
                    @if($banner->video_type === 'youtube')
                        <iframe src="{{ $banner->youtube_embed_url }}" title="YouTube video" allowfullscreen></iframe>
                    @elseif($banner->video_type === 'vimeo')
                        <iframe src="{{ $banner->vimeo_embed_url }}" title="Vimeo video" allowfullscreen></iframe>
                    @else
                        <video controls src="{{ $banner->video_source }}" poster="{{ $banner->image_source }}" class="w-100 h-100 object-fit-cover"></video>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- LIÊN KẾT NÚT BẤM & CẤU HÌNH -->
    <div class="col-12 mt-4 border-top pt-3">
        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-link-45deg me-1"></i> Nút bấm & Thứ tự hiển thị</h6>
    </div>
    <div class="col-md-8">
        <label class="form-label fw-semibold">Link nút bấm</label>
        <input name="button_url" value="{{ old('button_url', $banner->button_url ?? '') }}" class="form-control" maxlength="2048" placeholder="/products">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Tên nút</label>
        <input name="button_text" value="{{ old('button_text', $banner->button_text ?? '') }}" class="form-control" maxlength="80" placeholder="VD: Mua ngay, Khám phá...">
    </div>
    <div class="col-md-8">
        <label class="form-label fw-semibold">Alt ảnh / Chú thích SEO</label>
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
            <label class="form-check-label fw-semibold" for="is_active">Hiển thị</label>
        </div>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary rounded-pill px-4"><i class="bi bi-save me-1"></i> Lưu banner</button>
    <a href="{{ route('admin.home-banners.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy</a>
</div>
