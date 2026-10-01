@forelse($products as $product)
    <div class="col-lg-3 col-md-4 col-sm-6 product-grid-item" data-product-id="{{ $product->id }}">
        @php
            $variationPrices = $product->variations->map(fn ($variation) => $product->effectivePrice($variation));
            $displayMinPrice = $variationPrices->isNotEmpty() ? $variationPrices->min() : $product->effectivePrice();
            $displayMaxPrice = $variationPrices->isNotEmpty() ? $variationPrices->max() : $product->effectivePrice();
            $productStock = $product->available_stock;
        @endphp
        <div class="card product-card text-center h-100 shadow-sm border-0">
            <div class="card-body p-3 p-md-4 d-flex flex-column">
                
                <div class="mb-3 shine-sweep rounded-3 position-relative overflow-hidden view-inline-1">
                    <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="d-block w-100 h-100">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover rounded product-list-image" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                        @else
                            <div class="d-flex align-items-center justify-content-center w-100 h-100 rounded-3 bg-light product-icon-placeholder">
                                <i class="bi bi-bag-heart text-danger fs-2"></i>
                            </div>
                        @endif
                    </a>
                    @if($productStock <= 0)
                        <span class="position-absolute top-0 start-0 m-2 badge bg-danger-subtle text-danger rounded-pill fw-bold z-2">
                            <i class="bi bi-slash-circle me-1"></i>Hết hàng
                        </span>
                    @elseif($product->isFlashSaleActive())
                        <span class="position-absolute top-0 start-0 m-2 badge bg-danger rounded-pill shadow-sm">
                            <i class="bi bi-lightning-charge-fill"></i> FLASH
                        </span>
                    @endif

                    <!-- NÚT TIỆN ÍCH NỔI TRÊN ẢNH: XEM NHANH & SO SÁNH -->
                    <div class="product-card-floating-actions position-absolute top-0 end-0 m-2 d-flex flex-column gap-1 z-2">
                        <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm floating-action-btn btn-quick-view" data-quick-view-slug="{{ $product->slug }}" title="Xem nhanh sản phẩm" aria-label="Xem nhanh {{ $product->name }}">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm floating-action-btn btn-compare-toggle" data-compare-id="{{ $product->id }}" data-compare-name="{{ $product->name }}" data-compare-image="{{ $product->image ? asset('storage/' . $product->image) : asset('images/placeholder.svg') }}" title="So sánh sản phẩm" aria-label="So sánh {{ $product->name }}">
                            <i class="bi bi-arrow-left-right"></i>
                        </button>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-1 flex-wrap mb-2">
                    <span class="badge text-primary rounded-pill product-category-badge view-inline-2">{{ $product->category->name ?? 'Mỹ phẩm' }}</span>
                    @if($product->brand)
                        <span class="badge bg-light text-dark rounded-pill view-inline-2">{{ $product->brand->name }}</span>
                    @endif
                </div>

                <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                    <h5 class="fw-bold text-dark mb-0 fs-6 text-truncate w-100" title="{{ $product->name }}">
                        <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="text-decoration-none text-dark">
                            {{ $product->name }}
                        </a>
                    </h5>
                    @auth
                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="wishlist-toggle-form flex-shrink-0">
                            @csrf
                            <button type="submit" class="btn btn-sm wishlist-toggle-btn {{ ($wishlistProductIds ?? collect())->contains($product->id) ? 'btn-danger' : 'btn-outline-danger' }} rounded-circle p-0 view-inline-3" title="{{ ($wishlistProductIds ?? collect())->contains($product->id) ? 'Bỏ khỏi yêu thích' : 'Lưu vào yêu thích' }}" aria-label="{{ ($wishlistProductIds ?? collect())->contains($product->id) ? 'Bỏ khỏi yêu thích' : 'Lưu vào yêu thích' }}">
                                <i class="bi bi-heart{{ ($wishlistProductIds ?? collect())->contains($product->id) ? '-fill' : '' }}"></i>
                            </button>
                        </form>
                    @endauth
                </div>

                <p class="text-muted small mb-2 flex-grow-1 product-description line-clamp-2 view-inline-4">
                    {{ $product->description ?? 'Sản phẩm chăm sóc chất lượng cho vẻ đẹp rạng ngời mỗi ngày.' }}
                </p>

                <div class="product-card-price-wrap mb-2">
                    <div class="product-card-price text-danger fw-bold fs-6">
                        @if($displayMinPrice < $displayMaxPrice)
                            {{ number_format($displayMinPrice, 0, ',', '.') }} - {{ number_format($displayMaxPrice, 0, ',', '.') }} ₫
                        @else
                            {{ number_format($displayMinPrice, 0, ',', '.') }} ₫
                        @endif
                    </div>
                    <div class="product-card-meta-row small text-muted d-flex justify-content-center gap-2 mt-1">
                        @if($product->reviews_avg_rating)
                            <span><i class="bi bi-star-fill text-warning"></i> {{ number_format($product->reviews_avg_rating, 1) }}</span>
                            <span>• {{ $product->reviews_count ?? $product->reviews->count() }} đánh giá</span>
                        @else
                            <span><i class="bi bi-star text-muted"></i> Chưa có đánh giá</span>
                        @endif
                    </div>
                </div>

                <!-- ACTION ROW: Nút Thêm vào giỏ hàng tinh gọn, chuẩn e-commerce -->
                <div class="action-row pt-2 border-top mt-auto">
                    @if($productStock > 0)
                        <button type="button" class="btn btn-sm btn-primary rounded-pill w-100 py-2 fw-semibold btn-quick-add shadow-sm d-flex align-items-center justify-content-center gap-2" data-quick-add-id="{{ $product->id }}" data-quick-add-slug="{{ $product->slug }}" data-has-variations="{{ $product->variations->isNotEmpty() ? 'true' : 'false' }}" title="Thêm vào giỏ" aria-label="Thêm {{ $product->name }} vào giỏ">
                            <i class="bi bi-bag-plus"></i>
                            <span>{{ $product->variations->isNotEmpty() ? 'Chọn mua' : 'Thêm vào giỏ' }}</span>
                        </button>
                    @else
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill w-100 py-2 fw-semibold disabled" disabled>
                            <i class="bi bi-slash-circle me-1"></i>Hết hàng
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 py-4">
        <x-empty-state 
            type="search" 
            title="Không tìm thấy sản phẩm nào" 
            description="Hãy thử thay đổi tiêu chí bộ lọc, mức giá hoặc tìm kiếm với từ khóa khác nhé." 
            action-label="Xóa tất cả bộ lọc" 
            action-url="{{ route('products.index') }}" 
        />
    </div>
@endforelse
