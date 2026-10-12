@props(['currentSlug' => null])

<!-- KHỐI SẢN PHẨM ĐÃ XEM GẦN ĐÂY (PROMPT 3.5) -->
<section id="recently-viewed-section"
    class="my-5 d-none"
    data-current-slug="{{ $currentSlug }}"
    data-products-url="{{ url('/products') }}"
    data-placeholder-image="{{ asset('images/placeholder.svg') }}"
    aria-labelledby="recently-viewed-title">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 id="recently-viewed-title" class="fw-bold mb-0 storefront-title fs-5">
            <i class="bi bi-clock-history text-danger me-2"></i>Sản phẩm bạn đã xem gần đây
        </h4>
        <button type="button" class="btn btn-sm btn-link text-muted text-decoration-none" onclick="window.clearRecentlyViewed()">
            Xóa lịch sử xem
        </button>
    </div>
    
    <div id="recently-viewed-track" class="recently-viewed-track">
        <!-- Injected by JS -->
    </div>
</section>

<link rel="stylesheet" href="{{ asset_v('css/views/components-recently-viewed-blade-php.css') }}">
<script src="{{ asset_v('js/views/components-recently-viewed-blade-php.js') }}" defer></script>
