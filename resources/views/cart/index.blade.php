@extends('layouts.app')
@section('title', 'Giỏ hàng của bạn - BeatyCare 🌸')

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
        $finalTotal = max(0, $total - $discount);
    @endphp

    <!-- TIÊU ĐỀ TRANG GIỎ HÀNG -->
    <div class="cart-page-heading mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="cart-eyebrow">BEATYCARE 🌸 / GIỎ HÀNG</span>
            <h2 class="fw-bold storefront-title mb-1">
                <i class="bi bi-cart3 me-2"></i>Giỏ hàng của bạn
            </h2>
            <p class="text-muted mb-0">Quản lý và kiểm tra các sản phẩm đã chọn trước khi tiến hành thanh toán.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-light border rounded-pill px-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i>Tiếp tục mua sắm
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

    @if(session('cart') && count(session('cart')) > 0)
        <div class="row g-4">
            <!-- CỘT TRÁI: DANH SÁCH SẢN PHẨM TRONG GIỎ HÀNG -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4 storefront-panel-card cart-items-card">
                    <div class="card-body p-0">
                        <div class="cart-card-heading d-flex justify-content-between align-items-center p-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1"><i class="bi bi-bag-check text-primary me-2"></i>Sản phẩm đã chọn</h5>
                                <small class="text-muted"><span id="selected-product-count">{{ count(session('cart')) }}</span> / {{ count(session('cart')) }} sản phẩm sẵn sàng</small>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="cart-secure-badge"><i class="bi bi-shield-check me-1"></i>An toàn</span>
                                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ sản phẩm trong giỏ hàng?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Xóa toàn bộ giỏ hàng">
                                        <i class="bi bi-trash3 me-1"></i>Xoá giỏ hàng
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle text-center mb-0 cart-shopee-table">
                                <thead>
                                    <tr>
                                        <th class="ps-4 text-start">
                                            <div class="form-check mb-0">
                                                <input type="checkbox" class="form-check-input cart-checkbox-input" id="cart-select-all" aria-label="Chọn tất cả sản phẩm" checked>
                                                <label class="form-check-label fw-bold cursor-pointer" for="cart-select-all">Sản phẩm</label>
                                            </div>
                                        </th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Số tiền</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="cart-shop-row">
                                        <td colspan="5" class="text-start ps-4 py-2 bg-light-subtle">
                                            <input type="checkbox" class="form-check-input cart-checkbox-input cart-shop-select me-2" checked>
                                            <strong class="text-dark">BeatyCare 🌸</strong>
                                            <span class="badge bg-danger-subtle text-danger ms-2">Yêu thích</span>
                                        </td>
                                    </tr>

                                    @php $cartSubtotal = 0; @endphp
                                    @foreach(session('cart') as $id => $details)
                                        @php $cartSubtotal += $details['price'] * $details['quantity']; @endphp
                                        <tr class="cart-item-row" data-unit-price="{{ $details['price'] }}">
                                            <td class="text-start ps-4">
                                                <div class="d-flex align-items-center cart-product-cell">
                                                    <input type="checkbox" class="form-check-input cart-checkbox-input cart-product-select me-3" aria-label="Chọn {{ $details['name'] }}" checked>
                                                    @if(isset($details['image']) && $details['image'])
                                                        <img src="{{ asset('storage/' . $details['image']) }}" width="68" height="68" class="img-thumbnail rounded-3 shadow-sm me-3 cart-product-image object-fit-cover" alt="{{ $details['name'] }}">
                                                    @else
                                                        <div class="bg-light rounded-3 border me-3 storefront-thumb-placeholder cart-product-image d-flex align-items-center justify-content-center" style="width: 68px; height: 68px;">
                                                            <i class="bi bi-flower1 text-muted fs-3"></i>
                                                        </div>
                                                    @endif
                                                    <div class="cart-product-info">
                                                        <div class="fw-bold cart-product-name text-dark mb-1">{{ $details['name'] }}</div>
                                                        @if(isset($details['variation']))
                                                            <small class="text-muted">Phân loại: <span class="badge bg-info-subtle text-info-emphasis border">{{ $details['variation'] }}</span></small>
                                                        @else
                                                            <small class="text-muted">Phân loại: <span class="badge bg-secondary-subtle text-secondary-emphasis">Mặc định</span></small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(!empty($details['promotion_label']))
                                                    <span class="badge bg-danger d-block mb-1">{{ $details['promotion_label'] }}</span>
                                                    <span class="text-muted text-decoration-line-through small">{{ number_format($details['original_price'], 0, ',', '.') }} đ</span><br>
                                                @endif
                                                <span class="cart-unit-price fw-semibold">{{ number_format($details['price'], 0, ',', '.') }} đ</span>
                                            </td>
                                            <td>
                                                <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex justify-content-center align-items-center quantity-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="quantity-control d-inline-flex border rounded-3 overflow-hidden bg-white shadow-sm">
                                                        <button type="button" class="btn btn-sm btn-light quantity-step px-2 border-0" data-step="-1">−</button>
                                                        <input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control form-control-sm text-center quantity-input border-0 shadow-none" style="width: 48px;" min="1">
                                                        <button type="button" class="btn btn-sm btn-light quantity-step px-2 border-0" data-step="1">+</button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-danger fw-bold line-total flash-highlight fs-6">
                                                {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }} đ
                                            </td>
                                            <td>
                                                <form action="{{ route('cart.destroy', $id) }}" method="POST" class="cart-remove-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Xoá sản phẩm">
                                                        <i class="bi bi-trash3 me-1"></i>Xoá
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i>Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            <!-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG & TIẾN HÀNH THANH TOÁN -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 storefront-panel-card position-sticky" style="top: 90px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-bottom pb-3 text-center">
                            <i class="bi bi-receipt text-primary me-2"></i>TỔNG ĐƠN HÀNG
                        </h5>
                        
                        <!-- TẠM TÍNH -->
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-bold fs-6" id="cart-subtotal">{{ number_format($total, 0, ',', '.') }} đ</span>
                        </div>

                        <!-- VOUCHER ĐÃ ÁP DỤNG -->
                        @if($discountVoucher || $shippingVoucher)
                            <div class="d-flex justify-content-between mb-3 text-success p-2 bg-success-subtle rounded-3">
                                <span class="small">
                                    <i class="bi bi-tag-fill me-1"></i>Voucher giảm:
                                </span>
                                <span class="fw-bold small text-end">
                                    @if($discountVoucher) -<span id="cart-discount">{{ number_format($discount, 0, ',', '.') }}</span> đ ({{ $discountVoucher['code'] }})<br>@endif
                                    @if($shippingVoucher) Free ship ({{ $shippingVoucher['code'] }}) @endif
                                </span>
                            </div>
                        @endif

                        <!-- KHU VỰC VOUCHER -->
                        <div class="mb-3 border-top pt-3 voucher-box">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label text-dark fw-bold mb-0">
                                    <i class="bi bi-ticket-perforated text-warning me-1"></i>Voucher của bạn
                                </label>
                                @if(isset($vouchers) && $vouchers->isNotEmpty())
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#voucherPickerModal">
                                        Chọn hoặc nhập mã
                                    </button>
                                @endif
                            </div>
                            <div class="small text-muted">
                                @if($discountVoucher)<span class="text-primary fw-semibold"><i class="bi bi-check2 me-1"></i>Mã giảm: {{ $discountVoucher['code'] }}</span><br>@endif
                                @if($shippingVoucher)<span class="text-success fw-semibold"><i class="bi bi-check2 me-1"></i>Free ship: {{ $shippingVoucher['code'] }}</span>@endif
                                @if(!$discountVoucher && !$shippingVoucher) Chưa chọn voucher @endif
                            </div>
                        </div>

                        <!-- THÀNH TIỀN TẠM TÍNH -->
                        <div class="d-flex justify-content-between border-top pt-3 mb-2">
                            <span class="fw-bold fs-5">Tạm tính tổng:</span>
                            <span class="fw-bold fs-4 text-danger" id="cart-final-total">{{ number_format($finalTotal, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="small text-muted mb-4">
                            <i class="bi bi-info-circle me-1"></i>Phí vận chuyển sẽ được tính chi tiết theo địa chỉ ở bước thanh toán.
                        </div>

                        <!-- NÚT TIẾN HÀNH THANH TOÁN (CHUYỂN SANG TRANG /checkout) -->
                        <a href="{{ route('checkout') }}" id="btn-proceed-checkout" class="btn btn-success w-100 py-3 rounded-pill fw-bold text-uppercase shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-credit-card fs-5"></i>
                            <span>TIẾN HÀNH THANH TOÁN</span>
                        </a>

                        <!-- CAM KẾT DỊCH VỤ -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                                <i class="bi bi-shield-check text-success fs-5"></i>
                                <span>Bảo mật thông tin thanh toán tuyệt đối</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                                <i class="bi bi-arrow-counterclockwise text-primary fs-5"></i>
                                <span>Đổi trả sản phẩm dễ dàng trong 7 ngày</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-truck text-warning fs-5"></i>
                                <span>Giao hàng toàn quốc - Nhận hàng kiểm tra</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- GIỎ HÀNG TRỐNG -->
        <div class="text-center py-5 bg-light rounded-4 shadow-sm mt-4 border">
            <i class="bi bi-cart-x text-muted empty-cart-icon" style="font-size: 4rem;"></i>
            <h4 class="mt-3 text-muted fw-bold">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted">Hãy chọn thêm những sản phẩm yêu thích từ BeatyCare 🌸 nhé!</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-4 mt-2 shadow-sm">
                <i class="bi bi-bag-plus me-1"></i>Khám phá sản phẩm ngay
            </a>
        </div>
    @endif
</div>

<!-- MODAL CHỌN VOUCHER -->
@if(isset($vouchers))
<div class="modal fade voucher-picker-modal" id="voucherPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="bi bi-ticket-perforated text-warning me-2"></i>Chọn voucher ưu đãi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body p-4">
                <div class="input-group mb-4">
                    <input type="text" id="voucher-code-modal" class="form-control text-uppercase" placeholder="Nhập mã voucher giảm giá...">
                    <button type="button" class="btn btn-dark px-4" id="apply-voucher-modal">Áp dụng mã</button>
                </div>
                <p class="small text-muted mb-3"><i class="bi bi-info-circle me-1"></i>Bạn có thể áp dụng cùng lúc 1 mã giảm tiền và 1 mã miễn phí vận chuyển.</p>
                <form action="{{ route('cart.apply_vouchers') }}" method="POST" id="selected-vouchers-form">
                    @csrf
                    <div class="mt-3">
                        <h6 class="fw-bold border-bottom pb-2"><i class="bi bi-currency-dollar text-primary me-2"></i>Mã giảm tiền toàn sàn</h6>
                        @forelse($vouchers->where('scope', 'platform')->whereIn('type', ['fixed', 'percent']) as $voucher)
                            <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center">
                                <label class="d-flex align-items-center gap-3 w-100 mb-0 cursor-pointer">
                                    <input type="checkbox" class="form-check-input voucher-choice" data-voucher-type="discount" name="voucher_codes[]" value="{{ $voucher->code }}"
                                        @checked(session('voucher_discount.code') === $voucher->code)>
                                    <div>
                                        <strong class="text-primary">{{ $voucher->code }}</strong>
                                        <span class="d-block small text-muted">Giảm {{ $voucher->type === 'fixed' ? number_format($voucher->value, 0, ',', '.') . ' đ' : $voucher->value . '%' }} · Đơn tối thiểu {{ number_format($voucher->min_order_value, 0, ',', '.') }} đ</span>
                                    </div>
                                </label>
                            </div>
                        @empty
                            <p class="small text-muted">Hiện chưa có mã giảm tiền toàn sàn.</p>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        <h6 class="fw-bold border-bottom pb-2"><i class="bi bi-truck text-info me-2"></i>Miễn phí vận chuyển</h6>
                        @forelse($vouchers->where('scope', 'platform')->where('type', 'free_shipping') as $voucher)
                            <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center">
                                <label class="d-flex align-items-center gap-3 w-100 mb-0 cursor-pointer">
                                    <input type="checkbox" class="form-check-input voucher-choice" data-voucher-type="shipping" name="voucher_codes[]" value="{{ $voucher->code }}"
                                        @checked(session('voucher_shipping.code') === $voucher->code)>
                                    <div>
                                        <strong class="text-info">{{ $voucher->code }}</strong>
                                        <span class="d-block small text-muted">Miễn phí vận chuyển · Đơn tối thiểu {{ number_format($voucher->min_order_value, 0, ',', '.') }} đ</span>
                                    </div>
                                </label>
                            </div>
                        @empty
                            <p class="small text-muted">Hiện chưa có mã free ship toàn sàn.</p>
                        @endforelse
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill mt-3 py-2 fw-semibold">
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
<style>
    #voucherPickerModal { z-index: 2000 !important; }
    #voucherPickerModal .modal-dialog,
    #voucherPickerModal .modal-content { position: relative; z-index: 2001; }
    .modal-backdrop.show { z-index: 1990 !important; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('cart-select-all');
    const shopSelect = document.querySelector('.cart-shop-select');
    const productSelects = Array.from(document.querySelectorAll('.cart-product-select'));
    const btnProceedCheckout = document.getElementById('btn-proceed-checkout');
    const selectedCountDisplay = document.getElementById('selected-product-count');

    const money = value => new Intl.NumberFormat('vi-VN').format(Math.round(value)) + ' đ';
    const discountType = @json($discountVoucher['type'] ?? null);
    const discountValue = Number(@json($discountVoucher['value'] ?? 0));

    function syncSelectAllState() {
        if (!selectAll || !productSelects.length) return;
        const selectedCount = productSelects.filter(input => input.checked).length;
        selectAll.checked = selectedCount === productSelects.length;
        selectAll.indeterminate = selectedCount > 0 && selectedCount < productSelects.length;
        if (shopSelect) {
            shopSelect.checked = selectAll.checked;
            shopSelect.indeterminate = selectAll.indeterminate;
        }
        if (selectedCountDisplay) {
            selectedCountDisplay.textContent = selectedCount;
        }
        if (btnProceedCheckout) {
            if (selectedCount === 0) {
                btnProceedCheckout.classList.add('disabled', 'opacity-50');
            } else {
                btnProceedCheckout.classList.remove('disabled', 'opacity-50');
            }
        }
    }

    function setProductsSelected(checked) {
        productSelects.forEach(input => { input.checked = checked; });
        syncSelectAllState();
        refreshCartTotals();
    }

    selectAll?.addEventListener('change', function () {
        setProductsSelected(this.checked);
    });
    shopSelect?.addEventListener('change', function () {
        setProductsSelected(this.checked);
    });
    productSelects.forEach(input => input.addEventListener('change', function () {
        syncSelectAllState();
        refreshCartTotals();
    }));

    // Cập nhật số lượng qua nút bấm + và -
    document.querySelectorAll('.quantity-step').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = this.closest('.quantity-control').querySelector('input');
            const nextValue = Math.max(1, Number(input.value || 1) + Number(this.dataset.step));
            input.value = nextValue;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });

    function refreshCartTotals() {
        let subtotal = 0;
        document.querySelectorAll('.cart-item-row').forEach(function (row) {
            const input = row.querySelector('.quantity-input');
            const quantity = Math.max(1, Number(input.value || 1));
            input.value = quantity;
            const lineTotal = Number(row.dataset.unitPrice) * quantity;
            const productSelect = row.querySelector('.cart-product-select');
            if (productSelect?.checked) subtotal += lineTotal;
            row.querySelector('.line-total').textContent = money(lineTotal);
        });

        let discount = 0;
        if (discountType === 'fixed') discount = discountValue;
        if (discountType === 'percent') discount = subtotal * discountValue / 100;
        discount = Math.min(discount, subtotal);
        const total = Math.max(0, subtotal - discount);

        document.getElementById('cart-subtotal').textContent = money(subtotal);
        const discountElem = document.getElementById('cart-discount');
        if (discountElem) discountElem.textContent = new Intl.NumberFormat('vi-VN').format(Math.round(discount));
        document.getElementById('cart-final-total').textContent = money(total);
    }
    window.refreshCartTotals = refreshCartTotals;

    // Tự động submit khi đổi số lượng (có debounce)
    document.querySelectorAll('.quantity-input').forEach(function (input) {
        input.addEventListener('input', function () {
            refreshCartTotals();
            clearTimeout(input.form.dataset.updateTimer);
            input.form.dataset.updateTimer = setTimeout(() => input.form.submit(), 600);
        });
    });

    // Xử lý modal chọn voucher
    const voucherModal = document.getElementById('voucherPickerModal');
    if (voucherModal && voucherModal.parentElement !== document.body) {
        document.body.appendChild(voucherModal);
    }

    document.getElementById('apply-voucher-modal')?.addEventListener('click', function () {
        const code = document.getElementById('voucher-code-modal').value.trim();
        if (!code) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = @json(route('cart.apply_voucher'));
        form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}"><input type="hidden" name="voucher_code" value="${code}">`;
        document.body.appendChild(form);
        form.submit();
    });

    document.querySelectorAll('.voucher-choice').forEach(function (choice) {
        choice.addEventListener('change', function () {
            if (!this.checked) return;
            document.querySelectorAll('.voucher-choice[data-voucher-type="' + this.dataset.voucherType + '"]').forEach(function (other) {
                if (other !== choice) other.checked = false;
            });
        });
    });

    // Hiệu ứng xóa sản phẩm
    document.querySelectorAll('.cart-remove-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const tr = this.closest('tr');
            if (tr) {
                tr.classList.add('slide-out-right');
                setTimeout(() => {
                    HTMLFormElement.prototype.submit.call(this);
                }, 350);
            } else {
                HTMLFormElement.prototype.submit.call(this);
            }
        });
    });

    // Khởi tạo trạng thái ban đầu
    productSelects.forEach(input => { input.checked = true; });
    syncSelectAllState();
    refreshCartTotals();
});
</script>
@endpush
@endsection