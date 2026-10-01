@extends('layouts.app')
@section('title', 'Thanh toán đơn hàng - BeatyCare 🌸')
@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="{{ asset_v('css/views/cart-checkout-blade-php.css') }}">
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
        <input type="hidden" name="buy_now" value="{{ !empty($isBuyNow) ? 1 : 0 }}">
        <div class="row g-4">
            <!-- CỘT TRÁI: SẢN PHẨM & ĐỊA CHỈ GIAO HÀNG (COL-LG-8) -->
            <div class="col-lg-8">
                
                <!-- 1. DANH SÁCH SẢN PHẨM ĐẶT MUA -->
                <div class="card checkout-panel-card mb-4">
                    <div class="card-header bg-transparent border-bottom p-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="checkout-step-badge">1</span>
                            <h5 class="fw-bold mb-0">Sản phẩm đặt mua</h5>
                            @if(!empty($isBuyNow))
                                <span class="badge bg-danger rounded-pill"><i class="bi bi-lightning-charge-fill me-1"></i>Mua ngay</span>
                            @endif
                        </div>
                        <span class="badge bg-light text-muted border">{{ count($cart) }} sản phẩm</span>
                    </div>
                    @if(!empty($isBuyNow))
                        <div class="alert alert-danger-subtle border-0 rounded-0 m-0 py-2 px-3 small text-danger-emphasis d-flex align-items-center gap-2">
                            <i class="bi bi-lightning-charge-fill text-danger fs-6"></i>
                            <span>Đơn hàng <strong>Mua ngay</strong> riêng biệt cho sản phẩm này. Các sản phẩm khác trong giỏ hàng vẫn được lưu giữ an toàn.</span>
                        </div>
                    @endif
                    <div class="card-body p-0">
                        @foreach($cart as $id => $details)
                            <div class="d-flex align-items-center justify-content-between p-3 border-bottom checkout-product-row">
                                <div class="d-flex align-items-center gap-3">
                                    @if(isset($details['image']) && $details['image'])
                                        <img src="{{ asset('storage/' . $details['image']) }}" alt="{{ $details['name'] }}" width="64" height="64" class="rounded-3 border object-fit-cover shadow-sm" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                                    @else
                                        <div class="bg-light rounded-3 border d-flex align-items-center justify-content-center view-inline-1">
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
<script src="{{ asset_v('js/views/cart-checkout-blade-php.js') }}" defer></script>
<script src="{{ asset_v('js/views/cart-checkout-blade-php.js') }}" defer></script>
@endpush
@endsection