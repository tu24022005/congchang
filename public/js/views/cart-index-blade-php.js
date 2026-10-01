/* cart/index.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('cart-select-all');
    const shopSelect = document.querySelector('.cart-shop-select');
    const productSelects = Array.from(document.querySelectorAll('.cart-product-select'));
    const btnProceedCheckout = document.getElementById('btn-proceed-checkout');
    const selectedCountDisplay = document.getElementById('selected-product-count');

    const money = value => new Intl.NumberFormat('vi-VN').format(Math.round(value)) + ' đ';
    const discountType = null;
    const discountValue = Number(null);

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
        form.action = null;
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

    // Xử lý nút Tiến hành thanh toán: chuyển danh sách sản phẩm được chọn
    const checkoutLink = document.getElementById('btn-proceed-checkout');
    if (checkoutLink) {
        checkoutLink.addEventListener('click', function(e) {
            const checkedInputs = Array.from(document.querySelectorAll('.cart-product-select:checked'));
            if (checkedInputs.length === 0) {
                e.preventDefault();
                window.showToast?.('Vui lòng tích chọn ít nhất 1 sản phẩm để thanh toán.', 'warning');
                return;
            }
            const totalInputs = document.querySelectorAll('.cart-product-select').length;
            if (checkedInputs.length < totalInputs) {
                const selectedKeys = checkedInputs.map(input => input.dataset.key).filter(Boolean);
                if (selectedKeys.length > 0) {
                    this.href = '0?selected_items=' + encodeURIComponent(selectedKeys.join(','));
                }
            }
        });
    }

    // Khởi tạo trạng thái ban đầu
    productSelects.forEach(input => { input.checked = true; });
    syncSelectAllState();
    refreshCartTotals();
});

