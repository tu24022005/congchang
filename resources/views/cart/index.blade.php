@extends('layouts.app')
@section('title', 'Giỏ hàng của bạn')
@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
<div class="container py-4 cart-shopee-page">
    @php
        $discountVoucher = session('voucher_discount');
        $shippingVoucher = session('voucher_shipping');
        $discount = 0;
        if ($discountVoucher) {
            $discount = $discountVoucher['type'] === 'fixed'
                ? $discountVoucher['value']
                : $total * ($discountVoucher['value'] / 100);
            $discount = min($discount, $total);
        }
        $serviceFee = 0;
        $finalTotal = $total - $discount + $serviceFee;
    @endphp
    <div class="cart-page-heading mb-4"><div><span class="cart-eyebrow">ALOHA BEAUTY / GIỎ HÀNG</span><h2 class="fw-bold storefront-title mb-1"><i class="bi bi-cart3 me-2"></i>Giỏ hàng của bạn</h2><p class="text-muted mb-0">Phí vận chuyển sẽ được tính theo khu vực ở bước thanh toán.</p></div><a href="{{ route('products.index') }}" class="btn btn-light border rounded-pill"><i class="bi bi-plus-lg me-1"></i>Thêm sản phẩm</a></div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('cart') && count(session('cart')) > 0)
        <div class="row g-4">
            <!-- CỘT TRÁI: DANH SÁCH & THÔNG TIN GIAO HÀNG -->
            <div class="col-lg-8">
                <!-- Danh sách sản phẩm -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 storefront-panel-card cart-items-card">
                    <div class="card-body p-0">
                        <div class="cart-card-heading"><div><h5 class="fw-bold mb-1">Sản phẩm đã chọn</h5><small class="text-muted">{{ count(session('cart')) }} sản phẩm sẵn sàng thanh toán</small></div><div class="d-flex align-items-center gap-2"><span class="cart-secure-badge"><i class="bi bi-shield-check me-1"></i>An toàn</span><form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?');">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger p-0" title="Xóa toàn bộ giỏ hàng"><i class="bi bi-trash3"></i></button></form></div></div>
                        <div class="table-responsive">
                            <table class="table align-middle text-center mb-0 cart-shopee-table">
                                <thead>
                                    <tr>
                                        <th class="ps-4 text-start"><input type="checkbox" class="cart-checkbox-input" id="cart-select-all" aria-label="Chọn tất cả sản phẩm" checked> <label for="cart-select-all">Sản phẩm</label></th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Số tiền</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="cart-shop-row">
                                        <td colspan="5" class="text-start ps-4"><input type="checkbox" class="cart-checkbox-input cart-shop-select" aria-label="Chọn tất cả sản phẩm của Aloha Beauty" checked> <strong>Aloha Beauty</strong><span class="cart-shop-label">Yêu thích</span></td>
                                    </tr>
                                    @php $total = 0; @endphp
                                    @foreach(session('cart') as $id => $details)
                                        @php $total += $details['price'] * $details['quantity']; @endphp
                                        <tr class="cart-item-row" data-unit-price="{{ $details['price'] }}">
                                            <td class="text-start ps-4">
                                                <div class="d-flex align-items-center cart-product-cell">
                                                    <input type="checkbox" class="cart-checkbox-input cart-product-select" aria-label="Chọn {{ $details['name'] }}" checked>
                                                    @if(isset($details['image']) && $details['image'])
                                                        <img src="{{ asset('storage/' . $details['image']) }}" width="72" height="72" class="img-thumbnail rounded-3 shadow-sm me-3 cart-product-image">
                                                    @else
                                                        <div class="bg-light rounded-3 border me-3 storefront-thumb-placeholder cart-product-image"></div>
                                                    @endif
                                                    <div class="cart-product-info">
                                                        <div class="fw-bold cart-product-name">{{ $details['name'] }}</div>
                                                        @if(isset($details['variation']))
                                                            <small class="text-muted">Phân loại: <span class="badge bg-info text-dark">{{ $details['variation'] }}</span></small>
                                                        @else
                                                            <small class="text-muted">Phân loại: <span class="badge bg-secondary">Mặc định</span></small>
                                                        @endif
                                                    </div>
                                                </div>
                                            <td>
                                                @if(!empty($details['promotion_label']))<span class="badge bg-danger d-block mb-1">{{ $details['promotion_label'] }}</span><span class="text-muted text-decoration-line-through small">{{ number_format($details['original_price'], 0, ',', '.') }} đ</span><br>@endif
                                                <span class="cart-unit-price">{{ number_format($details['price'], 0, ',', '.') }} đ</span>
                                            </td>
                                            <td>
                                                <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex justify-content-center">
                                                    @csrf
                                                    <!-- ĐÃ SỬA TỪ PUT THÀNH PATCH Ở ĐÂY -->
                                                    @method('PATCH')
                                                    <div class="quantity-control"><button type="button" class="quantity-step" data-step="-1">−</button><input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control form-control-sm text-center quantity-input" min="1"><button type="button" class="quantity-step" data-step="1">+</button></div><button type="submit" class="btn btn-sm btn-outline-primary ms-2" title="Cập nhật số lượng"><i class="bi bi-check2"></i></button>
                                                </form>
                                            </td>
                                            <td class="text-danger fw-bold line-total">{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }} đ</td>
                                            <td>
                                                <!-- Đã cập nhật thành cart.destroy để sửa lỗi route -->
                                                <form action="{{ route('cart.destroy', $id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Xoá</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill mb-4">
                    <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                </a>

                <!-- BẮT ĐẦU FORM ĐẶT HÀNG CHÍNH -->
                <form action="{{ route('orders.store') }}" method="POST" id="checkout-order-form">
                    @csrf
                    <div class="card border-0 shadow-sm rounded-4 storefront-panel-card">
                        <div class="card-body p-4">
                            <div class="cart-card-heading border-bottom pb-3 mb-3"><div><h5 class="fw-bold mb-1"><i class="bi bi-geo-alt text-primary me-2"></i>Thông tin giao hàng</h5><small class="text-muted">Chọn vị trí trên bản đồ để địa chỉ chính xác hơn.</small></div><span class="step-badge">02</span></div>
                            @if ($addresses->isNotEmpty())
                                <div class="mb-3">
                                    <label for="cart-saved-address" class="form-label text-muted small">Chọn địa chỉ đã lưu:</label>
                                    <select id="cart-saved-address" name="address_id" class="form-select rounded-3">
                                        <option value="">Nhập địa chỉ mới</option>
                                        @foreach ($addresses as $address)
                                            <option value="{{ $address->id }}" data-name="{{ $address->recipient_name }}" data-phone="{{ $address->phone }}" data-address="{{ $address->address }}" @selected(old('address_id', $address->is_default ? $address->id : '') == $address->id)>{{ $address->label }} - {{ $address->recipient_name }} - {{ $address->phone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label text-muted small">Họ và tên người nhận:</label>
                                <input type="text" name="customer_name" class="form-control rounded-3" value="{{ old('customer_name', $addresses->firstWhere('is_default', true)?->recipient_name ?? Auth::user()->name ?? '') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Số điện thoại liên hệ:</label>
                                <input type="tel" name="customer_phone" class="form-control rounded-3" placeholder="Ví dụ: 0987654321" value="{{ old('customer_phone', $addresses->firstWhere('is_default', true)?->phone) }}" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label text-muted small">Địa chỉ nhận hàng chi tiết:</label>
                                <textarea name="customer_address" id="customer-address" class="form-control rounded-3" rows="2" placeholder="Số nhà, Tên đường, Phường/Xã..." required>{{ old('customer_address', $addresses->firstWhere('is_default', true)?->address) }}</textarea>
                                <input type="hidden" name="latitude" id="delivery-latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="delivery-longitude" value="{{ old('longitude') }}">
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label for="shipping-zone" class="form-label text-muted small">Khu vực giao hàng:</label>
                                    <select name="shipping_zone" id="shipping-zone" class="form-select rounded-3" required>
                                        <option value="">-- Chọn khu vực --</option>
                                        @foreach(config('shop.shipping_zones', []) as $key => $zone)
                                            <option value="{{ $key }}" data-fee="{{ $zone['fee'] }}" @selected(old('shipping_zone') === $key)>{{ $zone['label'] }} - {{ number_format($zone['fee'], 0, ',', '.') }}đ</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping-provider" class="form-label text-muted small">Đơn vị vận chuyển:</label>
                                    <select name="shipping_provider" id="shipping-provider" class="form-select rounded-3" required>
                                        <option value="">-- Chọn đơn vị --</option>
                                        @foreach(config('shop.shipping_providers', []) as $key => $label)
                                            <option value="{{ $key }}" @selected(old('shipping_provider') === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="delivery-map-tools mt-3"><div class="input-group"><input type="search" name="map_search" id="map-search" class="form-control" placeholder="Tìm địa chỉ trên bản đồ..." value="{{ old('map_search') }}"><button type="button" id="map-search-button" class="btn btn-primary"><i class="bi bi-search"></i></button></div><button type="button" id="use-current-location" class="btn btn-sm btn-outline-primary mt-2"><i class="bi bi-crosshair me-1"></i>Dùng vị trí hiện tại</button><span id="map-status" class="small text-muted ms-2"></span></div>
                            <div id="delivery-map" class="delivery-map mt-3"></div>
                        </div>
                    </div>
            </div>

            <!-- CỘT PHẢI: VOUCHER VÀ TỔNG ĐƠN -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 storefront-panel-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-bottom pb-3 text-center">TỔNG ĐƠN HÀNG</h5>
                        
                        <!-- THUẬT TOÁN TÍNH TIỀN ĐƯỢC GIẢM -->
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-bold" id="cart-subtotal">{{ number_format($total, 0, ',', '.') }} đ</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Phí giao hàng:</span>
                            <span class="text-success fw-bold">Miễn phí</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="fw-bold" id="cart-service-fee">Chọn khu vực</span>
                        </div>

                        <!-- HIỂN THỊ DÒNG TIỀN ĐƯỢC GIẢM -->
                        @if($discountVoucher || $shippingVoucher)
                            <div class="d-flex justify-content-between mb-3 text-success">
                                <span>
                                    <i class="bi bi-tag-fill me-1"></i> Voucher đã chọn:
                                </span>
                                <span class="fw-bold text-end">
                                    @if($discountVoucher) <span id="cart-discount-code">{{ $discountVoucher['code'] }}</span> (-<span id="cart-discount">{{ number_format($discount, 0, ',', '.') }}</span> đ) @endif
                                    @if($shippingVoucher)<br>{{ $shippingVoucher['code'] }} (Free ship)@endif
                                </span>
                            </div>
                        @endif

                        <!-- KHU VỰC NHẬP VOUCHER -->
                        <div class="mb-3 border-top pt-3 voucher-box">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label text-dark fw-bold mb-0"><i class="bi bi-ticket-perforated text-warning me-1"></i> Voucher của bạn</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#voucherPickerModal">Chọn hoặc nhập mã</button>
                            </div>
                            <div class="small text-muted">
                                @if($discountVoucher)<i class="bi bi-check-circle text-danger me-1"></i>Giảm tiền: {{ $discountVoucher['code'] }}<br>@endif
                                @if($shippingVoucher)<i class="bi bi-check-circle text-success me-1"></i>Free ship: {{ $shippingVoucher['code'] }}@endif
                                @if(!$discountVoucher && !$shippingVoucher) Chưa chọn voucher @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-3 mb-4">
                            <span class="fw-bold fs-5">Thành tiền:</span>
                            <span class="fw-bold fs-4 text-danger" id="cart-final-total">{{ number_format($finalTotal, 0, ',', '.') }} đ</span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small">Phương thức thanh toán:</label>
                            <select name="payment_method" class="form-select border-secondary rounded-3">
                                <option value="COD" @selected(old('payment_method', 'COD') === 'COD')>Thanh toán khi nhận hàng (COD)</option>
                                <option value="PAYOS" @selected(old('payment_method') === 'PAYOS')>Chuyển khoản Ngân hàng (PayOS)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold text-uppercase shadow-sm voucher-submit">
                            XÁC NHẬN ĐẶT HÀNG
                        </button>
                    </div>
                </div>
                </form> <!-- KẾT THÚC FORM ĐẶT HÀNG -->
            </div>
        </div>
    @else
        <div class="text-center py-5 bg-light rounded-4 shadow-sm mt-4 border">
            <i class="bi bi-cart-x text-muted empty-cart-icon"></i>
            <h4 class="mt-3 text-muted">Giỏ hàng của bạn đang trống</h4>
            <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-4 mt-3 shadow-sm">Khám phá sản phẩm ngay</a>
        </div>
    @endif
</div>

@if(isset($vouchers))
<div class="modal fade voucher-picker-modal" id="voucherPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-ticket-perforated text-warning me-2"></i>Chọn voucher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-4">
                    <input type="text" id="voucher-code-modal" class="form-control text-uppercase" placeholder="Nhập mã voucher">
                    <button type="button" class="btn btn-dark" id="apply-voucher-modal">Áp dụng mã</button>
                </div>
                <p class="small text-muted mb-3">Có thể tích cùng lúc 1 mã giảm tiền và 1 mã free ship.</p>
                <form action="{{ route('cart.apply_vouchers') }}" method="POST" id="selected-vouchers-form">
                    @csrf
                <div class="mt-3">
                    <h6 class="fw-bold border-bottom pb-2"><i class="bi bi-globe2 text-primary me-2"></i>Mã giảm tiền toàn sàn</h6>
                    @forelse($vouchers->where('scope', 'platform')->whereIn('type', ['fixed', 'percent']) as $voucher)
                        <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center">
                            <label class="d-flex align-items-center gap-3 w-100 mb-0">
                                <input type="checkbox" class="form-check-input voucher-choice" data-voucher-type="discount" name="voucher_codes[]" value="{{ $voucher->code }}">
                                <span><strong class="text-primary">{{ $voucher->code }}</strong><span class="d-block small text-muted">Giảm {{ $voucher->type === 'fixed' ? number_format($voucher->value, 0, ',', '.') . ' đ' : $voucher->value . '%' }} · Đơn từ {{ number_format($voucher->min_order_value, 0, ',', '.') }} đ</span></span>
                            </label>
                        </div>
                    @empty
                        <p class="small text-muted">Hiện chưa có mã giảm tiền toàn sàn.</p>
                    @endforelse
                </div>
                <div class="mt-3">
                    <h6 class="fw-bold border-bottom pb-2"><i class="bi bi-truck text-info me-2"></i>Free ship toàn sàn</h6>
                    @forelse($vouchers->where('scope', 'platform')->where('type', 'free_shipping') as $voucher)
                        <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center">
                            <label class="d-flex align-items-center gap-3 w-100 mb-0">
                                <input type="checkbox" class="form-check-input voucher-choice" data-voucher-type="shipping" name="voucher_codes[]" value="{{ $voucher->code }}">
                                <span><strong class="text-info">{{ $voucher->code }}</strong><span class="d-block small text-muted">Miễn phí vận chuyển · Đơn từ {{ number_format($voucher->min_order_value, 0, ',', '.') }} đ</span></span>
                            </label>
                        </div>
                    @empty
                        <p class="small text-muted">Hiện chưa có mã free ship toàn sàn.</p>
                    @endforelse
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill mt-3">Áp dụng voucher đã chọn</button>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Trở lại</button></div>
        </div>
    </div>
</div>
@endif
@push('scripts')
    <style>
        #voucherPickerModal { z-index: 2000 !important; }
        #voucherPickerModal .modal-dialog,
        #voucherPickerModal .modal-content { position: relative; z-index: 2001; }
        .modal-backdrop.show { z-index: 1990 !important; }
    </style>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('cart-select-all');
        const shopSelect = document.querySelector('.cart-shop-select');
        const productSelects = Array.from(document.querySelectorAll('.cart-product-select'));

        function syncSelectAllState() {
            if (!selectAll || !productSelects.length) return;
            const selectedCount = productSelects.filter(input => input.checked).length;
            selectAll.checked = selectedCount === productSelects.length;
            selectAll.indeterminate = selectedCount > 0 && selectedCount < productSelects.length;
            if (shopSelect) {
                shopSelect.checked = selectAll.checked;
                shopSelect.indeterminate = selectAll.indeterminate;
            }
        }

        function setProductsSelected(checked) {
            productSelects.forEach(input => { input.checked = checked; });
            syncSelectAllState();
            window.refreshCartTotals?.();
        }

        selectAll?.addEventListener('change', function () {
            setProductsSelected(this.checked);
        });
        shopSelect?.addEventListener('change', function () {
            setProductsSelected(this.checked);
        });
        productSelects.forEach(input => input.addEventListener('change', function () {
            syncSelectAllState();
            window.refreshCartTotals?.();
        }));

        document.querySelectorAll('.quantity-step').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = this.closest('.quantity-control').querySelector('input');
                const nextValue = Math.max(1, Number(input.value || 1) + Number(this.dataset.step));
                input.value = nextValue;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });
        productSelects.forEach(input => { input.checked = true; });
        syncSelectAllState();
        window.refreshCartTotals?.();
        const mapElement = document.getElementById('delivery-map');
        if (!mapElement) return;
        const addressInput = document.getElementById('customer-address');
        const latitudeInput = document.getElementById('delivery-latitude');
        const longitudeInput = document.getElementById('delivery-longitude');
        const status = document.getElementById('map-status');
        const shippingZone = document.getElementById('shipping-zone');
        const defaultPosition = [21.0285, 105.8542];
        const map = L.map(mapElement).setView(defaultPosition, 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
        let marker;

        function updateShippingZone(address = {}, label = '') {
            if (!shippingZone) return;
            const locationText = [
                address.city,
                address.town,
                address.municipality,
                address.state,
                address.county,
                label,
            ].filter(Boolean).join(' ').toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            const isInnerCity = locationText.includes('ha noi')
                || locationText.includes('hanoi')
                || locationText.includes('ho chi minh')
                || locationText.includes('thanh pho ho chi minh')
                || locationText.includes('sai gon')
                || locationText.includes('saigon');
            const zone = isInnerCity ? 'inner_city' : 'other_city';
            if (shippingZone.querySelector(`option[value="${zone}"]`)) {
                shippingZone.value = zone;
                shippingZone.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        function setLocation(latitude, longitude, label, address = {}) {
            latitudeInput.value = latitude.toFixed(7);
            longitudeInput.value = longitude.toFixed(7);
            if (!marker) marker = L.marker([latitude, longitude], { draggable: true }).addTo(map);
            marker.setLatLng([latitude, longitude]);
            marker.off('dragend').on('dragend', event => { const position = event.target.getLatLng(); reverseGeocode(position.lat, position.lng).catch(() => {}); });
            marker.bindPopup('Vị trí giao hàng').openPopup();
            map.setView([latitude, longitude], 16);
            if (label) addressInput.value = label;
            updateShippingZone(address, label);
            status.textContent = 'Đã chọn vị trí';
            status.className = 'small text-success ms-2';
        }

        async function reverseGeocode(latitude, longitude) {
            status.textContent = 'Đang lấy địa chỉ...';
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`, { headers: { 'Accept-Language': 'vi' } });
            const data = await response.json();
            setLocation(latitude, longitude, data.display_name || 'Vị trí đã chọn', data.address || {});
        }

        map.on('click', event => reverseGeocode(event.latlng.lat, event.latlng.lng).catch(() => status.textContent = 'Không lấy được địa chỉ, bạn có thể nhập tay.'));
        document.getElementById('map-search-button').addEventListener('click', async function () {
            const query = document.getElementById('map-search').value.trim();
            if (!query) return;
            status.textContent = 'Đang tìm...';
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&addressdetails=1&countrycodes=vn&q=${encodeURIComponent(query)}`, { headers: { 'Accept-Language': 'vi' } });
                const results = await response.json();
                if (!results.length) throw new Error();
                setLocation(Number(results[0].lat), Number(results[0].lon), results[0].display_name, results[0].address || {});
            } catch (error) { status.textContent = 'Không tìm thấy địa chỉ'; }
        });
        document.getElementById('use-current-location').addEventListener('click', function () { if (!navigator.geolocation) return; status.textContent = 'Đang lấy vị trí...'; navigator.geolocation.getCurrentPosition(position => reverseGeocode(position.coords.latitude, position.coords.longitude), () => status.textContent = 'Trình duyệt chưa cho phép định vị.'); });
        if (addressInput.value.trim()) document.getElementById('map-search').value = addressInput.value;
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const voucherModal = document.getElementById('voucherPickerModal');
        if (voucherModal && voucherModal.parentElement !== document.body) {
            document.body.appendChild(voucherModal);
        }
    });

    document.getElementById('apply-voucher-modal')?.addEventListener('click', function () {
        const code = document.getElementById('voucher-code-modal').value.trim();
        if (!code) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = @json(route('cart.apply_voucher'));
        form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}"><input type="hidden" name="voucher_code" value="${code}">`;
        preserveCheckoutDetails(form);
        document.body.appendChild(form);
        form.submit();
    });

    function preserveCheckoutDetails(targetForm) {
        const checkoutForm = document.getElementById('checkout-order-form');
        if (!checkoutForm) return;

        checkoutForm.querySelectorAll('[name]').forEach(function (field) {
            if (field.name === 'payment_method' && !field.checked) return;
            if (field.type === 'submit' || field.type === 'button' || field.name === 'voucher_codes[]') return;

            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = field.name;
            hidden.value = field.value;
            targetForm.appendChild(hidden);
        });
    }

    document.getElementById('selected-vouchers-form')?.addEventListener('submit', function () {
        preserveCheckoutDetails(this);
    });

    document.getElementById('cart-saved-address')?.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        const name = document.querySelector('#checkout-order-form [name="customer_name"]');
        const phone = document.querySelector('#checkout-order-form [name="customer_phone"]');
        const address = document.querySelector('#checkout-order-form [name="customer_address"]');

        if (!this.value) {
            name.value = '';
            phone.value = '';
            address.value = '';
            return;
        }

        name.value = option.dataset.name || '';
        phone.value = option.dataset.phone || '';
        address.value = option.dataset.address || '';
    });

    document.querySelectorAll('.voucher-choice').forEach(function (choice) {
        choice.addEventListener('change', function () {
            if (!this.checked) return;
            document.querySelectorAll('.voucher-choice[data-voucher-type="' + this.dataset.voucherType + '"]').forEach(function (other) {
                if (other !== choice) other.checked = false;
            });
        });
    });

    const money = value => new Intl.NumberFormat('vi-VN').format(Math.round(value));
    const discountType = @json($discountVoucher['type'] ?? null);
    const discountValue = Number(@json($discountVoucher['value'] ?? 0));
    const hasShippingVoucher = @json((bool) $shippingVoucher);
    const shippingZone = document.getElementById('shipping-zone');

    function refreshCartTotals() {
        let subtotal = 0;
        document.querySelectorAll('.cart-item-row').forEach(function (row) {
            const input = row.querySelector('.quantity-input');
            const quantity = Math.max(1, Number(input.value || 1));
            input.value = quantity;
            const lineTotal = Number(row.dataset.unitPrice) * quantity;
            const productSelect = row.querySelector('.cart-product-select');
            if (productSelect?.checked) subtotal += lineTotal;
            row.querySelector('.line-total').textContent = money(lineTotal) + ' đ';
        });

        let discount = 0;
        if (discountType === 'fixed') discount = discountValue;
        if (discountType === 'percent') discount = subtotal * discountValue / 100;
        discount = Math.min(discount, subtotal);
        const selectedZone = shippingZone?.options[shippingZone.selectedIndex];
        const shippingFee = hasShippingVoucher ? 0 : Number(selectedZone?.dataset.fee || 0);
        const total = subtotal - discount + shippingFee;

        document.getElementById('cart-subtotal').textContent = money(subtotal) + ' đ';
        document.getElementById('cart-discount')?.replaceChildren(document.createTextNode(money(discount)));
        document.getElementById('cart-service-fee').textContent = hasShippingVoucher
            ? 'Miễn phí'
            : shippingFee > 0 ? money(shippingFee) + ' đ' : 'Chọn khu vực';
        document.getElementById('cart-final-total').textContent = money(total) + ' đ';
    }
    window.refreshCartTotals = refreshCartTotals;

    document.querySelectorAll('.quantity-input').forEach(function (input) {
        input.addEventListener('input', function () {
            refreshCartTotals();
            clearTimeout(input.form.dataset.updateTimer);
            input.form.dataset.updateTimer = setTimeout(() => input.form.submit(), 500);
        });
    });
    shippingZone?.addEventListener('change', refreshCartTotals);
    refreshCartTotals();
    </script>
@endpush
@endsection