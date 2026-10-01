/* components/recently-viewed.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function() {
    const STORAGE_KEY = 'beatycare-recently-viewed';
    const currentSlug = document.getElementById('recently-viewed-section')?.dataset.currentSlug || null;
    const section = document.getElementById('recently-viewed-section');
    const track = document.getElementById('recently-viewed-track');

    function getRecentlyViewed() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        } catch(e) {
            return [];
        }
    }

    function renderRecentlyViewed() {
        if (!track || !section) return;
        const list = getRecentlyViewed().filter(item => !currentSlug || item.slug !== currentSlug);

        if (list.length === 0) {
            section.classList.add('d-none');
            return;
        }

        section.classList.remove('d-none');
        track.innerHTML = '';

        list.slice(0, 8).forEach(item => {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-3 col-lg-2 flex-shrink-0';
            col.style.scrollSnapAlign = 'start';
            col.innerHTML = `
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden text-center p-2">
                    <a href="/products/${encodeURIComponent(item.slug)}" class="text-decoration-none text-dark d-block">
                        <img src="${item.image || '/images/placeholder.svg'}" class="w-100 object-fit-cover rounded-2 mb-2" style="aspect-ratio: 1/1;" alt="${item.name || 'Sản phẩm'}" onerror="this.onerror=null;this.src='/images/placeholder.svg';">
                        <div class="small fw-bold text-truncate mb-1" title="${item.name || ''}">${item.name || ''}</div>
                        <div class="text-danger small fw-semibold">${item.price || ''}</div>
                    </a>
                </div>
            `;
            track.appendChild(col);
        });
    }

    window.clearRecentlyViewed = function() {
        localStorage.removeItem(STORAGE_KEY);
        if (section) section.classList.add('d-none');
    };

    renderRecentlyViewed();
});
