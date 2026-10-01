/* products/index.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ajax-filter-form');
    const gridContainer = document.getElementById('products-grid-container');
    const loadMoreBtn = document.getElementById('btn-load-more');
    const loadMoreSection = document.getElementById('load-more-section');
    const totalCountDisplay = document.getElementById('total-count-display');
    const mobileProductCount = document.getElementById('mobile-product-count');
    const chipsWrap = document.getElementById('active-chips');
    let searchDebounceTimeout = null;

    // Render filter chips
    function updateFilterChips() {
        if (!chipsWrap) return;
        const formData = new FormData(form);
        chipsWrap.innerHTML = '';
        let hasActiveFilters = false;

        const labels = {
            search: 'Từ khóa',
            category: 'Danh mục',
            brand: 'Thương hiệu',
            min_price: 'Giá từ',
            max_price: 'Giá đến',
            rating: 'Đánh giá',
            availability: 'Tình trạng',
        };

        for (const [key, value] of formData.entries()) {
            if (value && key !== 'sort' && key !== 'page') {
                hasActiveFilters = true;
                let displayVal = value;
                if (key === 'category') {
                    const opt = form.querySelector(`#filter-category option[value="${value}"]`);
                    displayVal = opt ? opt.textContent : value;
                } else if (key === 'brand') {
                    const opt = form.querySelector(`#filter-brand option[value="${value}"]`);
                    displayVal = opt ? opt.textContent : value;
                } else if (key === 'rating') {
                    displayVal = `${value} sao trở lên`;
                } else if (key === 'availability') {
                    displayVal = value === 'in_stock' ? 'Còn hàng' : 'Hết hàng';
                } else if (key === 'min_price' || key === 'max_price') {
                    displayVal = new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                }

                const chip = document.createElement('span');
                chip.className = 'badge bg-light text-dark border rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 small';
                chip.innerHTML = `<span><strong>${labels[key] || key}:</strong> ${displayVal}</span><button type="button" class="btn-close btn-close-sm" style="font-size: 0.65rem;" aria-label="Xóa bộ lọc ${labels[key]}"></button>`;
                chip.querySelector('button').onclick = () => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = '';
                        triggerAjaxFilter();
                    }
                };
                chipsWrap.appendChild(chip);
            }
        }

        if (hasActiveFilters) {
            const clearAllBtn = document.createElement('a');
            clearAllBtn.href = '/products';
            clearAllBtn.className = 'badge bg-danger-subtle text-danger rounded-pill px-3 py-2 text-decoration-none small fw-semibold';
            clearAllBtn.innerHTML = '<i class="bi bi-x-circle me-1"></i>Xóa tất cả bộ lọc';
            clearAllBtn.onclick = (e) => {
                e.preventDefault();
                form.reset();
                triggerAjaxFilter();
            };
            chipsWrap.appendChild(clearAllBtn);
        }
    }

    // Trigger AJAX filtering
    function triggerAjaxFilter(page = 1, append = false) {
        const formData = new FormData(form);
        const params = new URLSearchParams();
        for (const [k, v] of formData.entries()) {
            if (v) params.set(k, v);
        }
        if (page > 1) {
            params.set('page', page);
        }

        const url = `${form.getAttribute('action')}?${params.toString()}`;

        if (!append) {
            // Update browser URL
            window.history.pushState({ path: url }, '', url);
            // Show skeletons
            showSkeletons();
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (append) {
                // Append items
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;
                while (tempDiv.firstChild) {
                    gridContainer.appendChild(tempDiv.firstChild);
                }
            } else {
                gridContainer.innerHTML = data.html;
                gridContainer.classList.add('animate__animated', 'animate__fadeIn');
                setTimeout(() => gridContainer.classList.remove('animate__animated', 'animate__fadeIn'), 600);
            }
            gridContainer.classList.add('in-view');
            if (window.refreshScrollReveals) {
                window.refreshScrollReveals();
            }

            // Update counts
            if (totalCountDisplay) totalCountDisplay.textContent = data.total;
            if (mobileProductCount) mobileProductCount.textContent = `Tìm thấy ${data.total} sản phẩm`;

            // Update Load More button
            updateLoadMoreBtn(data.has_more, page + 1);

            // Update chips
            updateFilterChips();
        })
        .catch(() => {
            if (!append) {
                window.location.href = url; // Fallback to normal navigation
            }
        });
    }

    function showSkeletons() {
        let skelHtml = '';
        for (let i = 0; i < 4; i++) {
            skelHtml += `
                <div class="col-lg-3 col-md-4 col-sm-6 skeleton-card-item">
                    <div class="card product-card text-center h-100 shadow-sm border-0">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="skeleton skeleton-img mb-3"></div>
                            <div class="skeleton skeleton-text short mx-auto mb-2" style="width: 80px; height: 18px; border-radius: 999px;"></div>
                            <div class="skeleton skeleton-title mx-auto mb-2" style="width: 75%;"></div>
                            <div class="skeleton skeleton-text mx-auto mb-3"></div>
                            <div class="skeleton skeleton-title mx-auto mb-2" style="width: 50%; height: 22px;"></div>
                            <div class="mt-auto d-flex justify-content-center gap-2 pt-2">
                                <div class="skeleton skeleton-btn" style="width: 80px; height: 36px;"></div>
                                <div class="skeleton skeleton-btn" style="width: 40px; height: 36px; border-radius: 50%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        gridContainer.innerHTML = skelHtml;
    }

    function updateLoadMoreBtn(hasMore, nextPage) {
        if (!loadMoreSection) return;
        if (hasMore) {
            loadMoreSection.innerHTML = `
                <button type="button" id="btn-load-more" class="btn btn-outline-danger rounded-pill px-5 py-2 fw-semibold shadow-sm" data-next-page="${nextPage}">
                    <span class="load-more-text"><i class="bi bi-arrow-down-circle me-2"></i>Xem thêm sản phẩm</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            `;
            attachLoadMoreListener();
        } else {
            loadMoreSection.innerHTML = '<p class="text-muted small my-3">✨ Bạn đã xem hết tất cả sản phẩm.</p>';
        }
    }

    function attachLoadMoreListener() {
        const btn = document.getElementById('btn-load-more');
        if (!btn) return;
        btn.onclick = function () {
            const nextPage = Number(this.dataset.nextPage || 2);
            const textSpan = this.querySelector('.load-more-text');
            const spinner = this.querySelector('.spinner-border');
            this.disabled = true;
            textSpan?.classList.add('d-none');
            spinner?.classList.remove('d-none');

            triggerAjaxFilter(nextPage, true);
        };
    }

    // Attach listeners to filter form
    form.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', () => triggerAjaxFilter(1, false));
    });

    form.querySelectorAll('input[type="number"], input[type="search"]').forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(searchDebounceTimeout);
            searchDebounceTimeout = setTimeout(() => triggerAjaxFilter(1, false), 350);
        });
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        triggerAjaxFilter(1, false);
    });

    // Browser back/forward navigation support
    window.addEventListener('popstate', (e) => {
        const search = window.location.search;
        const params = new URLSearchParams(search);
        form.querySelectorAll('input, select').forEach(el => {
            el.value = params.get(el.name) || '';
        });
        triggerAjaxFilter(1, false);
    });

    // Initial setups
    updateFilterChips();
    attachLoadMoreListener();
});

