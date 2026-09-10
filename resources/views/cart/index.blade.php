@extends('layouts.app')
@section('title', 'Giỏ hàng của bạn')
@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
<div class="container py-4">
    <div class="cart-page-heading mb-4"><div><span class="cart-eyebrow">ALOHA BEAUTY / GIỎ HÀNG</span><h2 class="fw-bold storefront-title mb-1"><i class="bi bi-cart3 me-2"></i>Giỏ hàng của bạn</h2><p class="text-muted mb-0">Kiểm tra sản phẩm và hoàn tất địa chỉ nhận hàng.</p></div><a href="{{ route('products.index') }}" class="btn btn-light border rounded-pill"><i class="bi bi-plus-lg me-1"></i>Thêm sản phẩm</a></div>

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
                            <table class="table table-hover align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 ps-4 text-start">Sản phẩm & Phân loại</th>
                                        <th class="py-3">Giá</th>
                                        <th class="py-3">Số lượng</th>
                                        <th class="py-3">Thành tiền</th>
                                        <th class="py-3">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach(session('cart') as $id => $details)
                                        @php $total += $details['price'] * $details['quantity']; @endphp
                                        <tr>
                                            <td class="text-start ps-4">
                                                <div class="d-flex align-items-center">
                                                    @if(isset($details['image']) && $details['image'])
                                                        <img src="{{ asset('storage/' . $details['image']) }}" width="60" class="img-thumbnail rounded-3 shadow-sm me-3">
                                                    @else
                                                        <div class="bg-light rounded-3 border me-3 storefront-thumb-placeholder"></div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold">{{ $details['name'] }}</div>
                                                        @if(isset($details['variation']))
                                                            <small class="text-muted">Phân loại: <span class="badge bg-info text-dark">{{ $details['variation'] }}</span></small>
                                                        @else
                                                            <small class="text-muted">Phân loại: <span class="badge bg-secondary">Mặc định</span></small>
                                                        @endif
                                                    </div>
                                                </div>
                                            <td>{{ number_format($details['price'], 0, ',', '.') }} đ</td>
                                            <td>
                                                <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex justify-content-center">
                                                    @csrf
                                                    <!-- ĐÃ SỬA TỪ PUT THÀNH PATCH Ở ĐÂY -->
                                                    @method('PATCH')
                                                    <div class="quantity-control"><button type="button" class="quantity-step" data-step="-1">-</button><input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control form-control-sm text-center quantity-input" min="1"><button type="button" class="quantity-step" data-step="1">+</button></div><button type="submit" class="btn btn-sm btn-outline-primary ms-2" title="Cập nhật số lượng"><i class="bi bi-check2"></i></button>
                                                </form>
                                            </td>
                                            <td class="text-danger fw-bold">{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }} đ</td>
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
                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <div class="card border-0 shadow-sm rounded-4 storefront-panel-card">
                        <div class="card-body p-4">
                            <div class="cart-card-heading border-bottom pb-3 mb-3"><div><h5 class="fw-bold mb-1"><i class="bi bi-geo-alt text-primary me-2"></i>Thông tin giao hàng</h5><small class="text-muted">Chọn vị trí trên bản đồ để địa chỉ chính xác hơn.</small></div><span class="step-badge">02</span></div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Họ và tên người nhận:</label>
                                <input type="text" name="customer_name" class="form-control rounded-3" value="{{ old('customer_name', Auth::user()->name ?? '') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Số điện thoại liên hệ:</label>
                                <input type="text" name="customer_phone" class="form-control rounded-3" placeholder="Ví dụ: 0987654321" value="{{ old('customer_phone') }}" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label text-muted small">Địa chỉ nhận hàng chi tiết:</label>
                                <textarea name="customer_address" id="customer-address" class="form-control rounded-3" rows="2" placeholder="Số nhà, Tên đường, Phường/Xã..." required>{{ old('customer_address') }}</textarea>
                                <input type="hidden" name="latitude" id="delivery-latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="delivery-longitude" value="{{ old('longitude') }}">
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
                        @php
                            $discount = 0;
                            if(session()->has('voucher')) {
                                if(session('voucher')['type'] == 'fixed') {
                                    $discount = session('voucher')['value'];
                                } else {
                                    $discount = $total * (session('voucher')['value'] / 100);
                                }
                                $discount = min($discount, $total);
                            }
                            $finalTotal = $total - $discount;
                            $serviceFee = config('shop.service_fee', 3000);
                            $finalTotal += $serviceFee;
                        @endphp

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-bold">{{ number_format($total, 0, ',', '.') }} đ</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Phí giao hàng:</span>
                            <span class="text-success fw-bold">Miễn phí</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Phí dịch vụ:</span>
                            <span class="fw-bold">{{ number_format($serviceFee, 0, ',', '.') }} đ</span>
                        </div>

                        <!-- HIỂN THỊ DÒNG TIỀN ĐƯỢC GIẢM -->
                        @if(session()->has('voucher'))
                            <div class="d-flex justify-content-between mb-3 text-success">
                                <span>
                                    <i class="bi bi-tag-fill me-1"></i> Voucher ({{ session('voucher')['code'] }}):
                                </span>
                                <span class="fw-bold">- {{ number_format($discount, 0, ',', '.') }} đ</span>
                            </div>
                        @endif

                        <!-- KHU VỰC NHẬP VOUCHER -->
                        <div class="mb-3 border-top pt-3">
                            <label class="form-label text-muted small"><i class="bi bi-ticket-perforated text-warning me-1"></i> Mã giảm giá Miu Voucher</label>
                            <div class="d-flex gap-2">
                                <input type="text" name="voucher_code" class="form-control rounded-3 border-secondary voucher-input" placeholder="Nhập mã..." value="{{ old('voucher_code', session('voucher')['code'] ?? '') }}">
                                <button type="submit" formaction="{{ route('cart.apply_voucher') }}" formmethod="POST" class="btn btn-dark rounded-3 px-3 text-nowrap">Áp dụng</button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-3 mb-4">
                            <span class="fw-bold fs-5">Thành tiền:</span>
                            <span class="fw-bold fs-4 text-danger">{{ number_format($finalTotal, 0, ',', '.') }} đ</span>
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
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.quantity-step').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = this.closest('.quantity-control').querySelector('input');
                const nextValue = Math.max(1, Number(input.value || 1) + Number(this.dataset.step));
                input.value = nextValue;
            });
        });
        const mapElement = document.getElementById('delivery-map');
        if (!mapElement) return;
        const addressInput = document.getElementById('customer-address');
        const latitudeInput = document.getElementById('delivery-latitude');
        const longitudeInput = document.getElementById('delivery-longitude');
        const status = document.getElementById('map-status');
        const defaultPosition = [21.0285, 105.8542];
        const map = L.map(mapElement).setView(defaultPosition, 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
        let marker;

        function setLocation(latitude, longitude, label) {
            latitudeInput.value = latitude.toFixed(7);
            longitudeInput.value = longitude.toFixed(7);
            if (!marker) marker = L.marker([latitude, longitude], { draggable: true }).addTo(map);
            marker.setLatLng([latitude, longitude]);
            marker.off('dragend').on('dragend', event => { const position = event.target.getLatLng(); reverseGeocode(position.lat, position.lng).catch(() => {}); });
            marker.bindPopup('Vị trí giao hàng').openPopup();
            map.setView([latitude, longitude], 16);
            if (label) addressInput.value = label;
            status.textContent = 'Đã chọn vị trí';
            status.className = 'small text-success ms-2';
        }

        async function reverseGeocode(latitude, longitude) {
            status.textContent = 'Đang lấy địa chỉ...';
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`, { headers: { 'Accept-Language': 'vi' } });
            const data = await response.json();
            setLocation(latitude, longitude, data.display_name || 'Vị trí đã chọn');
        }

        map.on('click', event => reverseGeocode(event.latlng.lat, event.latlng.lng).catch(() => status.textContent = 'Không lấy được địa chỉ, bạn có thể nhập tay.'));
        document.getElementById('map-search-button').addEventListener('click', async function () {
            const query = document.getElementById('map-search').value.trim();
            if (!query) return;
            status.textContent = 'Đang tìm...';
            try { const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=vn&q=${encodeURIComponent(query)}`, { headers: { 'Accept-Language': 'vi' } }); const results = await response.json(); if (!results.length) throw new Error(); setLocation(Number(results[0].lat), Number(results[0].lon), results[0].display_name); } catch (error) { status.textContent = 'Không tìm thấy địa chỉ'; }
        });
        document.getElementById('use-current-location').addEventListener('click', function () { if (!navigator.geolocation) return; status.textContent = 'Đang lấy vị trí...'; navigator.geolocation.getCurrentPosition(position => reverseGeocode(position.coords.latitude, position.coords.longitude), () => status.textContent = 'Trình duyệt chưa cho phép định vị.'); });
        if (addressInput.value.trim()) document.getElementById('map-search').value = addressInput.value;
    });
    </script>
@endpush
@endsection