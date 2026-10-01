/**
 * =============================================================================
 * BEATYCARE 🌸 - COMPREHENSIVE PAGE TRANSITIONS & INTERACTION FEEDBACK
 * High-performance Vanilla JS Engine:
 * 1. Page Entrance (fade-in + slide-up on DOMContentLoaded)
 * 2. Page Exit (fade-out on internal navigation with 200ms smooth delay)
 * 3. Slim Top Loading Bar (beforeunload / load)
 * 4. Button Ripple Effect (radial wave on click)
 * 5. Button Loading / Success / Error Feedback (spinners, checkmarks, shakes)
 * 6. Add to Cart Header Pop & Number Count-Up Animation
 * 7. Wishlist Heart Pop with Bursting Heart Particles
 * 8. Sliding Tab Indicators & Smooth Tab Content Transitions
 * 9. Spring Modals (scale 0.9->1 on open, 1->0.9 on close)
 * 10. Animated SVG Checkmark Drawing for Completed Form Actions
 * =============================================================================
 */

(function (window, document) {
    'use strict';

    // =========================================================================
    // 1. PAGE TRANSITIONS (LOAD ENTRANCE & NAVIGATION EXIT)
    // =========================================================================
    const PageTransitionEngine = {
        progressBar: null,
        isNavigating: false,

        init() {
            this.progressBar = document.getElementById('top-progress-bar');

            // 1.1 Page entrance on DOMContentLoaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.handlePageEntrance());
            } else {
                this.handlePageEntrance();
            }

            // 1.2 Hide progress bar once window load completes
            window.addEventListener('load', () => this.finishLoadingBar());

            // 1.3 Intercept internal links for smooth 200ms page-exit fade
            document.addEventListener('click', (e) => this.handleLinkClick(e));

            // 1.4 Show progress bar on beforeunload
            window.addEventListener('beforeunload', () => {
                if (this.progressBar) {
                    this.progressBar.classList.add('is-loading');
                }
            });

            // 1.5 Handle Back-Forward Cache (bfcache)
            window.addEventListener('pageshow', (event) => {
                document.body.classList.remove('page-exit');
                this.isNavigating = false;
                this.finishLoadingBar();
                this.handlePageEntrance();
            });
        },

        handlePageEntrance() {
            // Apply loaded class to trigger main content fade-in + slide-up
            document.body.classList.add('page-loaded');
            
            // Finish top progress bar
            this.finishLoadingBar();
        },

        finishLoadingBar() {
            if (!this.progressBar) return;
            this.progressBar.classList.remove('is-loading');
            this.progressBar.classList.add('is-completed');
            setTimeout(() => {
                this.progressBar.classList.remove('is-completed');
                this.progressBar.style.opacity = '0';
            }, 300);
        },

        handleLinkClick(e) {
            // Target the closest anchor
            const link = e.target.closest('a');
            if (!link) return;

            // Check if user is holding modifier keys (open in new tab/window)
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

            // Ignore right/middle clicks
            if (e.button && e.button !== 0) return;

            const href = link.getAttribute('href');
            if (!href) return;

            // Filter out non-navigating links
            if (href.startsWith('#') || href.startsWith('javascript:')) return;
            if (href.startsWith('mailto:') || href.startsWith('tel:')) return;
            if (link.target === '_blank') return;
            if (link.hasAttribute('download')) return;
            if (link.hasAttribute('data-bs-toggle')) return; // Bootstrap modal / dropdown
            if (link.closest('.no-transition') || link.classList.contains('no-transition')) return;

            // Check same origin
            try {
                const targetUrl = new URL(href, window.location.href);
                if (targetUrl.origin !== window.location.origin) return; // External link

                // If same page hash, don't exit
                if (targetUrl.pathname === window.location.pathname && targetUrl.search === window.location.search && targetUrl.hash) {
                    return;
                }

                // Guard against double clicks during navigation
                if (this.isNavigating) {
                    e.preventDefault();
                    return;
                }

                e.preventDefault();
                this.isNavigating = true;

                // Show exit transition
                document.body.classList.add('page-exit');

                // Trigger top progress line
                if (this.progressBar) {
                    this.progressBar.style.opacity = '1';
                    this.progressBar.classList.add('is-loading');
                }

                // 200ms smooth delay then navigate
                setTimeout(() => {
                    window.location.href = targetUrl.href;
                }, 200);

            } catch (err) {
                // If invalid URL, fallback to default behavior
            }
        }
    };

    // =========================================================================
    // 2. BUTTON RIPPLE EFFECT
    // =========================================================================
    const RippleEngine = {
        init() {
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn');
                if (!btn || btn.disabled || btn.classList.contains('no-ripple')) return;

                const rect = btn.getBoundingClientRect();
                const ripple = document.createElement('span');
                ripple.className = 'btn-ripple';

                const diameter = Math.max(rect.width, rect.height) * 2;
                const radius = diameter / 2;

                ripple.style.width = ripple.style.height = `${diameter}px`;
                ripple.style.left = `${e.clientX - rect.left - radius}px`;
                ripple.style.top = `${e.clientY - rect.top - radius}px`;

                // Remove existing ripples if any
                const existing = btn.querySelector('.btn-ripple');
                if (existing) existing.remove();

                btn.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 520);
            });
        }
    };

    // =========================================================================
    // 3. BUTTON LOADING & ACTION FEEDBACK (SPINNERS, SUCCESS, ERROR)
    // =========================================================================
    const ButtonActionEngine = {
        init() {
            // Automatic hook for regular form submissions (login, register, search, checkout, cart update)
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (!form || form.classList.contains('no-loading-hook')) return;

                // If form has HTML5 validation and is invalid, don't lock the button
                if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.classList.add('shake-invalid');
                        setTimeout(() => firstInvalid.classList.remove('shake-invalid'), 500);
                    }
                    return;
                }

                // Find active submit button
                const submitBtn = e.submitter || form.querySelector('button[type="submit"], input[type="submit"], .btn-submit');
                if (submitBtn && !submitBtn.disabled) {
                    this.setButtonLoading(submitBtn);
                }
            });
        },

        setButtonLoading(btn, customText = null) {
            if (!btn || btn.disabled) return;

            // Cache original HTML and width to prevent layout jump
            if (!btn.dataset.originalHtml) {
                btn.dataset.originalHtml = btn.innerHTML;
                btn.dataset.originalWidth = btn.offsetWidth + 'px';
            }

            btn.style.width = btn.dataset.originalWidth;
            btn.classList.add('is-loading');
            btn.style.pointerEvents = 'none';
            btn.setAttribute('aria-busy', 'true');
            setTimeout(() => { if (btn) btn.disabled = true; }, 150);

            const text = customText || btn.dataset.loadingText;
            if (text) {
                btn.innerHTML = `<span class="btn-spinner me-2" aria-hidden="true"></span><span>${text}</span>`;
            } else {
                btn.innerHTML = `<span class="btn-spinner" aria-hidden="true"></span>`;
            }
        },

        setButtonSuccess(btn, message = null, duration = 1400, callback = null) {
            if (!btn) return;

            btn.classList.remove('is-loading');
            btn.classList.add('btn-action-success');
            btn.innerHTML = `<i class="bi bi-check2-lg me-1"></i><span>${message || 'Thành công'}</span>`;

            setTimeout(() => {
                this.resetButton(btn);
                if (typeof callback === 'function') callback();
            }, duration);
        },

        setButtonError(btn, message = null, duration = 1200) {
            if (!btn) return;

            btn.classList.remove('is-loading');
            btn.classList.add('btn-action-error');
            if (message) {
                btn.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i><span>${message}</span>`;
            }

            setTimeout(() => {
                this.resetButton(btn);
            }, duration);
        },

        resetButton(btn) {
            if (!btn) return;

            btn.classList.remove('is-loading', 'btn-action-success', 'btn-action-error');
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            btn.style.width = '';

            if (btn.dataset.originalHtml) {
                btn.innerHTML = btn.dataset.originalHtml;
                delete btn.dataset.originalHtml;
                delete btn.dataset.originalWidth;
            }
        }
    };

    // =========================================================================
    // 4. ADD TO CART FEEDBACK (HEADER POP & BADGE COUNT ANIMATION)
    // =========================================================================
    const CartFeedbackEngine = {
        init() {
            // Listen to any custom cart event or form submission
            window.addEventListener('cart:updated', (e) => {
                this.triggerCartPop(e.detail?.count);
            });
        },

        triggerCartPop(newCount = null) {
            const cartIcons = document.querySelectorAll('.bi-cart, .bi-cart3, .bi-bag-heart, .cart-dropdown a');
            cartIcons.forEach(icon => {
                const target = icon.closest('.nav-item') || icon;
                target.classList.remove('cart-icon-pop');
                void target.offsetWidth; // Force reflow
                target.classList.add('cart-icon-pop');
                setTimeout(() => target.classList.remove('cart-icon-pop'), 650);
            });

            // Update badge with number roll
            const badge = document.querySelector('.cart-count-badge') || document.querySelector('.cart-dropdown .badge');
            if (badge) {
                badge.classList.remove('badge-count-bounce');
                void badge.offsetWidth;
                badge.classList.add('badge-count-bounce');

                if (newCount !== null && typeof newCount === 'number') {
                    this.animateCount(badge, parseInt(badge.textContent || '0', 10), newCount);
                }

                setTimeout(() => badge.classList.remove('badge-count-bounce'), 500);
            }
        },

        animateCount(element, start, end, duration = 400) {
            if (isNaN(start)) start = 0;
            const startTime = performance.now();

            const step = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const current = Math.round(start + (end - start) * progress);
                element.textContent = current;

                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    element.textContent = end;
                }
            };

            requestAnimationFrame(step);
        }
    };

    // =========================================================================
    // 5. WISHLIST HEART POP WITH MINI HEART PARTICLES
    // =========================================================================
    const WishlistEngine = {
        init() {
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.wishlist-toggle-btn, form[action*="wishlist"] button, .btn-wishlist');
                if (!btn) return;

                const heartIcon = btn.querySelector('.bi-heart, .bi-heart-fill');
                if (heartIcon) {
                    heartIcon.classList.remove('heart-pop-active');
                    void heartIcon.offsetWidth;
                    heartIcon.classList.add('heart-pop-active');
                    setTimeout(() => heartIcon.classList.remove('heart-pop-active'), 650);
                }

                // Spawn flying heart particles
                this.spawnParticles(btn);
            });
        },

        spawnParticles(element) {
            const rect = element.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            const numParticles = 6;

            for (let i = 0; i < numParticles; i++) {
                const particle = document.createElement('span');
                particle.className = 'mini-heart-particle';
                particle.innerHTML = '❤';

                // Random angle and distance
                const angle = (i * (360 / numParticles) + Math.random() * 30 - 15) * (Math.PI / 180);
                const distance = 28 + Math.random() * 22;
                const tx = Math.cos(angle) * distance;
                const ty = Math.sin(angle) * distance;

                particle.style.left = `${centerX}px`;
                particle.style.top = `${centerY}px`;
                particle.style.setProperty('--tx', `${tx}px`);
                particle.style.setProperty('--ty', `${ty}px`);

                document.body.appendChild(particle);

                setTimeout(() => {
                    particle.remove();
                }, 650);
            }
        }
    };

    // =========================================================================
    // 6. SLIDING TAB INDICATOR & SMOOTH TAB CONTENT SWITCHING
    // =========================================================================
    const TabSlidingEngine = {
        init() {
            const tabContainers = document.querySelectorAll(
                '.nav-tabs, .nav-pills, .customer-orders-page .row.g-3, .account-hub-grid'
            );

            // Handle Bootstrap tabs
            document.querySelectorAll('[data-bs-toggle="tab"], [data-bs-toggle="pill"]').forEach(tabTrigger => {
                tabTrigger.addEventListener('show.bs.tab', (e) => {
                    const targetSelector = e.target.getAttribute('data-bs-target') || e.target.getAttribute('href');
                    const targetPane = targetSelector ? document.querySelector(targetSelector) : null;
                    if (targetPane) {
                        targetPane.classList.add('tab-pane-enter');
                        setTimeout(() => targetPane.classList.remove('tab-pane-enter'), 300);
                    }
                });
            });

            // Smooth sliding indicator for standard tab headers
            document.querySelectorAll('.nav-tabs, .nav-pills').forEach(nav => {
                this.attachSlider(nav);
            });
        },

        attachSlider(navElement) {
            if (navElement.querySelector('.tab-slider-indicator')) return;

            navElement.style.position = 'relative';
            const slider = document.createElement('div');
            slider.className = 'tab-slider-indicator';
            navElement.appendChild(slider);

            const updateSlider = () => {
                const activeTab = navElement.querySelector('.nav-link.active') || navElement.querySelector('.active');
                if (activeTab) {
                    const left = activeTab.offsetLeft;
                    const width = activeTab.offsetWidth;
                    slider.style.transform = `translateX(${left}px)`;
                    slider.style.width = `${width}px`;
                    slider.style.opacity = '1';
                } else {
                    slider.style.opacity = '0';
                }
            };

            updateSlider();
            window.addEventListener('resize', updateSlider, { passive: true });

            navElement.querySelectorAll('.nav-link, a').forEach(item => {
                item.addEventListener('click', () => {
                    setTimeout(updateSlider, 50);
                });
            });
        }
    };

    // =========================================================================
    // 7. SPRING MODAL TRANSITIONS (ENTER & EXIT)
    // =========================================================================
    const ModalTransitionEngine = {
        init() {
            // Listen to Bootstrap modal hide to trigger smooth exit scale-down
            document.addEventListener('hide.bs.modal', (e) => {
                const modal = e.target;
                if (!modal) return;

                modal.classList.add('modal-closing');
            });

            document.addEventListener('hidden.bs.modal', (e) => {
                const modal = e.target;
                if (!modal) return;

                modal.classList.remove('modal-closing');
            });
        }
    };

    // =========================================================================
    // 8. ANIMATED SVG CHECKMARK DRAWING FOR SUCCESS ACTIONS
    // =========================================================================
    const SuccessCheckmarkEngine = {
        show({ title = 'Thành công!', message = 'Thao tác của bạn đã được thực hiện.', duration = 2000, callback = null } = {}) {
            const overlay = document.createElement('div');
            overlay.className = 'success-checkmark-overlay';
            overlay.innerHTML = `
                <div class="success-checkmark-card">
                    <svg class="animated-checkmark-svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="24" />
                        <path class="checkmark-check" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                    </svg>
                    <h4 class="fw-bold mb-2">${title}</h4>
                    <p class="text-muted small mb-0">${message}</p>
                </div>
            `;

            document.body.appendChild(overlay);

            setTimeout(() => {
                overlay.style.opacity = '0';
                overlay.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    overlay.remove();
                    if (typeof callback === 'function') callback();
                }, 320);
            }, duration);
        }
    };

    // =========================================================================
    // GLOBAL EXPORTS
    // =========================================================================
    window.Interactions = {
        setButtonLoading: (btn, text) => ButtonActionEngine.setButtonLoading(btn, text),
        setButtonSuccess: (btn, msg, dur, cb) => ButtonActionEngine.setButtonSuccess(btn, msg, dur, cb),
        setButtonError: (btn, msg, dur) => ButtonActionEngine.setButtonError(btn, msg, dur),
        resetButton: (btn) => ButtonActionEngine.resetButton(btn),
        triggerCartFeedback: (count) => CartFeedbackEngine.triggerCartPop(count),
        showSuccessCheckmark: (options) => SuccessCheckmarkEngine.show(options)
    };

    // Shorthands for easy global use
    window.setButtonLoading = window.Interactions.setButtonLoading;
    window.setButtonSuccess = window.Interactions.setButtonSuccess;
    window.setButtonError = window.Interactions.setButtonError;
    window.resetButton = window.Interactions.resetButton;
    window.triggerCartFeedback = window.Interactions.triggerCartFeedback;
    window.showSuccessCheckmark = window.Interactions.showSuccessCheckmark;

    // Initialize all engines on DOM load
    PageTransitionEngine.init();
    RippleEngine.init();
    ButtonActionEngine.init();
    CartFeedbackEngine.init();
    WishlistEngine.init();
    TabSlidingEngine.init();
    ModalTransitionEngine.init();

})(window, document);
