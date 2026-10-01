@php
    $freeShippingThreshold = (float) config('shop.free_shipping_threshold', 500000);
    if (Auth::check()) {
        $drawerCartCount = (int) Auth::user()->cartItems()->sum('quantity');
        $drawerCartItems = Auth::user()->cartItems()->with(['product.category', 'variation'])->latest()->get()->map(function ($item) {
            $variationLabel = $item->variation
                ? collect([$item->variation->sku, $item->variation->color, $item->variation->size_value ? rtrim(rtrim($item->variation->size_value, '0'), '.') . $item->variation->size_unit : null, $item->variation->storage])->filter()->implode(' · ')
                : null;
            $key = $item->variation ? $item->product_id . ':' . $item->variation->id : (string) $item->product_id;
            return [
                'key' => $key,
                'name' => $item->product->name ?? 'Sản phẩm',
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'image' => $item->product->image ?? '',
                'slug' => $item->product->slug ?? '',
                'variation' => $variationLabel,
                'stock' => $item->variation?->stock ?? $item->product?->quantity ?? 999,
            ];
        })->toArray();
    } else {
        $sessionCart = session()->get('cart', []);
        $drawerCartCount = (int) collect($sessionCart)->sum('quantity');
        $drawerCartItems = [];
        foreach ($sessionCart as $key => $item) {
            $parts = explode(':', (string)$key);
            $drawerCartItems[] = [
                'key' => (string) $key,
                'name' => $item['name'] ?? 'Sản phẩm',
                'price' => (float) ($item['price'] ?? 0),
                'quantity' => (int) ($item['quantity'] ?? 1),
                'image' => $item['image'] ?? '',
                'slug' => $item['slug'] ?? ($parts[0] ?? ''),
                'variation' => $item['variation'] ?? null,
                'stock' => (int) ($item['stock'] ?? 999),
            ];
        }
    }
    $drawerSubtotal = collect($drawerCartItems)->sum(fn ($i) => $i['price'] * $i['quantity']);
    $neededForFreeShipping = max(0, $freeShippingThreshold - $drawerSubtotal);
    $freeShippingProgress = $freeShippingThreshold > 0 ? min(100, round(($drawerSubtotal / $freeShippingThreshold) * 100)) : 100;
@endphp

<!-- OFFCANVAS MINI-CART DRAWER -->
<div class="offcanvas offcanvas-end mini-cart-drawer shadow-lg border-0" tabindex="-1" id="cartOffcanvasDrawer" aria-labelledby="cartDrawerLabel" class="view-inline-1" data-freeship-threshold="{{ $freeShippingThreshold }}">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="cartDrawerLabel">
            <i class="bi bi-bag-heart text-danger fs-4"></i>
            <span>Giỏ hàng</span>
            <span class="badge bg-danger rounded-pill" id="drawer-header-count">{{ $drawerCartCount }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
    </div>

    <!-- THANH TIẾN TRÌNH FREESHIP -->
    <div class="p-3 border-bottom bg-light-subtle" id="drawer-freeship-wrap">
        <div class="small mb-2 d-flex justify-content-between align-items-center" id="drawer-freeship-text">
            @if($neededForFreeShipping > 0)
                <span>Mua thêm <strong class="text-danger" id="drawer-freeship-needed">{{ number_format($neededForFreeShipping, 0, ',', '.') }}đ</strong> để được <strong>FREESHIP</strong>! 🚚</span>
            @else
                <span class="text-success fw-bold"><i class="bi bi-patch-check-fill me-1"></i>Đơn hàng đủ điều kiện MIỄN PHÍ VẬN CHUYỂN! 🎉</span>
            @endif
            <span class="text-muted small fw-semibold" id="drawer-freeship-percent">{{ $freeShippingProgress }}%</span>
        </div>
        <div class="progress view-inline-2">
            <div id="drawer-freeship-bar" class="progress-bar {{ $neededForFreeShipping > 0 ? 'bg-danger' : 'bg-success' }}" role="progressbar" data-inline-width="{{ $freeShippingProgress }}" class="inline-dynamic-width" aria-valuenow="{{ $freeShippingProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <!-- DANH SÁCH MÓN TRONG GIỎ HÀNG -->
    <div class="offcanvas-body p-3" id="drawer-cart-body" class="view-inline-3">
        <div id="drawer-items-list" class="d-flex flex-column gap-3">
            @forelse($drawerCartItems as $item)
                <div class="drawer-item card border-0 shadow-sm p-2 rounded-3" data-cart-key="{{ $item['key'] }}">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('images/placeholder.svg') }}" class="rounded-3 object-fit-cover flex-shrink-0" width="64" height="64" alt="{{ $item['name'] }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate text-dark small mb-1" title="{{ $item['name'] }}">{{ $item['name'] }}</div>
                            @if(!empty($item['variation']))
                                <div class="text-muted view-inline-4">Phân loại: {{ $item['variation'] }}</div>
                            @endif
                            <div class="text-danger fw-bold small mt-1">{{ number_format($item['price'], 0, ',', '.') }} ₫</div>
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <div class="drawer-qty-picker d-inline-flex align-items-center border rounded-2 bg-light">
                                    <button type="button" class="btn btn-sm btn-link text-dark p-0 px-2 text-decoration-none" onclick="window.updateDrawerCartItem('{{ $item['key'] }}', -1)">−</button>
                                    <span class="px-2 small fw-bold drawer-item-qty view-inline-5">{{ $item['quantity'] }}</span>
                                    <button type="button" class="btn btn-sm btn-link text-dark p-0 px-2 text-decoration-none" onclick="window.updateDrawerCartItem('{{ $item['key'] }}', 1)">+</button>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" onclick="window.removeDrawerCartItem('{{ $item['key'] }}')" title="Xóa khỏi giỏ">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5" id="drawer-empty-msg">
                    <i class="bi bi-cart-x text-muted display-4"></i>
                    <p class="text-muted mt-3 mb-3">Giỏ hàng của bạn đang trống</p>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-4" data-bs-dismiss="offcanvas">
                        Khám phá sản phẩm
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- FOOTER GIỎ HÀNG -->
    <div class="offcanvas-footer border-top p-3 bg-white" id="drawer-cart-footer">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted small">Tạm tính giỏ hàng:</span>
            <strong class="fs-5 text-danger" id="drawer-total-price">{{ number_format($drawerSubtotal, 0, ',', '.') }} ₫</strong>
        </div>
        <div class="d-grid gap-2">
            <a href="{{ route('checkout') }}" class="btn btn-success py-2 rounded-pill fw-bold text-uppercase shadow-sm">
                <i class="bi bi-credit-card me-2"></i>Thanh toán ngay
            </a>
            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary py-2 rounded-pill fw-semibold btn-sm">
                Xem giỏ hàng đầy đủ
            </a>
        </div>
    </div>
</div>
