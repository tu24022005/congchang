<!-- FLOATING COMPARE DOCK (PROMPT 3.6) -->
<div id="compare-floating-dock" class="compare-floating-dock d-none" role="region" aria-label="Khay so sánh sản phẩm">
    <div class="d-flex align-items-center gap-2" id="compare-items-thumbs">
        <!-- Injected by JS -->
    </div>
    <div class="d-flex align-items-center gap-2 border-start ps-3">
        <a href="{{ route('products.compare') }}" id="btn-compare-now" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold disabled">
            <i class="bi bi-arrow-left-right me-1"></i>So sánh ngay (<span id="compare-count-badge">0</span>)
        </a>
        <button type="button" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" onclick="window.clearCompareList()" title="Xóa tất cả">
            <i class="bi bi-x-circle fs-5"></i>
        </button>
    </div>
</div>

<script src="{{ asset_v('js/views/components-compare-floating-dock-blade-php.js') }}" defer></script>
