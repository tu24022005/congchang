@extends('layouts.app')
@section('title', 'Thông tin giao hàng')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4 text-center">Thông tin đặt hàng</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Cột trái: Form điền thông tin -->
            <div class="col-md-7 mb-4">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Thông tin người nhận</h5>
                        <div class="alert alert-info border-0 small py-2">Thông tin người nhận và số điện thoại giao hàng là thông tin riêng, không phụ thuộc số điện thoại cá nhân trong tài khoản.</div>
                        @if ($addresses->isNotEmpty())
                            <div class="mb-3">
                                <label for="saved-address" class="form-label fw-bold">Chọn địa chỉ đã lưu</label>
                                <select id="saved-address" name="address_id" class="form-select">
                                    <option value="">Nhập địa chỉ mới</option>
                                    @foreach ($addresses as $address)
                                        <option value="{{ $address->id }}" data-name="{{ $address->recipient_name }}" data-phone="{{ $address->phone }}" data-address="{{ $address->address }}" @selected(old('address_id', $address->is_default ? $address->id : '') == $address->id)>{{ $address->label }} - {{ $address->recipient_name }} - {{ $address->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        
                        <!-- Lấy sẵn tên và email của user đang đăng nhập -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và tên người nhận</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', Auth::user()->name) }}" minlength="2" maxlength="120" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Số điện thoại người nhận hàng</label>
                            <input type="tel" name="customer_phone" class="form-control" value="{{ old('customer_phone') }}" placeholder="Ví dụ: 0987654321" pattern="(0|\+84)(3|5|7|8|9)[0-9]{8}" maxlength="12" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa chỉ giao hàng chi tiết</label>
                            <textarea name="customer_address" id="checkout-customer-address" class="form-control" rows="3" minlength="10" maxlength="500" placeholder="Số nhà, tên đường..." required>{{ old('customer_address') }}</textarea>
                            <div class="row g-2 mt-2" data-vn-address>
                                <div class="col-md-4"><select class="form-select" data-province><option value="">Tỉnh/Thành phố</option></select></div>
                                <div class="col-md-4"><select class="form-select" data-district disabled><option value="">Quận/Huyện</option></select></div>
                                <div class="col-md-4"><select class="form-select" data-ward disabled><option value="">Phường/Xã</option></select></div>
                            </div>
                            <small class="text-muted">Chọn tỉnh, huyện và xã để tự điền địa chỉ giao hàng.</small>
                        </div>
                        <div class="mb-3">
                            <label for="shipping-zone" class="form-label fw-bold">Khu vực giao hàng</label>
                            <select name="shipping_zone" id="shipping-zone" class="form-select" required>
                                <option value="">-- Chọn khu vực --</option>
                                @foreach(config('shop.shipping_zones', []) as $key => $zone)
                                    <option value="{{ $key }}" data-fee="{{ $zone['fee'] }}" @selected(old('shipping_zone') === $key)>
                                        {{ $zone['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Phí được tính theo khu vực, voucher miễn phí vận chuyển sẽ được áp dụng nếu đủ điều kiện.</small>
                        </div>
                        <div class="mb-3">
                            <label for="shipping-provider" class="form-label fw-bold">Đơn vị vận chuyển</label>
                            <select name="shipping_provider" id="shipping-provider" class="form-select" required>
                                <option value="">-- Chọn đơn vị vận chuyển --</option>
                                @foreach(config('shop.shipping_providers', []) as $key => $label)
                                    <option value="{{ $key }}" data-label="{{ $label }}" data-fees="{{ json_encode(collect(config('shop.shipping_provider_fees', []))->mapWithKeys(fn ($fees, $zone) => [$zone => $fees[$key] ?? null])) }}" @selected(old('shipping_provider') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Tóm tắt đơn hàng & Chọn thanh toán -->
            <div class="col-md-5">
                <div class="card shadow-sm border-primary border-2 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Tóm tắt đơn hàng</h5>
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($cart as $id => $details)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="my-0">{{ $details['name'] }}</h6>
                                        <small class="text-muted">SL: {{ $details['quantity'] }}</small>
                                    </div>
                                    <span class="text-muted">{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }} đ</span>
                                </li>
                            @endforeach
                        </ul>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng tiền:</span>
                            <span class="fw-bold">{{ number_format($total, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                            <span>Phí vận chuyển:</span>
                            <span id="shipping-fee" class="text-danger">Chọn khu vực</span>
                        </div>

                        <h5 class="fw-bold mb-3">Phương thức thanh toán</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payCOD" value="COD" checked>
                            <label class="form-check-label" for="payCOD">
                                Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="payment_method" id="payOnline" value="PAYOS">
                            <label class="form-check-label" for="payOnline">
                                Chuyển khoản ngân hàng (Quét mã QR)
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold">HOÀN TẤT ĐẶT HÀNG</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selector = document.getElementById('saved-address');
        if (selector) {
            const name = document.querySelector('[name="customer_name"]');
            const phone = document.querySelector('[name="customer_phone"]');
            const address = document.querySelector('[name="customer_address"]');
            const fill = () => {
                const option = selector.options[selector.selectedIndex];
                name.value = option.dataset.name || '';
                phone.value = option.dataset.phone || '';
                address.value = option.dataset.address || '';
            };
            selector.addEventListener('change', fill);
            if (selector.value) fill();
        }

        const zone = document.getElementById('shipping-zone');
        const provider = document.getElementById('shipping-provider');
        const fee = document.getElementById('shipping-fee');
        const formatMoney = value => new Intl.NumberFormat('vi-VN').format(value) + ' đ';
        const refreshProviderLabels = () => {
            document.querySelectorAll('#shipping-provider option[data-fees]').forEach(option => {
                const fees = JSON.parse(option.dataset.fees || '{}');
                option.textContent = option.dataset.label + (fees[zone.value] ? ' - ' + formatMoney(fees[zone.value]) : '');
            });
        };
        const refreshShippingFee = () => {
            const zoneOption = zone.options[zone.selectedIndex];
            const providerOption = provider.options[provider.selectedIndex];
            const providerFees = providerOption?.dataset.fees ? JSON.parse(providerOption.dataset.fees) : {};
            const amount = providerFees[zoneOption?.value] || zoneOption?.dataset.fee;
            fee.textContent = amount ? formatMoney(Number(amount)) : 'Chọn khu vực và đơn vị';
            refreshProviderLabels();
        };
        zone.addEventListener('change', refreshShippingFee);
        provider.addEventListener('change', refreshShippingFee);
        refreshShippingFee();

        const form = document.querySelector('form[action="{{ route('orders.store') }}"]');
        const wrapper = document.querySelector('[data-vn-address]');
        if (!form || !wrapper) return;
        const province = wrapper.querySelector('[data-province]');
        const district = wrapper.querySelector('[data-district]');
        const ward = wrapper.querySelector('[data-ward]');
        const detail = document.getElementById('checkout-customer-address');
        const api = 'https://provinces.open-api.vn/api';
        const fill = (select, items, placeholder) => {
            select.innerHTML = `<option value="">${placeholder}</option>` + items.map(item => `<option value="${item.code}" data-name="${item.name}">${item.name}</option>`).join('');
            select.disabled = false;
        };
        fetch(`${api}/p/`).then(response => response.json()).then(items => fill(province, items, 'Tỉnh/Thành phố')).catch(() => {});
        province.addEventListener('change', function () {
            district.innerHTML = '<option value="">Đang tải quận/huyện...</option>'; district.disabled = true;
            ward.innerHTML = '<option value="">Phường/Xã</option>'; ward.disabled = true;
            if (this.value) fetch(`${api}/p/${this.value}?depth=2`).then(response => response.json()).then(data => fill(district, data.districts, 'Quận/Huyện')).catch(() => {});
        });
        district.addEventListener('change', function () {
            ward.innerHTML = '<option value="">Đang tải phường/xã...</option>'; ward.disabled = true;
            if (this.value) fetch(`${api}/d/${this.value}?depth=2`).then(response => response.json()).then(data => fill(ward, data.wards, 'Phường/Xã')).catch(() => {});
        });
        form.addEventListener('submit', function () {
            const names = [ward, district, province].map(select => select.options[select.selectedIndex]?.dataset.name).filter(Boolean);
            const street = detail.value.split(',').map(part => part.trim()).filter(Boolean)[0] || detail.value.trim();
            if (names.length === 3 && street) detail.value = [street, ...names].join(', ');
        });
    });
</script>
@endpush
@endsection