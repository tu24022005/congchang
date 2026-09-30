document.addEventListener('DOMContentLoaded', function () {
    // 1. Top Progress Bar (Loading Simulation)
    const progressBar = document.getElementById('top-progress-bar');
    if (progressBar) {
        // Simple simulation of page load
        let width = 0;
        let interval = setInterval(() => {
            if (width >= 90) {
                clearInterval(interval);
            } else {
                width += Math.random() * 10;
                progressBar.style.width = width + '%';
            }
        }, 100);

        window.addEventListener('load', () => {
            clearInterval(interval);
            progressBar.style.width = '100%';
            setTimeout(() => {
                progressBar.style.opacity = '0';
            }, 500);
        });
    }

    // 2. Back to Top Button
    const backToTopBtn = document.getElementById('back-to-top');
    if (backToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });

        backToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // 3. Scroll Reveal for all .reveal-up elements
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -50px 0px',
        threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries, observer) => {
        let delay = 0;
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('in-view');
                }, delay);
                delay += 80; // Stagger effect
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.reveal-up').forEach(el => {
        observer.observe(el);
    });

    // 4. Global Custom Toast Notification logic (can be called globally)
    window.showToast = function(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;

        const toast = document.createElement('div');
        toast.className = `custom-toast toast-${type}`;
        
        let icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
        
        toast.innerHTML = `
            <div class="toast-content">
                <i class="bi ${icon}"></i>
                <span>${message}</span>
            </div>
            <button class="toast-close"><i class="bi bi-x"></i></button>
        `;

        toastContainer.appendChild(toast);
        
        // Trigger reflow for slide-in animation
        setTimeout(() => toast.classList.add('show'), 10);

        // Auto remove
        let autoRemove = setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);

        // Click to close
        toast.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(autoRemove);
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });
    };

    // 5. Fly to Cart Animation
    window.flyToCart = function(imgElement, cartIconSelector = '.bi-cart') {
        if (!imgElement) return;
        const cartIcon = document.querySelector(cartIconSelector);
        if (!cartIcon) return;

        const imgClone = imgElement.cloneNode(true);
        const rect = imgElement.getBoundingClientRect();
        const cartRect = cartIcon.getBoundingClientRect();

        imgClone.className = 'flying-img';
        imgClone.style.top = rect.top + 'px';
        imgClone.style.left = rect.left + 'px';
        document.body.appendChild(imgClone);

        // Force reflow
        void imgClone.offsetWidth;

        imgClone.style.top = cartRect.top + 'px';
        imgClone.style.left = cartRect.left + 'px';
        imgClone.style.transform = 'scale(0.2)';
        imgClone.style.opacity = '0';

        setTimeout(() => {
            imgClone.remove();
            cartIcon.parentElement.classList.add('pop');
            setTimeout(() => {
                cartIcon.parentElement.classList.remove('pop');
            }, 300);
        }, 800);
    };

    // 7. Count-Up Animation
    const countUpElements = document.querySelectorAll('.count-up');
    if (countUpElements.length > 0) {
        const countObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-target'), 10);
                    if (isNaN(target)) return;
                    
                    const duration = 2000;
                    const frameDuration = 1000 / 60;
                    const totalFrames = Math.round(duration / frameDuration);
                    let frame = 0;
                    
                    const count = () => {
                        frame++;
                        const progress = frame / totalFrames;
                        const current = Math.round(target * progress);
                        el.innerText = current;
                        
                        if (frame < totalFrames) {
                            requestAnimationFrame(count);
                        } else {
                            el.innerText = target;
                        }
                    };
                    
                    count();
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.1 });
        
        countUpElements.forEach(el => countObserver.observe(el));
    }
});
