/* products/show.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {

    // 1. LƯU SẢN PHẨM VÀO LỊCH SỬ ĐÃ XEM (PROMPT 3.5)
    
    try {
        const RECENT_KEY = 'beatycare-recently-viewed';
        let recents = JSON.parse(localStorage.getItem(RECENT_KEY) || '[]');
        recents = recents.filter(item => item.slug !== null);
        const currentSlug = document.getElementById('product-question-form')?.dataset.productSlug || '';
        recents.unshift(currentSlug);
        if (recents.length > 20) recents.pop();
        localStorage.setItem(RECENT_KEY, JSON.stringify(recents));
    } catch(e) {}

    // TRICK GIẢI CỨU MÀN HÌNH ĐEN: Đưa Lightbox tự code ra ngoài thẻ body
    const reviewLightbox = document.getElementById('reviewImageModal');
    if (reviewLightbox) {
        document.body.appendChild(reviewLightbox);
    }
    const galleryLightbox = document.getElementById('productGalleryModal');
    if (galleryLightbox) {
        document.body.appendChild(galleryLightbox);
    }

    const mainImage = document.getElementById('detail-main-image');
    document.querySelectorAll('.detail-thumb').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            if (mainImage?.tagName === 'IMG') mainImage.src = this.dataset.image;
            document.querySelectorAll('.detail-thumb').forEach(item => item.classList.remove('active'));
            this.classList.add('active');
        });
    });

    const quantityInput = document.getElementById('detail-quantity');
    const priceElement = document.querySelector('.detail-price');
    const stockElement = document.querySelector('.detail-stock');
    const stockNote = document.getElementById('detail-stock-note');
    document.querySelectorAll('input[name="variation_id"]').forEach(function (option) {
        option.addEventListener('change', function () {
            const stock = Number(this.dataset.stock || 0);
            quantityInput.max = Math.max(1, stock);
            quantityInput.value = Math.min(Number(quantityInput.value || 1), Math.max(1, stock));
            priceElement.textContent = Number(this.dataset.price).toLocaleString('vi-VN') + ' đ';
            stockNote.textContent = 'Tối đa ' + stock + ' sản phẩm';
            stockElement.classList.toggle('out', stock < 1);
            stockElement.innerHTML = stock > 0 ? '<i class="bi bi-check-circle me-1"></i>Còn ' + stock + ' sản phẩm' : '<i class="bi bi-x-circle me-1"></i>Hết hàng';
            if (this.dataset.image && mainImage?.tagName === 'IMG') {
                mainImage.src = this.dataset.image;
                document.querySelectorAll('.detail-thumb').forEach(item => item.classList.toggle('active', item.dataset.variation === this.value));
            }
            document.querySelectorAll('.detail-buy').forEach(button => {
                button.disabled = stock < 1;
            });
        });
    });
    document.querySelector('input[name="variation_id"]:checked')?.dispatchEvent(new Event('change'));

    document.querySelectorAll('[data-review-filter]').forEach(function (filter) {
        filter.addEventListener('click', function () {
            document.querySelectorAll('.review-filter').forEach(item => item.classList.remove('active'));
            this.classList.add('active');
            const selected = this.dataset.reviewFilter;
            document.querySelectorAll('[data-review-rating]').forEach(function (review) {
                const visible = selected === 'all'
                    || (selected === 'comment' && review.dataset.reviewComment === '1')
                    || (selected === 'media' && review.dataset.reviewMedia === '1')
                    || review.dataset.reviewRating === selected;
                review.classList.toggle('d-none', !visible);
            });
        });
    });

    // 2. BÌNH CHỌN HỮU ÍCH ĐÁNH GIÁ (PROMPT 3.7)
    document.querySelectorAll('.btn-vote-helpful').forEach(btn => {
        btn.addEventListener('click', async function() {
            const reviewId = this.dataset.reviewId;
            try {
                const res = await fetch(`/reviews/${reviewId}/vote-helpful`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (res.status === 401) {
                    if (window.showToast) window.showToast('Vui lòng đăng nhập để bình chọn đánh giá.', 'warning');
                    else alert('Vui lòng đăng nhập để bình chọn đánh giá.');
                    return;
                }
                if (!res.ok) {
                    if (window.showToast) window.showToast(data.message || 'Không thể thực hiện', 'warning');
                    return;
                }
                this.querySelector('.helpful-count').textContent = data.helpful_count;
                const icon = this.querySelector('i');
                if (data.voted) {
                    this.classList.add('active', 'text-primary', 'border-primary', 'bg-primary-subtle');
                    icon.className = 'bi bi-hand-thumbs-up-fill me-1';
                } else {
                    this.classList.remove('active', 'text-primary', 'border-primary', 'bg-primary-subtle');
                    icon.className = 'bi bi-hand-thumbs-up me-1';
                }
                if (window.showToast) window.showToast(data.message, 'success');
            } catch(e) {
                console.error(e);
            }
        });
    });

    // 3. STICKY ADD TO CART INTERSECTION OBSERVER (PROMPT 3.5)
    const stickyBar = document.getElementById('sticky-add-to-cart');
    const mainAddToCartBtn = document.getElementById('btn-add-to-cart');
    if (stickyBar && mainAddToCartBtn) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.boundingClientRect.top < 0 && !entry.isIntersecting) {
                    stickyBar.classList.add('is-visible');
                    stickyBar.setAttribute('aria-hidden', 'false');
                } else {
                    stickyBar.classList.remove('is-visible');
                    stickyBar.setAttribute('aria-hidden', 'true');
                }
            });
        }, { threshold: 0 });
        observer.observe(mainAddToCartBtn);
    }

    // 4. GALLERY LIGHTBOX CONTROLLER (PROMPT 3.5)
    const galleryImages = Array.from(document.querySelectorAll('.detail-thumb')).map(thumb => thumb.dataset.image).filter(Boolean);
    let currentGalleryIdx = 0;
    const modalMainImg = document.getElementById('galleryModalMainImg');
    const modalCounter = document.getElementById('galleryModalCounter');
    const modalThumbsContainer = document.getElementById('galleryModalThumbs');

    if (modalThumbsContainer && galleryImages.length > 0) {
        galleryImages.forEach((src, idx) => {
            const thumb = document.createElement('img');
            thumb.src = src;
            thumb.className = 'gallery-modal-thumb' + (idx === 0 ? ' active' : '');
            thumb.onclick = () => window.setGalleryIndex(idx);
            modalThumbsContainer.appendChild(thumb);
        });
    }

    window.openGalleryModal = function() {
        if (!galleryLightbox || galleryImages.length === 0) return;
        const currentSrc = mainImage?.src;
        currentGalleryIdx = Math.max(0, galleryImages.indexOf(currentSrc));
        window.setGalleryIndex(currentGalleryIdx);
        galleryLightbox.classList.add('is-open');
        galleryLightbox.setAttribute('aria-hidden', 'false');
    };

    window.closeGalleryModal = function() {
        if (!galleryLightbox) return;
        galleryLightbox.classList.remove('is-open');
        galleryLightbox.setAttribute('aria-hidden', 'true');
    };

    window.setGalleryIndex = function(idx) {
        if (idx < 0) idx = galleryImages.length - 1;
        if (idx >= galleryImages.length) idx = 0;
        currentGalleryIdx = idx;
        if (modalMainImg) modalMainImg.src = galleryImages[idx];
        if (modalCounter) modalCounter.textContent = `${idx + 1} / ${galleryImages.length}`;
        document.querySelectorAll('.gallery-modal-thumb').forEach((t, i) => {
            t.classList.toggle('active', i === idx);
        });
    };

    window.navGallery = function(dir) {
        window.setGalleryIndex(currentGalleryIdx + dir);
    };

    document.addEventListener('keydown', function(e) {
        if (galleryLightbox && galleryLightbox.classList.contains('is-open')) {
            if (e.key === 'Escape') window.closeGalleryModal();
            if (e.key === 'ArrowLeft') window.navGallery(-1);
            if (e.key === 'ArrowRight') window.navGallery(1);
        }
    });

    const reviewImageModal = document.getElementById('reviewImageModal');
    const reviewImagePreview = document.getElementById('reviewImagePreview');
    document.querySelectorAll('[data-review-image]').forEach(function (imageButton) {
        imageButton.addEventListener('click', function () {
            reviewImagePreview.src = this.dataset.reviewImage;
            reviewImageModal.classList.add('is-open');
            reviewImageModal.setAttribute('aria-hidden', 'false');
        });
    });
    reviewImageModal?.addEventListener('click', function (event) {
        if (!event.target.closest('.review-image-preview')) {
            window.closeReviewImage(event);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && reviewImageModal?.classList.contains('is-open')) {
            window.closeReviewImage(event);
        }
    });

    window.closeReviewImage = function (event) {
        event?.stopPropagation();
        if (!reviewImageModal) return;
        reviewImageModal.classList.remove('is-open');
        reviewImageModal.setAttribute('aria-hidden', 'true');
        reviewImagePreview?.removeAttribute('src');
    };

    document.querySelectorAll('[data-quantity-step]').forEach(function (button) {
        button.addEventListener('click', function () {
            const step = Number(this.dataset.quantityStep);
            const next = Math.max(Number(quantityInput.min), Math.min(Number(quantityInput.max), Number(quantityInput.value || 1) + step));
            quantityInput.value = next;
        });
    });

    document.getElementById('copy-product-link')?.addEventListener('click', async function () {
        await navigator.clipboard.writeText(window.location.href);
        const original = this.innerHTML;
        this.innerHTML = '<i class="bi bi-check2 me-1"></i>Đã sao chép';
        setTimeout(() => this.innerHTML = original, 1800);
        if (window.showToast) window.showToast('Đã sao chép liên kết vào bộ nhớ tạm!', 'success');
    });

    const cartForm = document.getElementById('detail-cart-form');
    if (cartForm) {
        const buyNowInput = document.getElementById('detail-buy-now-input');
        const buyNowBtn = document.getElementById('btn-buy-now');
        const addToCartBtn = document.getElementById('btn-add-to-cart');

        if (buyNowBtn) {
            buyNowBtn.addEventListener('click', function() {
                if (buyNowInput) buyNowInput.value = '1';
            });
        }
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                if (buyNowInput) buyNowInput.value = '0';
            });
        }

        cartForm.addEventListener('submit', function(e) {
            const isBuyNow = (buyNowInput && buyNowInput.value === '1') || 
                             (e.submitter && (e.submitter.name === 'buy_now' || e.submitter.id === 'btn-buy-now'));

            if (isBuyNow) {
                if (buyNowInput) buyNowInput.value = '1';
                if (buyNowBtn && window.setButtonLoading) {
                    window.setButtonLoading(buyNowBtn, 'Đang chuyển thanh toán...');
                }
                return;
            }

            if (buyNowInput) buyNowInput.value = '0';
            e.preventDefault();
            const mainImg = document.getElementById('detail-main-image');
            if (addToCartBtn && window.setButtonLoading) {
                window.setButtonLoading(addToCartBtn, 'Đang thêm...');
            }
            if (window.flyToCart && mainImg) {
                window.flyToCart(mainImg);
                setTimeout(() => {
                    HTMLFormElement.prototype.submit.call(cartForm);
                }, 800);
            } else {
                HTMLFormElement.prototype.submit.call(cartForm);
            }
        });
    }
});

// 5. HỎI ĐÁP SẢN PHẨM HANDLERS (PROMPT 3.5 & 3.7)
async function handleQuestionSubmit(event) {
    event.preventDefault();
    const input = document.getElementById('qa-question-input');
    const btn = document.getElementById('btn-submit-question');
    const question = input.value.trim();
    if (!question || question.length < 5) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang gửi...';

    try {
        const res = await fetch(document.getElementById('product-question-form').dataset.questionUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ question })
        });
        const data = await res.json();
        if (res.ok) {
            if (window.showToast) window.showToast(data.message, 'success');
            input.value = '';
            const emptyMsg = document.getElementById('qa-empty-msg');
            if (emptyMsg) emptyMsg.remove();
            
            const list = document.getElementById('qa-list');
            if (list && data.question) {
                const item = document.createElement('div');
                item.className = 'p-3 rounded-3 bg-light border qa-item';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="fw-bold text-dark"><i class="bi bi-question-circle-fill text-warning me-1"></i>${data.question.user_name}</div>
                        <small class="text-muted">${data.question.created_at}</small>
                    </div>
                    <p class="mb-2 text-dark">${data.question.question}</p>
                    <div class="ms-3 text-muted small fst-italic"><i class="bi bi-hourglass-split me-1"></i>Đang chờ chuyên viên Aloha Beauty phản hồi...</div>
                `;
                list.prepend(item);
                const countEl = document.getElementById('qa-count');
                if (countEl) countEl.textContent = Number(countEl.textContent || 0) + 1;
            }
        } else {
            if (window.showToast) window.showToast(data.message || 'Không thể gửi câu hỏi.', 'warning');
        }
    } catch(e) {
        console.error(e);
        if (window.showToast) window.showToast('Lỗi kết nối máy chủ.', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send me-1"></i>Gửi câu hỏi';
    }
}

function toggleAnswerForm(id) {
    const form = document.getElementById('answer-form-' + id);
    if (form) form.classList.toggle('d-none');
}
