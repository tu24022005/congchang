/**
 * Aloha Beauty - Cart Drawer & Quick View / Quick Add Controller
 * Vanilla JS, no external framework dependencies
 */
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const drawerElement = document.getElementById('cartOffcanvasDrawer');
    const drawerInstance = drawerElement && window.bootstrap?.Offcanvas ? bootstrap.Offcanvas.getOrCreateInstance(drawerElement) : null;
    const freeshipThreshold = Number(drawerElement?.dataset.freeshipThreshold || 500000);

    // Format currency VND
    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' ₫';
    }

    // Helper: update all cart badges on page
    window.updateGlobalCartBadges = function (count) {
        const num = Number(count) || 0;
        document.querySelectorAll('.cart-count-badge, #drawer-header-count').forEach(badge => {
            badge.textContent = num;
            if (num > 0) {
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        });
    };

    // Helper: open drawer
    window.openCartDrawer = function () {
        if (drawerInstance) {
            drawerInstance.show();
        }
    };

    // Update freeship UI
    function updateFreeshipUI(subtotal) {
        const wrap = document.getElementById('drawer-freeship-wrap');
        const text = document.getElementById('drawer-freeship-text');
        const bar = document.getElementById('drawer-freeship-bar');
        if (!wrap || !bar) return;

        const needed = Math.max(0, freeshipThreshold - subtotal);
        const percent = freeshipThreshold > 0 ? Math.min(100, Math.round((subtotal / freeshipThreshold) * 100)) : 100;

        bar.style.width = percent + '%';
        bar.setAttribute('aria-valuenow', percent);

        if (needed > 0) {
            bar.className = 'progress-bar bg-danger';
            if (text) {
                text.innerHTML = `<span>Mua thêm <strong class="text-danger">${formatVND(needed)}</strong> để được <strong>FREESHIP</strong>! 🚚</span><span class="text-muted small fw-semibold">${percent}%</span>`;
            }
        } else {
            bar.className = 'progress-bar bg-success';
            if (text) {
                text.innerHTML = `<span class="text-success fw-bold"><i class="bi bi-patch-check-fill me-1"></i>Đơn hàng đủ điều kiện MIỄN PHÍ VẬN CHUYỂN! 🎉</span><span class="text-muted small fw-semibold">100%</span>`;
            }
        }
    }

    // Re-render drawer items list
    window.renderDrawerItems = function (cart, subtotal) {
        const itemsList = document.getElementById('drawer-items-list');
        const totalPriceEl = document.getElementById('drawer-total-price');
        if (totalPriceEl) totalPriceEl.textContent = formatVND(subtotal);

        updateFreeshipUI(subtotal);

        const items = Object.entries(cart || {});
        if (!itemsList) return;

        if (items.length === 0) {
            itemsList.innerHTML = `
                <div class="text-center py-5" id="drawer-empty-msg">
                    <i class="bi bi-cart-x text-muted display-4"></i>
                    <p class="text-muted mt-3 mb-3">Giỏ hàng của bạn đang trống</p>
                    <a href="/products" class="btn btn-sm btn-outline-danger rounded-pill px-4" data-bs-dismiss="offcanvas">
                        Khám phá sản phẩm
                    </a>
                </div>
            `;
            return;
        }

        let html = '';
        items.forEach(([key, item]) => {
            const imgSrc = item.image ? `/storage/${item.image}` : '/images/placeholder.svg';
            const priceFormatted = formatVND(item.price);
            const varLabel = item.variation ? `<div class="text-muted" style="font-size: 0.75rem;">Phân loại: ${escapeHtml(item.variation)}</div>` : '';
            html += `
                <div class="drawer-item card border-0 shadow-sm p-2 rounded-3" data-cart-key="${escapeHtml(key)}">
                    <div class="d-flex align-items-center gap-3">
                        <img src="${imgSrc}" class="rounded-3 object-fit-cover flex-shrink-0" width="64" height="64" alt="${escapeHtml(item.name)}" onerror="this.onerror=null;this.src='/images/placeholder.svg';">
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate text-dark small mb-1" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</div>
                            ${varLabel}
                            <div class="text-danger fw-bold small mt-1">${priceFormatted}</div>
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <div class="drawer-qty-picker d-inline-flex align-items-center border rounded-2 bg-light">
                                    <button type="button" class="btn btn-sm btn-link text-dark p-0 px-2 text-decoration-none" onclick="window.updateDrawerCartItem('${escapeHtml(key)}', -1)">−</button>
                                    <span class="px-2 small fw-bold drawer-item-qty" style="min-width: 24px; text-align: center;">${item.quantity}</span>
                                    <button type="button" class="btn btn-sm btn-link text-dark p-0 px-2 text-decoration-none" onclick="window.updateDrawerCartItem('${escapeHtml(key)}', 1)">+</button>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" onclick="window.removeDrawerCartItem('${escapeHtml(key)}')" title="Xóa khỏi giỏ">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        itemsList.innerHTML = html;
    };

    // Update item quantity in drawer
    window.updateDrawerCartItem = function (key, delta) {
        const itemEl = document.querySelector(`.drawer-item[data-cart-key="${key}"]`);
        const qtySpan = itemEl?.querySelector('.drawer-item-qty');
        const currentQty = Number(qtySpan?.textContent || 1);
        const newQty = currentQty + delta;
        if (newQty < 1) {
            window.removeDrawerCartItem(key);
            return;
        }

        fetch(`/cart/${encodeURIComponent(key)}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ quantity: newQty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.updateGlobalCartBadges(data.cart_count);
                window.renderDrawerItems(data.cart, data.subtotal);
            } else if (data.message) {
                window.showToast?.(data.message, 'warning');
            }
        })
        .catch(() => {
            window.showToast?.('Không thể cập nhật số lượng. Vui lòng thử lại.', 'danger');
        });
    };

    // Remove item from drawer
    window.removeDrawerCartItem = function (key) {
        fetch(`/cart/${encodeURIComponent(key)}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.updateGlobalCartBadges(data.cart_count);
                window.renderDrawerItems(data.cart, data.subtotal);
                window.showToast?.(data.message, 'success');
            }
        })
        .catch(() => {
            window.showToast?.('Không thể xóa sản phẩm. Vui lòng thử lại.', 'danger');
        });
    };

    // Global Quick Add handler
    document.addEventListener('click', function (e) {
        const quickAddBtn = e.target.closest('.btn-quick-add');
        if (!quickAddBtn) return;

        e.preventDefault();
        const hasVariations = quickAddBtn.dataset.hasVariations === 'true';
        const productSlug = quickAddBtn.dataset.quickAddSlug;
        const productId = quickAddBtn.dataset.quickAddId;

        if (hasVariations) {
            // Open quick view modal for selection
            window.openQuickView(productSlug);
            return;
        }

        // Direct add 1 item
        const origContent = quickAddBtn.innerHTML;
        quickAddBtn.disabled = true;
        quickAddBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ quantity: 1 })
        })
        .then(res => res.json())
        .then(data => {
            quickAddBtn.disabled = false;
            quickAddBtn.innerHTML = origContent;
            if (data.success) {
                window.updateGlobalCartBadges(data.cart_count);
                const cardImg = quickAddBtn.closest('.product-card')?.querySelector('img');
                if (cardImg && typeof window.flyToCart === 'function') {
                    window.flyToCart(cardImg);
                }
                window.showToast?.(data.message, 'success');
                window.renderDrawerItems(data.cart, data.subtotal);
                window.openCartDrawer();
            } else {
                window.showToast?.(data.message || 'Lỗi thêm sản phẩm', 'danger');
            }
        })
        .catch(() => {
            quickAddBtn.disabled = false;
            quickAddBtn.innerHTML = origContent;
            window.showToast?.('Không thể thêm sản phẩm vào giỏ. Vui lòng thử lại.', 'danger');
        });
    });

    // Global Quick View handler
    document.addEventListener('click', function (e) {
        const qvBtn = e.target.closest('.btn-quick-view');
        if (qvBtn) {
            e.preventDefault();
            const slug = qvBtn.dataset.quickViewSlug;
            window.openQuickView(slug);
        }
    });

    // Quick View Modal implementation
    const qvModalEl = document.getElementById('quickViewModal');
    const qvModalInstance = qvModalEl && window.bootstrap?.Modal ? bootstrap.Modal.getOrCreateInstance(qvModalEl) : null;
    let currentQvProduct = null;
    let selectedQvVariation = null;

    qvModalEl?.querySelector('[data-bs-dismiss="modal"]')?.addEventListener('click', function (event) {
        event.preventDefault();
        qvModalInstance?.hide();
    });

    qvModalEl?.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            qvModalInstance?.hide();
        }
    });

    window.openQuickView = function (slug) {
        if (!qvModalInstance) return;
        qvModalInstance.show();

        const skeleton = document.getElementById('quick-view-skeleton');
        const content = document.getElementById('quick-view-content');
        skeleton?.classList.remove('d-none');
        content?.classList.add('d-none');

        fetch(`/products/${encodeURIComponent(slug)}/quick-view`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            currentQvProduct = data;
            selectedQvVariation = data.variations && data.variations.length > 0 ? data.variations[0] : null;

            document.getElementById('qv-title').textContent = data.name;
            document.getElementById('qv-category').textContent = data.category_name;
            document.getElementById('qv-description').textContent = data.description;
            document.getElementById('qv-detail-link').href = data.detail_url;

            const mainImg = document.getElementById('qv-main-image');
            mainImg.src = data.main_image;
            mainImg.alt = data.name;

            const flashBadge = document.getElementById('qv-flash-badge');
            if (data.is_flash_sale) {
                flashBadge.classList.remove('d-none');
            } else {
                flashBadge.classList.add('d-none');
            }

            // Thumbs
            const thumbsWrap = document.getElementById('qv-thumbs');
            thumbsWrap.innerHTML = '';
            if (data.gallery && data.gallery.length > 1) {
                data.gallery.forEach((url, i) => {
                    const thumb = document.createElement('img');
                    thumb.src = url;
                    thumb.className = `rounded-2 object-fit-cover border cursor-pointer ${i === 0 ? 'border-primary' : ''}`;
                    thumb.style.width = '52px';
                    thumb.style.height = '52px';
                    thumb.style.cursor = 'pointer';
                    thumb.onclick = () => {
                        mainImg.src = url;
                        thumbsWrap.querySelectorAll('img').forEach(img => img.classList.remove('border-primary'));
                        thumb.classList.add('border-primary');
                    };
                    thumbsWrap.appendChild(thumb);
                });
                thumbsWrap.classList.remove('d-none');
            } else {
                thumbsWrap.classList.add('d-none');
            }

            // Price & Stock
            updateQvPriceAndStock();

            // Variations
            const varSec = document.getElementById('qv-variations-section');
            const varOpts = document.getElementById('qv-variation-options');
            varOpts.innerHTML = '';
            if (data.has_variations) {
                varSec.classList.remove('d-none');
                data.variations.forEach((v, idx) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `btn btn-sm ${idx === 0 ? 'btn-danger' : 'btn-outline-secondary'} rounded-3`;
                    btn.textContent = v.label;
                    btn.onclick = () => {
                        selectedQvVariation = v;
                        varOpts.querySelectorAll('button').forEach(b => {
                            b.className = 'btn btn-sm btn-outline-secondary rounded-3';
                        });
                        btn.className = 'btn btn-sm btn-danger rounded-3';
                        if (v.image) {
                            mainImg.src = v.image;
                        }
                        updateQvPriceAndStock();
                    };
                    varOpts.appendChild(btn);
                });
            } else {
                varSec.classList.add('d-none');
            }

            // Reset quantity to 1
            const qtyInput = document.getElementById('qv-quantity');
            if (qtyInput) qtyInput.value = 1;

            skeleton?.classList.add('d-none');
            content?.classList.remove('d-none');
        })
        .catch(() => {
            skeleton?.classList.add('d-none');
            window.showToast?.('Không thể tải thông tin sản phẩm.', 'danger');
            qvModalInstance.hide();
        });
    };

    function updateQvPriceAndStock() {
        if (!currentQvProduct) return;
        const priceEl = document.getElementById('qv-price');
        const origPriceEl = document.getElementById('qv-original-price');
        const stockEl = document.getElementById('qv-stock-label');
        const addBtn = document.getElementById('qv-btn-add-cart');
        const buyNowBtn = document.getElementById('qv-btn-buy-now');

        const price = selectedQvVariation ? selectedQvVariation.formatted_price : currentQvProduct.formatted_price;
        const origPrice = selectedQvVariation ? selectedQvVariation.formatted_original_price : currentQvProduct.formatted_original_price;
        const stock = selectedQvVariation ? selectedQvVariation.stock : currentQvProduct.stock;

        priceEl.textContent = price;
        if (currentQvProduct.is_flash_sale || (selectedQvVariation && selectedQvVariation.price < selectedQvVariation.original_price)) {
            origPriceEl.textContent = origPrice;
            origPriceEl.classList.remove('d-none');
        } else {
            origPriceEl.classList.add('d-none');
        }

        if (stock > 0) {
            stockEl.textContent = `Còn ${stock} sản phẩm`;
            stockEl.className = 'badge bg-success-subtle text-success rounded-pill';
            if (addBtn) addBtn.disabled = false;
            if (buyNowBtn) buyNowBtn.disabled = false;
        } else {
            stockEl.textContent = 'Hết hàng';
            stockEl.className = 'badge bg-danger-subtle text-danger rounded-pill';
            if (addBtn) addBtn.disabled = true;
            if (buyNowBtn) buyNowBtn.disabled = true;
        }
    }

    // Quick View Quantity +/-
    document.getElementById('qv-qty-minus')?.addEventListener('click', () => {
        const inp = document.getElementById('qv-quantity');
        if (!inp) return;
        inp.value = Math.max(1, Number(inp.value) - 1);
    });

    document.getElementById('qv-qty-plus')?.addEventListener('click', () => {
        const inp = document.getElementById('qv-quantity');
        if (!inp || !currentQvProduct) return;
        const maxStock = selectedQvVariation ? selectedQvVariation.stock : currentQvProduct.stock;
        inp.value = Math.min(maxStock, Number(inp.value) + 1);
    });

    // Quick View Add to Cart
    document.getElementById('qv-btn-add-cart')?.addEventListener('click', function () {
        if (!currentQvProduct) return;
        const qty = Number(document.getElementById('qv-quantity')?.value || 1);
        const payload = { quantity: qty };
        if (selectedQvVariation) {
            payload.variation_id = selectedQvVariation.id;
        }

        const btn = this;
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang thêm...';

        fetch(currentQvProduct.add_cart_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            if (data.success) {
                window.updateGlobalCartBadges(data.cart_count);
                qvModalInstance?.hide();
                window.showToast?.(data.message, 'success');
                window.renderDrawerItems(data.cart, data.subtotal);
                window.openCartDrawer();
            } else {
                window.showToast?.(data.message || 'Lỗi thêm vào giỏ', 'danger');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            window.showToast?.('Không thể thêm sản phẩm vào giỏ.', 'danger');
        });
    });

    // Quick View Buy Now (Mua ngay)
    document.getElementById('qv-btn-buy-now')?.addEventListener('click', function () {
        if (!currentQvProduct) return;
        const qty = Number(document.getElementById('qv-quantity')?.value || 1);
        const payload = { quantity: qty, buy_now: 1 };
        if (selectedQvVariation) {
            payload.variation_id = selectedQvVariation.id;
        }

        const btn = this;
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang xử lý...';

        fetch(currentQvProduct.add_cart_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            if (data.success) {
                window.location.href = data.redirect_url || '/cart';
            } else {
                window.showToast?.(data.message || 'Lỗi xử lý mua ngay', 'danger');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            window.showToast?.('Không thể thực hiện mua ngay. Vui lòng thử lại.', 'danger');
        });
    });

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
});
