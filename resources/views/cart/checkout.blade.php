@extends('layouts.app')
@section('title', 'Thanh toán đơn hàng - BeatyCare 🌸')
@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        .checkout-page {
            max-width: 1200px;
        }
        .checkout-panel-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        body.dark-mode .checkout-panel-card {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .checkout-step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #ff6b81, #ff8fa3);
            color: #fff;
            border-radius: 50%;
            font-size: 0.82rem;
            font-weight: 700;
        }
        .delivery-map {
            height: 240px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            z-index: 1;
        }
        body.dark-mode .delivery-map {
            border-color: #334155;
        }
        .checkout-product-row:last-child {
            border-bottom: 0 !important;
        }
        .order-summary-sticky {
            position: sticky;
            top: 90px;
        }
    </style>
@endpush

@section('content')
<div class="container py-4 checkout-page">
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
        $finalTotal = max(0, $total - $discount);
    @endphp

    <!-- HEADER TIÊU ĐỀ TRANG THANH TOÁN -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="text-uppercase fw-bold small text-primary tracking-wide">
                <i class="bi bi-shield-check me-1"></i>BEATYCARE 🌸 / THANH TOÁN
            </span>
            <h2 class="fw-bold mb-1 mt-1 text-dark">
                <i class="bi bi-credit-card-2-front text-primary me-2"></i>Thanh toán đơn hàng
            </h2>
            <p class="text-muted mb-0">Kiểm tra thông tin giao hàng và xác nhận đặt mua sản phẩm của bạn.</p>
        </div>
        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-cart3 me-1"></i>Xem lại giỏ hàng
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <strong><i class="bi bi-exclamation-circle me-1"></i>Vui lòng kiểm tra lại thông tin:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST" id="checkout-order-form">
        @csrf
        <div class="row g-4">
            <!-- CỘT TRÁI: SẢN PHẨM & ĐỊA CHỈ GIAO HÀNG (COL-LG-8) -->
            <div class="col-lg-8">
                
                <!-- 1. DANH SÁCH SẢN PHẨM ĐẶT MUA -->
                <div class="card checkout-panel-card mb-4">
                    <div class="card-header bg-transparent border-bottom p-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="checkout-step-badge">1</span>
                            <h5 class="fw-bold mb-0">Sản phẩm đặt mua</h5>
                        </div>
                        <span class="badge bg-light text-muted border">{{ count($cart) }} sản phẩm</span>
                    </div>
                    <div class="card-body p-0">
                        @foreach($cart as $id => $details)
                            <div class="d-flex align-items-center justify-content-between p-3 border-bottom checkout-product-row">
                                <div class="d-flex align-items-center gap-3">
                                    @if(isset($details['image']) && $details['image'])
                                        <img src="{{ asset('storage/' . $details['image']) }}" alt="{{ $details['name'] }}" width="64" height="64" class="rounded-3 border object-fit-cover shadow-sm" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                                    @else
                                        <div class="bg-light rounded-3 border d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                            <i class="bi bi-flower1 text-muted fs-3"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $details['name'] }}</div>
                                        @if(!empty($details['variation']))
                                            <small class="text-muted d-block mt-1">Phân loại: <span class="badge bg-light text-dark border">{{ $details['variation'] }}</span></small>
                                        @endif
                                        <div class="text-muted small mt-1">
                                            <span>{{ number_format($details['price'], 0, ',', '.') }} đ</span> × <strong class="text-dark">{{ $details['quantity'] }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-danger fs-6">{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }} đ</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 2. THÔNG TIN NGƯỜI NHẬN & ĐỊA CHỈ GIAO HÀNG -->
                <div class="card checkout-panel-card mb-4">
                    <div class="card-header bg-transparent border-bottom p-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="checkout-step-badge">2</span>
                            <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Địa chỉ nhận hàng</h5>
                        </div>
                        <small class="text-muted">Chọn vị trí bản đồ hoặc nhập chi tiết</small>
                    </div>
                    <div class="card-body p-4">
                        @if ($addresses->isNotEmpty())
                            <div class="mb-3">
                                <label for="saved-address" class="form-label text-muted small fw-semibold">Chọn địa chỉ đã lưu trong sổ địa chỉ:</label>
                                <select id="saved-address" name="address_id" class="form-select rounded-3">
                                    <option value="">-- Nhập thông tin địa chỉ mới bên dưới --</option>
                                    @foreach ($addresses as $address)
                                        <option value="{{ $address->id }}" 
                                            data-name="{{ $address->recipient_name }}" 
                                            data-phone="{{ $address->phone }}" 
                                            data-address="{{ $address->address }}" 
                                            @selected(old('address_id', $address->is_default ? $address->id : '') == $address->id)>
                                            {{ $address->label }} - {{ $address->recipient_name }} ({{ $address->phone }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Họ và tên người nhận <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control rounded-3" 
                                    value="{{ old('customer_name', $addresses->firstWhere('is_default', true)?->recipient_name ?? Auth::user()->name ?? '') }}" 
                                    minlength="2" maxlength="120" placeholder="Nguyễn Văn A" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                                <input type="tel" name="customer_phone" class="form-control rounded-3" 
                                    value="{{ old('customer_phone', $addresses->firstWhere('is_default', true)?->phone ?? '') }}" 
                                    placeholder="Ví dụ: 0987654321" pattern="(0|\+84)(3|5|7|8|9)[0-9]{8}" maxlength="12" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Địa chỉ nhận hàng chi tiết <span class="text-danger">*</span></label>
                            <textarea name="customer_address" id="checkout-customer-address" class="form-control rounded-3" rows="2" 
                                placeholder="Số nhà, tên toà nhà, tên đường..." required>{{ old('customer_address', $addresses->firstWhere('is_default', true)?->address ?? '') }}</textarea>
                            
                            <div class="row g-2 mt-2" data-vn-address>
                                <div class="col-md-4">
                                    <select class="form-select rounded-3" data-province>
                                        <option value="">Tỉnh/Thành phố</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select rounded-3" data-district disabled>
                                        <option value="">Quận/Huyện</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select rounded-3" data-ward disabled>
                                        <option value="">Phường/Xã</option>
                                    </select>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">Chọn Tỉnh/Thành, Quận/Huyện và Phường/Xã để tự động chuẩn hoá địa chỉ nhận hàng.</small>

                            <!-- BẢN ĐỒ VỊ TRÍ GIAO HÀNG LEAFLET -->
                            <div class="delivery-map-tools mt-3">
                                <div class="input-group">
                                    <input type="search" name="map_search" id="map-search" class="form-control rounded-start-3" placeholder="Tìm địa chỉ trên bản đồ..." value="{{ old('map_search') }}">
                                    <button type="button" id="map-search-button" class="btn btn-primary"><i class="bi bi-search me-1"></i>Tìm</button>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <button type="button" id="use-current-location" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-crosshair me-1"></i>Lấy vị trí hiện tại của tôi
                                    </button>
                                    <span id="map-status" class="small text-muted ms-3"></span>
                                </div>
                            </div>
                            <div id="delivery-map" class="delivery-map mt-3"></div>
                            <input type="hidden" name="latitude" id="delivery-latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="delivery-longitude" value="{{ old('longitude') }}">
                        </div>
                    </div>
                </div>

                <!-- 3. PHƯƠNG THỨC VẬN CHUYỂN -->
                <div class="card checkout-panel-card mb-4">
                    <div class="card-header bg-transparent border-bottom p-3 d-flex align-items-center gap-2">
                        <span class="checkout-step-badge">3</span>
                        <h5 class="fw-bold mb-0"><i class="bi bi-truck text-info me-1"></i>Hình thức vận chuyển</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="shipping-zone" class="form-label text-muted small fw-semibold">Khu vực giao hàng <span class="text-danger">*</span></label>
                                <select name="shipping_zone" id="shipping-zone" class="form-select rounded-3" required>
                                    <option value="">-- Chọn khu vực nhận hàng --</option>
                                    @foreach(config('shop.shipping_zones', []) as $key => $zone)
                                        <option value="{{ $key }}" data-fee="{{ $zone['fee'] }}" @selected(old('shipping_zone') === $key)>
                                            {{ $zone['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="shipping-provider" class="form-label text-muted small fw-semibold">Đơn vị vận chuyển <span class="text-danger">*</span></label>
                                <select name="shipping_provider" id="shipping-provider" class="form-select rounded-3" required>
                                    <option value="">-- Chọn đơn vị vận chuyển --</option>
                                    @foreach(config('shop.shipping_providers', []) as $key => $label)
                                        <option value="{{ $key }}" data-label="{{ $label }}" 
                                            data-fees="{{ json_encode(collect(config('shop.shipping_provider_fees', []))->mapWithKeys(fn ($fees, $zone) => [$zone => $fees[$key] ?? null])) }}" 
                                            @selected(old('shipping_provider') === $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>Phí vận chuyển được tính minh bạch theo khu vực. Nếu bạn áp dụng voucher Free Ship, phí ship sẽ được miễn phí 100%.
                        </small>
                    </div>
                </div>

            </div>

            <!-- CỘT PHẢI: TỔNG KẾT & PHƯƠNG THỨC THANH TOÁN (COL-LG-4) -->
            <div class="col-lg-4">
                <div class="card checkout-panel-card order-summary-sticky">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-3 text-center">
                            <i class="bi bi-receipt me-2 text-primary"></i>TỔNG ĐƠN HÀNG
                        </h5>
                        
                        <!-- CÁC DÒNG TIỀN -->
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính ({{ count($cart) }} món):</span>
                            <span class="fw-bold" id="checkout-subtotal">{{ number_format($total, 0, ',', '.') }} đ</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phí giao hàng:</span>
                            <span class="text-success fw-bold">Miễn phí cơ bản</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="fw-bold" id="checkout-shipping-fee">Chọn khu vực</span>
                        </div>

                        <!-- VOUCHER ĐÃ ÁP DỤNG -->
                        @if($discountVoucher || $shippingVoucher)
                            <div class="d-flex justify-content-between mb-3 text-success p-2 bg-success-subtle rounded-3">
                                <span class="small">
                                    <i class="bi bi-tag-fill me-1"></i>Voucher giảm:
                                </span>
                                <span class="fw-bold small text-end">
                                    @if($discountVoucher) -{{ number_format($discount, 0, ',', '.') }} đ ({{ $discountVoucher['code'] }})<br>@endif
                                    @if($shippingVoucher) Miễn phí ship ({{ $shippingVoucher['code'] }}) @endif
                                </span>
                            </div>
                        @endif

                        <!-- KHU VỰC CHỌN VOUCHER -->
                        <div class="mb-3 border-top pt-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label text-dark fw-bold mb-0">
                                    <i class="bi bi-ticket-perforated text-warning me-1"></i>Voucher ưu đãi
                                </label>
                                @if(isset($vouchers) && $vouchers->isNotEmpty())
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#voucherPickerModal">
                                        Chọn voucher
                                    </button>
                                @endif
                            </div>
                            <div class="small text-muted">
                                @if($discountVoucher)<span class="text-primary fw-semibold"><i class="bi bi-check2 me-1"></i>Mã giảm: {{ $discountVoucher['code'] }}</span><br>@endif
                                @if($shippingVoucher)<span class="text-success fw-semibold"><i class="bi bi-check2 me-1"></i>Free ship: {{ $shippingVoucher['code'] }}</span>@endif
                                @if(!$discountVoucher && !$shippingVoucher) Chưa áp dụng mã giảm giá @endif
                            </div>
                        </div>

                        <!-- TỔNG TIỀN THANH TOÁN -->
                        <div class="d-flex justify-content-between border-top pt-3 mb-4">
                            <span class="fw-bold fs-5">Thành tiền:</span>
                            <span class="fw-bold fs-4 text-danger" id="checkout-final-total">{{ number_format($finalTotal, 0, ',', '.') }} đ</span>
                        </div>

                        <!-- PHƯƠNG THỨC THANH TOÁN -->
                        <div class="mb-4">
                            <label class="form-label text-dark fw-bold small mb-2">Phương thức thanh toán:</label>
                            <div class="border rounded-3 p-3 mb-2 d-flex align-items-center gap-3 bg-light">
                                <input class="form-check-input mt-0" type="radio" name="payment_method" id="payCOD" value="COD" checked>
                                <label class="form-check-label w-100 cursor-pointer" for="payCOD">
                                    <div class="fw-bold text-dark"><i class="bi bi-cash-coin text-success me-2 fs-5"></i>Thanh toán khi nhận hàng (COD)</div>
                                    <small class="text-muted">Nhận hàng, kiểm tra rồi mới thanh toán tiền mặt</small>
                                </label>
                            </div>
                            <div class="border rounded-3 p-3 d-flex align-items-center gap-3 bg-light">
                                <input class="form-check-input mt-0" type="radio" name="payment_method" id="payOnline" value="PAYOS">
                                <label class="form-check-label w-100 cursor-pointer" for="payOnline">
                                    <div class="fw-bold text-dark"><i class="bi bi-qr-code-scan text-primary me-2 fs-5"></i>Chuyển khoản QR (PayOS)</div>
                                    <small class="text-muted">Quét mã VietQR tiện lợi qua ứng dụng ngân hàng</small>
                                </label>
                            </div>
                        </div>

                        <!-- NÚT HOÀN TẤT ĐẶT HÀNG -->
                        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold text-uppercase shadow-sm">
                            <i class="bi bi-bag-check-fill me-2"></i>XÁC NHẬN ĐẶT HÀNG
                        </button>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check text-success me-1"></i>Bảo mật thông tin & Đổi trả trong 7 ngày
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- MODAL CHỌN VOUCHER NẾU CÓ VOUCHERS -->
@if(isset($vouchers))
<div class="modal fade voucher-picker-modal" id="voucherPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="bi bi-ticket-perforated text-warning me-2"></i>Kho Voucher BeatyCare 🌸</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('cart.apply_vouchers') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2"><i class="bi bi-currency-dollar text-primary me-2"></i>Mã giảm tiền đơn hàng</h6>
                        @forelse($vouchers->where('scope', 'platform')->whereIn('type', ['fixed', 'percent']) as $voucher)
                            <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center">
                                <label class="d-flex align-items-center gap-3 w-100 mb-0 cursor-pointer">
                                    <input type="checkbox" class="form-check-input" name="voucher_codes[]" value="{{ $voucher->code }}"
                                        @checked(session('voucher_discount.code') === $voucher->code)>
                                    <div>
                                        <strong class="text-primary">{{ $voucher->code }}</strong>
                                        <div class="small text-muted">
                                            Giảm {{ $voucher->type === 'fixed' ? number_format($voucher->value, 0, ',', '.') . ' đ' : $voucher->value . '%' }} · Đơn tối thiểu {{ number_format($voucher->min_order_value, 0, ',', '.') }} đ
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @empty
                            <p class="small text-muted mb-0">Hiện chưa có mã giảm tiền toàn sàn.</p>
                        @endforelse
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2"><i class="bi bi-truck text-success me-2"></i>Mã Miễn phí vận chuyển</h6>
                        @forelse($vouchers->where('scope', 'platform')->where('type', 'free_shipping') as $voucher)
                            <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center">
                                <label class="d-flex align-items-center gap-3 w-100 mb-0 cursor-pointer">
                                    <input type="checkbox" class="form-check-input" name="voucher_codes[]" value="{{ $voucher->code }}"
                                        @checked(session('voucher_shipping.code') === $voucher->code)>
                                    <div>
                                        <strong class="text-success">{{ $voucher->code }}</strong>
                                        <div class="small text-muted">
                                            Miễn phí vận chuyển · Đơn tối thiểu {{ number_format($voucher->min_order_value, 0, ',', '.') }} đ
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @empty
                            <p class="small text-muted mb-0">Hiện chưa có mã freeship khả dụng.</p>
                        @endforelse
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                        Áp dụng voucher đã chọn
                    </button>
                </form>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. TỰ ĐỘNG ĐIỀN ĐỊA CHỈ ĐÃ LƯU
    const savedAddressSelect = document.getElementById('saved-address');
    const nameInput = document.querySelector('[name="customer_name"]');
    const phoneInput = document.querySelector('[name="customer_phone"]');
    const addressInput = document.getElementById('checkout-customer-address');

    if (savedAddressSelect) {
        const fillSavedAddress = () => {
            const selected = savedAddressSelect.options[savedAddressSelect.selectedIndex];
            if (selected && selected.value) {
                if (selected.dataset.name) nameInput.value = selected.dataset.name;
                if (selected.dataset.phone) phoneInput.value = selected.dataset.phone;
                if (selected.dataset.address) addressInput.value = selected.dataset.address;
            }
        };
        savedAddressSelect.addEventListener('change', fillSavedAddress);
    }

    // 2. TÍNH PHÍ VẬN CHUYỂN DYNAMIC
    const zoneSelect = document.getElementById('shipping-zone');
    const providerSelect = document.getElementById('shipping-provider');
    const shippingFeeDisplay = document.getElementById('checkout-shipping-fee');
    const finalTotalDisplay = document.getElementById('checkout-final-total');
    const baseSubtotal = Number({{ $total }});
    const voucherDiscount = Number({{ $discount }});
    const hasShippingVoucher = Boolean({{ $shippingVoucher ? 'true' : 'false' }});
    const formatMoney = val => new Intl.NumberFormat('vi-VN').format(Math.round(val)) + ' đ';

    function refreshShippingFee() {
        const zoneVal = zoneSelect?.value;
        const selectedZoneOpt = zoneSelect?.options[zoneSelect.selectedIndex];
        const selectedProviderOpt = providerSelect?.options[providerSelect.selectedIndex];

        // Cập nhật nhãn phí trong dropdown đơn vị vận chuyển
        if (providerSelect && zoneVal) {
            providerSelect.querySelectorAll('option[data-fees]').forEach(opt => {
                const fees = JSON.parse(opt.dataset.fees || '{}');
                opt.textContent = opt.dataset.label + (fees[zoneVal] ? ' - ' + formatMoney(fees[zoneVal]) : '');
            });
        }

        const providerFees = selectedProviderOpt?.dataset.fees ? JSON.parse(selectedProviderOpt.dataset.fees) : {};
        let fee = Number(providerFees[zoneVal] || selectedZoneOpt?.dataset.fee || 0);

        if (hasShippingVoucher) {
            fee = 0;
            if (shippingFeeDisplay) shippingFeeDisplay.textContent = 'Miễn phí (Voucher)';
        } else {
            if (shippingFeeDisplay) {
                shippingFeeDisplay.textContent = fee > 0 ? formatMoney(fee) : 'Chọn khu vực & ĐVVC';
            }
        }

        const finalTotal = Math.max(0, baseSubtotal - voucherDiscount + fee);
        if (finalTotalDisplay) {
            finalTotalDisplay.textContent = formatMoney(finalTotal);
        }
    }

    zoneSelect?.addEventListener('change', refreshShippingFee);
    providerSelect?.addEventListener('change', refreshShippingFee);
    refreshShippingFee();

    // 3. TÍCH HỢP TỈNH / THÀNH, QUẬN / HUYỆN, PHƯỜNG / XÃ & BẢN ĐỒ ĐỒNG BỘ
    const addressWrapper = document.querySelector('[data-vn-address]');
    const provinceSelect = addressWrapper?.querySelector('[data-province]');
    const districtSelect = addressWrapper?.querySelector('[data-district]');
    const wardSelect = addressWrapper?.querySelector('[data-ward]');
    const api = 'https://provinces.open-api.vn/api';

    const fillSelect = (select, items, placeholder) => {
        if (!select) return;
        select.innerHTML = `<option value="">${placeholder}</option>` + items.map(item => `<option value="${item.code}" data-name="${item.name}">${item.name}</option>`).join('');
        select.disabled = false;
    };

    // Tải danh sách tỉnh/thành ban đầu
    if (provinceSelect) {
        fetch(`${api}/p/`).then(res => res.json()).then(items => {
            window._provincesList = items;
            fillSelect(provinceSelect, items, 'Tỉnh/Thành phố');
        }).catch(err => console.error('Lỗi tải tỉnh/thành:', err));

        provinceSelect.addEventListener('change', function () {
            if (!districtSelect || !wardSelect) return;
            districtSelect.innerHTML = '<option value="">Đang tải...</option>';
            districtSelect.disabled = true;
            wardSelect.innerHTML = '<option value="">Phường/Xã</option>';
            wardSelect.disabled = true;
            if (this.value) {
                fetch(`${api}/p/${this.value}?depth=2`).then(res => res.json()).then(data => {
                    fillSelect(districtSelect, data.districts || [], 'Quận/Huyện');
                }).catch(() => {});
            }
        });

        districtSelect?.addEventListener('change', function () {
            if (!wardSelect) return;
            wardSelect.innerHTML = '<option value="">Đang tải...</option>';
            wardSelect.disabled = true;
            if (this.value) {
                fetch(`${api}/d/${this.value}?depth=2`).then(res => res.json()).then(data => {
                    fillSelect(wardSelect, data.wards || [], 'Phường/Xã');
                }).catch(() => {});
            }
        });
    }

    // Hàm chuẩn hóa chuỗi tiếng Việt để so khớp địa danh
    const norm = str => (str || '').toString().toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[đĐ]/g, 'd')
        .replace(/[^a-z0-9]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();

    const strip = str => norm(str)
        .replace(/^(tinh|thanh pho|tp|quan|huyen|thi xa|tx|phuong|xa|thi tran|tt)\s+/gi, '')
        .trim();

    // Hàm tự động chọn Tỉnh/Thành, Quận/Huyện, Phường/Xã từ thông tin định vị
    async function syncAddressFromGeocode(addr, fullAddress) {
        if (!provinceSelect || !districtSelect || !wardSelect) return;

        const fullNorm = norm(fullAddress);

        // 1. TÌM VÀ CHỌN TỈNH / THÀNH PHỐ
        if (!window._provincesList || !window._provincesList.length) {
            try {
                const res = await fetch(`${api}/p/`);
                window._provincesList = await res.json();
                fillSelect(provinceSelect, window._provincesList, 'Tỉnh/Thành phố');
            } catch (e) {
                return;
            }
        }

        const provList = window._provincesList;
        const provCandidates = [
            strip(addr.city),
            strip(addr.province),
            strip(addr.state)
        ].filter(Boolean);

        let matchedProv = null;
        for (const p of provList) {
            const pStrip = strip(p.name);
            if (provCandidates.some(c => c === pStrip || c.includes(pStrip) || pStrip.includes(c))) {
                matchedProv = p;
                break;
            }
        }
        if (!matchedProv) {
            for (const p of provList) {
                const pStrip = strip(p.name);
                if (pStrip.length > 2 && fullNorm.includes(pStrip)) {
                    matchedProv = p;
                    break;
                }
            }
        }

        if (!matchedProv) return;
        provinceSelect.value = matchedProv.code;

        // 2. TẢI VÀ CHỌN QUẬN / HUYỆN
        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        districtSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">Phường/Xã</option>';
        wardSelect.disabled = true;

        let districtList = [];
        try {
            const res = await fetch(`${api}/p/${matchedProv.code}?depth=2`);
            const data = await res.json();
            districtList = data.districts || [];
            fillSelect(districtSelect, districtList, 'Quận/Huyện');
        } catch (e) {
            return;
        }

        const distCandidates = [
            strip(addr.city_district),
            strip(addr.county),
            strip(addr.district),
            strip(addr.suburb),
            strip(addr.town)
        ].filter(Boolean);

        let matchedDist = null;
        for (const d of districtList) {
            const dStrip = strip(d.name);
            if (distCandidates.some(c => c === dStrip || c.includes(dStrip) || dStrip.includes(c))) {
                matchedDist = d;
                break;
            }
        }
        if (!matchedDist) {
            for (const d of districtList) {
                const dStrip = strip(d.name);
                if (dStrip.length > 2 && fullNorm.includes(dStrip)) {
                    matchedDist = d;
                    break;
                }
            }
        }

        // Trường hợp Nominatim trả về phường trong suburb mà không có quận (như Phú Diễn)
        const wardRaw = addr.quarter || addr.suburb || addr.ward || addr.neighbourhood;
        if (!matchedDist && wardRaw) {
            try {
                const wSearchRes = await fetch(`${api}/w/search/?q=${encodeURIComponent(wardRaw)}`);
                const foundWards = await wSearchRes.json();
                if (Array.isArray(foundWards)) {
                    const targetW = strip(wardRaw);
                    for (const fw of foundWards) {
                        if (strip(fw.name) === targetW) {
                            const foundD = districtList.find(item => item.code === fw.district_code);
                            if (foundD) {
                                matchedDist = foundD;
                                break;
                            }
                        }
                    }
                }
            } catch (e) {}
        }

        if (!matchedDist) return;
        districtSelect.value = matchedDist.code;

        // 3. TẢI VÀ CHỌN PHƯỜNG / XÃ
        wardSelect.innerHTML = '<option value="">Đang tải...</option>';
        wardSelect.disabled = true;

        let wardList = [];
        try {
            const res = await fetch(`${api}/d/${matchedDist.code}?depth=2`);
            const data = await res.json();
            wardList = data.wards || [];
            fillSelect(wardSelect, wardList, 'Phường/Xã');
        } catch (e) {
            return;
        }

        const wardCandidates = [
            strip(addr.quarter),
            strip(addr.suburb),
            strip(addr.ward),
            strip(addr.neighbourhood),
            strip(addr.village)
        ].filter(Boolean);

        let matchedWard = null;
        for (const w of wardList) {
            const wStrip = strip(w.name);
            if (wardCandidates.some(c => c === wStrip || c.includes(wStrip) || wStrip.includes(c))) {
                matchedWard = w;
                break;
            }
        }
        if (!matchedWard) {
            for (const w of wardList) {
                const wStrip = strip(w.name);
                if (wStrip.length > 2 && fullNorm.includes(wStrip)) {
                    matchedWard = w;
                    break;
                }
            }
        }

        if (matchedWard) {
            wardSelect.value = matchedWard.code;
        }

        // 4. TỰ ĐỘNG CHỌN KHU VỰC GIAO HÀNG & TÍNH PHÍ SHIP
        if (zoneSelect) {
            const isInner = fullNorm.includes('ha noi') || fullNorm.includes('hanoi') ||
                            fullNorm.includes('ho chi minh') || fullNorm.includes('sai gon');
            const targetZone = isInner ? 'inner_city' : 'other_city';
            if (zoneSelect.value !== targetZone) {
                zoneSelect.value = targetZone;
                zoneSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }

    // 4. BẢN ĐỒ VỊ TRÍ GIAO HÀNG LEAFLET & ĐỊNH VỊ
    const mapElement = document.getElementById('delivery-map');
    if (mapElement && typeof L !== 'undefined') {
        const latInput = document.getElementById('delivery-latitude');
        const lngInput = document.getElementById('delivery-longitude');
        const mapStatus = document.getElementById('map-status');
        const searchInput = document.getElementById('map-search');
        const searchBtn = document.getElementById('map-search-button');
        const geoBtn = document.getElementById('use-current-location');

        const initLat = Number(latInput?.value) || 21.0285;
        const initLng = Number(lngInput?.value) || 105.8542;

        const map = L.map('delivery-map').setView([initLat, initLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);

        // Hàm xử lý toạ độ: ghim vị trí + reverse geocode lấy địa chỉ chi tiết và tự chọn dropdowns
        async function reverseGeocodeAndSync(lat, lng) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 16);
            if (latInput) latInput.value = Number(lat).toFixed(7);
            if (lngInput) lngInput.value = Number(lng).toFixed(7);

            if (mapStatus) {
                mapStatus.textContent = 'Đang nhận diện địa chỉ từ toạ độ...';
                mapStatus.className = 'small text-primary ms-3';
            }

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&addressdetails=1`, {
                    headers: { 'Accept-Language': 'vi' }
                });
                if (!response.ok) throw new Error('Không thể kết nối dịch vụ bản đồ.');
                const data = await response.json();

                const addr = data.address || {};
                const displayName = data.display_name || '';

                // Cập nhật ô nhập địa chỉ chi tiết
                if (addressInput && displayName) {
                    addressInput.value = displayName;
                    addressInput.dispatchEvent(new Event('input', { bubbles: true }));
                }

                // Cập nhật ô tìm kiếm bản đồ
                if (searchInput && displayName) {
                    searchInput.value = displayName;
                }

                // Tự động khớp và chọn Tỉnh/Thành, Quận/Huyện, Phường/Xã
                await syncAddressFromGeocode(addr, displayName);

                if (mapStatus) {
                    mapStatus.textContent = '✓ Đã cập nhật toạ độ và địa chỉ nhận hàng!';
                    mapStatus.className = 'small text-success ms-3 fw-semibold';
                }
            } catch (err) {
                console.warn('Lỗi reverse geocode:', err);
                if (mapStatus) {
                    mapStatus.textContent = 'Đã ghim toạ độ. Vui lòng chọn Tỉnh/Huyện/Xã nếu chưa tự điền.';
                    mapStatus.className = 'small text-warning ms-3';
                }
            }
        }

        // Khi kéo thả marker trên bản đồ
        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            reverseGeocodeAndSync(pos.lat, pos.lng);
        });

        // Khi click chuột vào bất kỳ điểm nào trên bản đồ
        map.on('click', function (e) {
            reverseGeocodeAndSync(e.latlng.lat, e.latlng.lng);
        });

        // Tìm kiếm địa chỉ bằng ô tìm kiếm
        if (searchBtn && searchInput) {
            const handleSearch = function () {
                const query = searchInput.value.trim();
                if (!query) return;
                if (mapStatus) {
                    mapStatus.textContent = 'Đang tìm địa chỉ...';
                    mapStatus.className = 'small text-primary ms-3';
                }
                fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&addressdetails=1&countrycodes=vn&q=${encodeURIComponent(query)}`, {
                    headers: { 'Accept-Language': 'vi' }
                })
                .then(res => res.json())
                .then(results => {
                    if (results && results.length > 0) {
                        const first = results[0];
                        reverseGeocodeAndSync(Number(first.lat), Number(first.lon));
                    } else {
                        if (mapStatus) {
                            mapStatus.textContent = 'Không tìm thấy vị trí phù hợp.';
                            mapStatus.className = 'small text-warning ms-3';
                        }
                    }
                })
                .catch(() => {
                    if (mapStatus) {
                        mapStatus.textContent = 'Lỗi tìm kiếm bản đồ.';
                        mapStatus.className = 'small text-danger ms-3';
                    }
                });
            };

            searchBtn.addEventListener('click', handleSearch);
            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSearch();
                }
            });
        }

        // Bấm nút "Lấy vị trí hiện tại của tôi"
        if (geoBtn && navigator.geolocation) {
            geoBtn.addEventListener('click', function () {
                if (mapStatus) {
                    mapStatus.textContent = 'Đang lấy toạ độ GPS...';
                    mapStatus.className = 'small text-primary ms-3';
                }
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        reverseGeocodeAndSync(pos.coords.latitude, pos.coords.longitude);
                    },
                    err => {
                        console.error('Geolocation error:', err);
                        if (mapStatus) {
                            mapStatus.textContent = 'Không thể lấy vị trí hiện tại. Vui lòng kiểm tra quyền truy cập vị trí trên trình duyệt.';
                            mapStatus.className = 'small text-danger ms-3';
                        }
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            });
        }
    }

    // Chuẩn hóa địa chỉ khi gửi form
    const checkoutForm = document.getElementById('checkout-order-form');
    checkoutForm?.addEventListener('submit', function () {
        if (!provinceSelect || !districtSelect || !wardSelect || !addressInput) return;
        const names = [wardSelect, districtSelect, provinceSelect].map(s => s.options[s.selectedIndex]?.dataset.name).filter(Boolean);
        const street = addressInput.value.split(',').map(p => p.trim()).filter(Boolean)[0] || addressInput.value.trim();
        if (names.length === 3 && street && !addressInput.value.includes(names[0])) {
            addressInput.value = [street, ...names].join(', ');
        }
    });
});
</script>
@endpush
@endsection