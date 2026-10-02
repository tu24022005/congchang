<!-- QUICK VIEW MODAL (XEM NHANH SẢN PHẨM) -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 pb-0 position-relative view-inline-1">
                <button type="button" class="btn-close ms-auto rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body p-4 pt-1">
                <!-- LOADING SKELETON -->
                <div id="quick-view-skeleton" class="text-center py-5">
                    <div class="spinner-border text-danger" role="status" class="view-inline-2">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-muted mt-3 mb-0">Đang chuẩn bị thông tin sản phẩm...</p>
                </div>

                <!-- CONTENT WRAPPER -->
                <div id="quick-view-content" class="d-none">
                    <div class="row g-4 align-items-start">
                        <!-- CỘT ẢNH SẢN PHẨM -->
                        <div class="col-md-6">
                            <div class="quick-view-gallery">
                                <div class="position-relative rounded-3 overflow-hidden bg-light mb-3 view-inline-3">
                                    <img id="qv-main-image" src="" alt="Sản phẩm" class="w-100 h-100 object-fit-cover rounded-3" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                                    <span id="qv-flash-badge" class="position-absolute top-0 start-0 m-2 badge bg-danger rounded-pill d-none">
                                        <i class="bi bi-lightning-charge-fill"></i> FLASH SALE
                                    </span>
                                </div>
                                <div id="qv-thumbs" class="d-flex gap-2 overflow-x-auto pb-2">
                                    <!-- Thumbs injected by JS -->
                                </div>
                            </div>

                            <link rel="stylesheet" href="{{ asset_v('css/views/components-quick-view-modal-blade-php.css') }}">
                        </div>

                        <!-- CỘT CHI TIẾT SẢN PHẨM -->
                        <div class="col-md-6 d-flex flex-column">
                            <span id="qv-category" class="badge bg-danger-subtle text-danger rounded-pill align-self-start mb-2"></span>
                            <h3 id="qv-title" class="fw-bold text-dark mb-2 fs-4"></h3>
                            
                            <!-- GIÁ CẢ -->
                            <div class="d-flex align-items-baseline gap-2 mb-3">
                                <span id="qv-price" class="text-danger fw-bold fs-3"></span>
                                <del id="qv-original-price" class="text-muted small d-none"></del>
                            </div>

                            <p id="qv-description" class="text-muted small mb-3 line-clamp-3 view-inline-4"></p>

                            <!-- BỘ CHỌN BIẾN THỂ -->
                            <div id="qv-variations-section" class="mb-3 d-none">
                                <label class="form-label small fw-bold text-dark mb-2">Phân loại hàng:</label>
                                <div id="qv-variation-options" class="d-flex flex-wrap gap-2">
                                    <!-- Variations injected by JS -->
                                </div>
                            </div>

                            <!-- TỒN KHO & SỐ LƯỢNG -->
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label small fw-bold text-dark mb-0">Số lượng:</label>
                                <span id="qv-stock-label" class="badge bg-success-subtle text-success rounded-pill"></span>
                            </div>

                            <div class="d-flex gap-3 align-items-center mb-3">
                                <div class="quantity-picker view-inline-5">
                                    <button type="button" id="qv-qty-minus" aria-label="Giảm">−</button>
                                    <input type="number" id="qv-quantity" value="1" min="1" max="999" aria-label="Số lượng" class="view-inline-6">
                                    <button type="button" id="qv-qty-plus" aria-label="Tăng">+</button>
                                </div>
                            </div>

                            <!-- NÚT THÊM VÀO GIỎ & MUA NGAY -->
                            <div class="d-flex gap-2 mb-4">
                                <button type="button" id="qv-btn-add-cart" class="btn btn-outline-danger rounded-pill px-3 py-2 flex-grow-1 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1 view-inline-7">
                                    <i class="bi bi-bag-plus"></i>
                                    <span>Thêm vào giỏ</span>
                                </button>
                                <button type="button" id="qv-btn-buy-now" class="btn btn-danger rounded-pill px-3 py-2 flex-grow-1 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1 text-white view-inline-8">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                    <span>Mua ngay</span>
                                </button>
                            </div>

                            <div class="border-top pt-3 mt-auto">
                                <a id="qv-detail-link" href="#" class="small text-decoration-none fw-semibold text-primary d-inline-flex align-items-center gap-1">
                                    <span>Xem thông tin chi tiết đầy đủ</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
