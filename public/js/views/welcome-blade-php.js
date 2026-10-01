/* welcome.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
                const badge = document.getElementById('home-chat-badge');
                const refreshChatBadge = () => fetch('/chat/users').then(response => response.json()).then(users => {
                    const total = users.reduce((sum, user) => sum + Number(user.unread_messages_count || 0), 0);
                    badge.textContent = total > 99 ? '99+' : total;
                    badge.classList.toggle('d-none', total === 0);
                }).catch(() => {});
                refreshChatBadge();
                setInterval(refreshChatBadge, 15000);
            });

/* welcome.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    const hero = document.getElementById('heroCarousel');
    if (hero && window.bootstrap?.Carousel) {
        const bsCarousel = bootstrap.Carousel.getOrCreateInstance(hero, {
            interval: 5000,
            ride: 'carousel',
            wrap: true,
            pause: false,
            touch: true
        });

        // Tự động cuộn liên tục (tôn trọng prefers-reduced-motion)
        if (!window.prefersReducedMotion?.matches) {
            bsCarousel.cycle();
        }

        // Quản lý dừng/phát video khi trượt carousel
        hero.addEventListener('slide.bs.carousel', function (e) {
            const prevSlide = hero.querySelector('.carousel-item.active');
            if (prevSlide) {
                const prevVid = prevSlide.querySelector('video');
                if (prevVid) prevVid.pause();
            }

            const nextSlide = e.relatedTarget;
            if (nextSlide) {
                const nextVid = nextSlide.querySelector('video');
                if (nextVid) {
                    nextVid.play().catch(() => {});
                }
            }
        });

        // Nút bật/tắt âm thanh và play/pause cho video trực tiếp
        document.querySelectorAll('.banner-sound-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const vid = document.getElementById(this.dataset.target);
                if (vid) {
                    vid.muted = !vid.muted;
                    this.innerHTML = vid.muted ? '<i class="bi bi-volume-mute-fill"></i>' : '<i class="bi bi-volume-up-fill"></i>';
                    this.setAttribute('title', vid.muted ? 'Bật âm thanh' : 'Tắt âm thanh');
                }
            });
        });

        document.querySelectorAll('.banner-vid-play-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const vid = document.getElementById(this.dataset.target);
                if (vid) {
                    if (vid.paused) {
                        vid.play().catch(() => {});
                        this.innerHTML = '<i class="bi bi-pause-fill"></i>';
                        this.setAttribute('title', 'Tạm dừng video');
                    } else {
                        vid.pause();
                        this.innerHTML = '<i class="bi bi-play-fill"></i>';
                        this.setAttribute('title', 'Phát video');
                    }
                }
            });
        });

        if (window.prefersReducedMotion) {
            window.prefersReducedMotion.addEventListener('change', (e) => {
                if (e.matches) {
                    bsCarousel.pause();
                    isHeroPaused = true;
                    if (heroToggle) heroToggle.innerHTML = '<i class="bi bi-play-fill"></i>';
                }
            });
        }
    }
});

/* welcome.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-countdown]').forEach(function (element) {
        const endAt = new Date(element.dataset.countdown).getTime();
        const label = element.querySelector('span');
        const updateCountdown = function () {
            const remaining = Math.max(0, endAt - Date.now());
            const totalSeconds = Math.floor(remaining / 1000);
            const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
            const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
            const seconds = String(totalSeconds % 60).padStart(2, '0');
            label.textContent = remaining > 0 ? `Còn: ${hours}:${minutes}:${seconds}` : 'Flash Sale đã kết thúc';
            if (remaining <= 0) element.classList.add('is-expired');
        };
        updateCountdown();
        window.setInterval(updateCountdown, 1000);
    });

    const track = document.getElementById('hot-products-track');
    if (track) {
        document.querySelectorAll('.hot-scroll-button').forEach(button => {
            button.addEventListener('click', function () {
                track.scrollBy({ left: Number(this.dataset.direction) * 300, behavior: 'smooth' });
            });
        });

        let hotScrollInterval = null;
        let isUserInteracting = false;

        const scrollStep = () => {
            if (isUserInteracting || document.hidden || window.prefersReducedMotion?.matches) return;
            const maxScroll = track.scrollWidth - track.clientWidth;
            if (track.scrollLeft >= maxScroll - 20) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                track.scrollBy({ left: track.clientWidth * 0.75, behavior: 'smooth' });
            }
        };

        const startAutoScroll = () => {
            if (hotScrollInterval || window.prefersReducedMotion?.matches) return;
            hotScrollInterval = setInterval(scrollStep, 5000);
        };

        const stopAutoScroll = () => {
            if (hotScrollInterval) {
                clearInterval(hotScrollInterval);
                hotScrollInterval = null;
            }
        };

        // Pause on mouse hover, focus, touch
        track.addEventListener('mouseenter', () => { isUserInteracting = true; stopAutoScroll(); });
        track.addEventListener('focusin', () => { isUserInteracting = true; stopAutoScroll(); });
        track.addEventListener('touchstart', () => { isUserInteracting = true; stopAutoScroll(); }, { passive: true });

        // Resume on mouse leave, focus out, touch end
        track.addEventListener('mouseleave', () => { isUserInteracting = false; startAutoScroll(); });
        track.addEventListener('focusout', () => { isUserInteracting = false; startAutoScroll(); });
        track.addEventListener('touchend', () => { isUserInteracting = false; startAutoScroll(); }, { passive: true });

        // Dừng khi tab bị ẩn
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoScroll();
            } else if (!isUserInteracting) {
                startAutoScroll();
            }
        });

        // Lắng nghe reduced-motion thay đổi
        if (window.prefersReducedMotion) {
            window.prefersReducedMotion.addEventListener('change', (e) => {
                if (e.matches) {
                    stopAutoScroll();
                } else if (!isUserInteracting) {
                    startAutoScroll();
                }
            });
        }

        // Dọn dẹp interval khi rời trang
        window.addEventListener('pagehide', stopAutoScroll);
        window.addEventListener('beforeunload', stopAutoScroll);

        startAutoScroll();
    }
});

