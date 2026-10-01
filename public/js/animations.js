/**
 * BEATYCARE 🌸 - COMPREHENSIVE LUXURY ANIMATION ENGINE
 * Handles: Preloader, Scroll-Driven Reading Tracker, Circular Back-to-Top,
 * Scroll Reveal Cascade, Fly-to-Cart Physics, Dynamic Glass Navbar, 
 * Micro-interactions, Tactile Steppers & Modern Toasts.
 */

// Biến dùng chung kiểm tra prefers-reduced-motion trên toàn trang
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
window.prefersReducedMotion = prefersReducedMotion;

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // =========================================================================
    // 1. BRAND PRELOADER (ENTRANCE WHEN OPENING WEB)
    // =========================================================================
    const preloader = document.getElementById('app-preloader');
    const preloaderFill = document.querySelector('.preloader-progress-fill');
    
    if (preloader) {
        if (prefersReducedMotion.matches || sessionStorage.getItem('beatycare-preloader-shown') === '1') {
            preloader.style.display = 'none';
        } else {
            sessionStorage.setItem('beatycare-preloader-shown', '1');

            let progress = 15;
            if (preloaderFill) preloaderFill.style.width = '15%';

            // Smooth progress simulation while DOM / assets load
            const progressInterval = setInterval(() => {
                if (progress >= 85) {
                    clearInterval(progressInterval);
                } else {
                    progress += Math.random() * 18;
                    if (preloaderFill) preloaderFill.style.width = Math.min(progress, 88) + '%';
                }
            }, 80);

            const dismissPreloader = () => {
                clearInterval(progressInterval);
                if (preloaderFill) preloaderFill.style.width = '100%';
                
                setTimeout(() => {
                    preloader.classList.add('fade-out');
                    setTimeout(() => {
                        preloader.style.display = 'none';
                    }, 520);
                }, 250);
            };

            // Window loaded
            if (document.readyState === 'complete') {
                dismissPreloader();
            } else {
                window.addEventListener('load', dismissPreloader);
                // Fallback safety timeout (never stall user past 750ms)
                setTimeout(dismissPreloader, 750);
            }

            // Fast dismiss on click or Escape key
            preloader.addEventListener('click', dismissPreloader);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') dismissPreloader();
            });

            // Fast history traversal (bfcache)
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    preloader.style.display = 'none';
                }
            });
        }
    }

    // =========================================================================
    // 2. DUAL SCROLL TRACKER: TOP PROGRESS BAR & BACK-TO-TOP CIRCULAR RING
    // =========================================================================
    const topProgressBar = document.getElementById('top-progress-bar');
    const backToTopBtn = document.getElementById('back-to-top');
    const progressCircle = document.getElementById('scroll-progress-circle');
    const ringCircumference = 125.66; // 2 * PI * 20

    if (progressCircle) {
        progressCircle.style.strokeDasharray = `${ringCircumference} ${ringCircumference}`;
        progressCircle.style.strokeDashoffset = ringCircumference;
    }

    const updateScrollMetrics = () => {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = docHeight > 0 ? Math.min(1, Math.max(0, scrollTop / docHeight)) : 0;

        // Top hairline progress bar
        if (topProgressBar) {
            topProgressBar.style.width = (scrollPercent * 100) + '%';
            topProgressBar.style.opacity = scrollTop > 20 ? '1' : '0.85';
        }

        // Back to top button visibility & circular ring
        if (backToTopBtn) {
            if (scrollTop > 260) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }

            if (progressCircle) {
                const offset = ringCircumference - (scrollPercent * ringCircumference);
                progressCircle.style.strokeDashoffset = offset;
            }
        }
    };

    window.addEventListener('scroll', updateScrollMetrics, { passive: true });
    updateScrollMetrics(); // Initialize on load

    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // =========================================================================
    // 3. DYNAMIC GLASSMORPHIC NAVBAR ON SCROLL
    // =========================================================================
    const navbar = document.querySelector('.navbar.glass-navbar');
    if (navbar) {
        const checkNavbarScroll = () => {
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };
        window.addEventListener('scroll', checkNavbarScroll, { passive: true });
        checkNavbarScroll();
    }

    // =========================================================================
    // 4. UNIVERSAL SCROLL-REVEAL CASCADE (INTERSECTION OBSERVER)
    // =========================================================================
    const revealObserverOptions = {
        root: null,
        rootMargin: '0px 0px -20px 0px',
        threshold: 0.05
    };

    let revealObserver = null;
    if ('IntersectionObserver' in window) {
        revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    observer.unobserve(entry.target);
                }
            });
        }, revealObserverOptions);
    }

    function initScrollReveals() {
        const isInViewport = (el) => {
            const rect = el.getBoundingClientRect();
            return rect.top < (window.innerHeight || document.documentElement.clientHeight) + 120 && rect.bottom > -40;
        };

        // Explicitly declared reveal elements
        const explicitReveals = document.querySelectorAll(
            '.reveal-up, .reveal-down, .reveal-left, .reveal-right, .reveal-scale, .reveal-fade, .reveal-blur'
        );
        explicitReveals.forEach(el => {
            if (isInViewport(el) || !revealObserver) {
                el.classList.add('in-view');
            } else if (!el.classList.contains('in-view')) {
                revealObserver.observe(el);
            }
        });

        // Stagger containers: dynamically assign stagger index to children
        document.querySelectorAll('.reveal-stagger').forEach(container => {
            Array.from(container.children).forEach((child, index) => {
                child.style.setProperty('--stagger-index', index);
            });
            if (isInViewport(container) || !revealObserver) {
                container.classList.add('in-view');
            } else if (!container.classList.contains('in-view')) {
                revealObserver.observe(container);
            }
        });

        // Auto-reveal for major storefront cards (excluding basic product-card to prevent blank space)
        const autoComponents = document.querySelectorAll(
            '.category-card, .flash-sale-card, .hot-product-card, .blog-card, .account-hub-card, .footer-promise'
        );
        let autoIndex = 0;
        autoComponents.forEach((card) => {
            if (!card.classList.contains('reveal-up') && !card.classList.contains('in-view')) {
                card.classList.add('reveal-up');
                card.style.transitionDelay = `${(autoIndex % 4) * 70}ms`;
                autoIndex++;
                if (isInViewport(card) || !revealObserver) {
                    card.classList.add('in-view');
                } else {
                    revealObserver.observe(card);
                }
            }
        });
    }

    initScrollReveals();
    window.refreshScrollReveals = initScrollReveals;

    // =========================================================================
    // 5. FLY TO CART PHYSICS & CART BADGE POP ANIMATION
    // =========================================================================
    window.flyToCart = function (imgElement, cartIconSelector = '.bi-cart, .bi-cart3, .bi-bag-heart') {
        if (!imgElement) return;
        const cartIcon = document.querySelector(cartIconSelector);
        if (!cartIcon) return;

        // Nếu bật reduced-motion: chỉ cập nhật/pulse badge, không bay hình ảnh
        if (prefersReducedMotion.matches) {
            const cartContainer = cartIcon.closest('.nav-item') || cartIcon.parentElement;
            const badge = cartContainer?.querySelector('.badge');
            if (badge) {
                badge.classList.remove('badge-pulse');
                void badge.offsetWidth;
                badge.classList.add('badge-pulse');
                setTimeout(() => badge.classList.remove('badge-pulse'), 350);
            }
            return;
        }

        const imgRect = imgElement.getBoundingClientRect();
        const cartRect = cartIcon.getBoundingClientRect();

        const flyingImg = imgElement.cloneNode(true);
        flyingImg.className = 'flying-cart-img';
        flyingImg.style.top = imgRect.top + 'px';
        flyingImg.style.left = imgRect.left + 'px';
        flyingImg.style.width = Math.min(imgRect.width, 90) + 'px';
        flyingImg.style.height = Math.min(imgRect.height, 90) + 'px';
        document.body.appendChild(flyingImg);

        // Force reflow for silky smooth CSS transition
        void flyingImg.offsetWidth;

        // Fly trajectory with scale and fade
        flyingImg.style.top = (cartRect.top - 10) + 'px';
        flyingImg.style.left = (cartRect.left - 10) + 'px';
        flyingImg.style.width = '24px';
        flyingImg.style.height = '24px';
        flyingImg.style.transform = 'scale(0.2) rotate(360deg)';
        flyingImg.style.opacity = '0.15';

        setTimeout(() => {
            flyingImg.remove();
            
            // Pop & bounce target cart icon
            const cartContainer = cartIcon.closest('.nav-item') || cartIcon.parentElement;
            if (cartContainer) {
                cartContainer.classList.remove('cart-bounce-pop');
                void cartContainer.offsetWidth; // reflow
                cartContainer.classList.add('cart-bounce-pop');
                setTimeout(() => cartContainer.classList.remove('cart-bounce-pop'), 700);
            }

            // Pulse badge
            const badge = cartContainer?.querySelector('.badge');
            if (badge) {
                badge.classList.add('badge-pulse');
                setTimeout(() => badge.classList.remove('badge-pulse'), 1200);
            }
        }, 750);
    };

    // =========================================================================
    // 6. ENHANCED TOAST NOTIFICATIONS (WITH TIMED PROGRESS BAR)
    // =========================================================================
    window.showToast = function (message, type = 'success', duration = 3500) {
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = `custom-toast toast-${type}`;

        let iconClass = 'bi-check-circle-fill';
        if (type === 'error' || type === 'danger') iconClass = 'bi-exclamation-circle-fill';
        if (type === 'info') iconClass = 'bi-info-circle-fill';
        if (type === 'warning') iconClass = 'bi-exclamation-triangle-fill';

        toast.innerHTML = `
            <div class="custom-toast-inner">
                <div class="toast-content d-flex align-items-center gap-2">
                    <i class="bi ${iconClass} toast-icon fs-5"></i>
                    <span>${message}</span>
                </div>
                <button class="toast-close border-0 bg-transparent text-secondary p-0" aria-label="Đóng"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="toast-progress-bar">
                <div class="toast-progress-bar-fill"></div>
            </div>
        `;

        toastContainer.appendChild(toast);

        // Animate entrance
        requestAnimationFrame(() => {
            toast.classList.add('show');
            const progressFill = toast.querySelector('.toast-progress-bar-fill');
            if (progressFill) {
                progressFill.style.transitionDuration = `${duration}ms`;
                progressFill.style.width = '0%';
            }
        });

        let removeTimeout = setTimeout(() => dismiss(), duration);

        const dismiss = () => {
            clearTimeout(removeTimeout);
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        };

        toast.querySelector('.toast-close').addEventListener('click', dismiss);

        // Pause on mouse hover
        toast.addEventListener('mouseenter', () => clearTimeout(removeTimeout));
        toast.addEventListener('mouseleave', () => {
            removeTimeout = setTimeout(() => dismiss(), 1500);
        });
    };

    // =========================================================================
    // 7. CART PAGE INTERACTIVE ENHANCEMENTS (SMOOTH DELETE ROW & HIGHLIGHT)
    // =========================================================================
    const cartRemoveForms = document.querySelectorAll('.cart-remove-form');
    cartRemoveForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const row = this.closest('tr') || this.closest('.cart-item-row');
            if (row && !row.classList.contains('is-removing')) {
                e.preventDefault();
                row.classList.add('is-removing');
                setTimeout(() => {
                    form.submit();
                }, 320);
            }
        });
    });

    // Quantity buttons tactile feedback
    document.querySelectorAll('.quantity-step, .quantity-picker button').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            if (row) {
                const lineTotal = row.querySelector('.line-total');
                if (lineTotal) {
                    lineTotal.classList.remove('flash-highlight');
                    void lineTotal.offsetWidth; // Reflow
                    lineTotal.classList.add('flash-highlight');
                }
            }
        });
    });

    // =========================================================================
    // 8. WISHLIST HEART BURST POP
    // =========================================================================
    document.querySelectorAll('form[action*="wishlist"]').forEach(form => {
        const btn = form.querySelector('button');
        if (btn) {
            btn.addEventListener('click', function () {
                if (prefersReducedMotion.matches) return;
                this.classList.add('heart-burst');
                setTimeout(() => this.classList.remove('heart-burst'), 650);
            });
        }
    });

    // =========================================================================
    // 9. PRODUCT DETAIL GALLERY THUMBNAIL CROSSFADE
    // =========================================================================
    const mainImg = document.getElementById('detail-main-image');
    const thumbs = document.querySelectorAll('.detail-thumb');
    if (mainImg && thumbs.length > 0) {
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function () {
                const newSrc = this.getAttribute('data-image');
                if (newSrc && mainImg.src !== newSrc) {
                    mainImg.classList.add('img-fading');
                    setTimeout(() => {
                        mainImg.src = newSrc;
                        mainImg.classList.remove('img-fading');
                    }, 180);

                    thumbs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    }

    // =========================================================================
    // 10. NUMBER COUNT-UP WITH SMOOTH EASING
    // =========================================================================
    const countUpElements = document.querySelectorAll('.count-up');
    if (countUpElements.length > 0) {
        const countObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-target'), 10);
                    if (isNaN(target)) return;

                    // Nếu bật reduced-motion: hiển thị số cuối ngay lập tức
                    if (prefersReducedMotion.matches) {
                        el.innerText = target;
                        observer.unobserve(el);
                        return;
                    }

                    const duration = 1800;
                    const startTime = performance.now();

                    const step = (currentTime) => {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        // Ease Out Expo
                        const easeOut = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                        const current = Math.round(target * easeOut);
                        el.innerText = current;

                        if (progress < 1) {
                            requestAnimationFrame(step);
                        } else {
                            el.innerText = target;
                        }
                    };

                    requestAnimationFrame(step);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.15 });

        countUpElements.forEach(el => countObserver.observe(el));
    }

    // =========================================================================
    // 11. BUTTON RIPPLE EFFECT
    // =========================================================================
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${e.clientX - rect.left - (size / 2)}px`;
            ripple.style.top = `${e.clientY - rect.top - (size / 2)}px`;

            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // =========================================================================
    // 12. FAST NAVIGATION LOADING FEEDBACK (INTERNAL LINK PROGRESS BAR)
    // =========================================================================
    document.querySelectorAll('a[href]:not([target="_blank"]):not([href^="#"]):not([href^="javascript"]):not([href^="mailto"]):not([href^="tel"])').forEach(link => {
        link.addEventListener('click', function (e) {
            // Ignore if opening in new tab or holding cmd/ctrl
            if (e.metaKey || e.ctrlKey || e.shiftKey) return;
            const href = this.getAttribute('href');
            if (href && !href.startsWith('#')) {
                if (topProgressBar) {
                    topProgressBar.style.width = '75%';
                    topProgressBar.style.opacity = '1';
                }
            }
        });
    });

    // =========================================================================
    // 13. CONFETTI CELEBRATION (PROMPT 4.4)
    // =========================================================================
    window.launchConfetti = function (durationMs = 2800) {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        const canvas = document.createElement('canvas');
        canvas.style.position = 'fixed';
        canvas.style.inset = '0';
        canvas.style.width = '100vw';
        canvas.style.height = '100vh';
        canvas.style.zIndex = '999999';
        canvas.style.pointerEvents = 'none';
        document.body.appendChild(canvas);

        const ctx = canvas.getContext('2d');
        const width = canvas.width = window.innerWidth;
        const height = canvas.height = window.innerHeight;

        const colors = ['#ff6b81', '#ff4757', '#ffa502', '#2ed573', '#1e90ff', '#a55eea', '#ff78c4'];
        const particles = Array.from({ length: 90 }, () => ({
            x: width * (0.3 + Math.random() * 0.4),
            y: height * 0.42,
            vx: (Math.random() - 0.5) * 16,
            vy: (Math.random() - 1.25) * 16,
            size: 6 + Math.random() * 6,
            color: colors[Math.floor(Math.random() * colors.length)],
            rotation: Math.random() * 360,
            rSpeed: (Math.random() - 0.5) * 12,
            gravity: 0.35 + Math.random() * 0.2,
            opacity: 1
        }));

        const startTime = Date.now();
        function frame() {
            const elapsed = Date.now() - startTime;
            if (elapsed > durationMs) {
                canvas.remove();
                return;
            }
            ctx.clearRect(0, 0, width, height);
            particles.forEach(p => {
                p.x += p.vx;
                p.y += p.vy;
                p.vy += p.gravity;
                p.vx *= 0.98;
                p.rotation += p.rSpeed;
                if (elapsed > durationMs - 800) {
                    p.opacity = Math.max(0, 1 - (elapsed - (durationMs - 800)) / 800);
                }

                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate((p.rotation * Math.PI) / 180);
                ctx.fillStyle = p.color;
                ctx.globalAlpha = p.opacity;
                ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
                ctx.restore();
            });
            requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
    };
});
