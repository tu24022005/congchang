/* components/compare-floating-dock.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function() {
    const STORAGE_KEY = 'beatycare-compare-ids';
    const dock = document.getElementById('compare-floating-dock');
    const thumbsWrap = document.getElementById('compare-items-thumbs');
    const countBadge = document.getElementById('compare-count-badge');
    const compareNowBtn = document.getElementById('btn-compare-now');

    function getCompareList() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        } catch(e) {
            return [];
        }
    }

    function saveCompareList(list) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
        updateCompareDock();
    }

    window.clearCompareList = function() {
        localStorage.removeItem(STORAGE_KEY);
        updateCompareDock();
        if (typeof window.showToast === 'function') {
            window.showToast('Đã xóa danh sách so sánh.', 'info');
        }
    };

    function updateCompareDock() {
        const list = getCompareList();
        
        // Update all toggle buttons on page
        document.querySelectorAll('.btn-compare-toggle').forEach(btn => {
            const id = Number(btn.dataset.compareId);
            if (list.some(item => item.id === id)) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        if (!dock) return;

        if (list.length === 0) {
            dock.classList.remove('is-visible');
            setTimeout(() => dock.classList.add('d-none'), 350);
            return;
        }

        dock.classList.remove('d-none');
        requestAnimationFrame(() => dock.classList.add('is-visible'));

        if (countBadge) countBadge.textContent = list.length;
        if (compareNowBtn) {
            const ids = list.map(i => i.id).join(',');
            compareNowBtn.href = `/compare?ids=${ids}`;
            if (list.length >= 2) {
                compareNowBtn.classList.remove('disabled');
            } else {
                compareNowBtn.classList.add('disabled');
            }
        }

        if (thumbsWrap) {
            thumbsWrap.innerHTML = '';
            list.forEach(item => {
                const wrap = document.createElement('div');
                wrap.className = 'position-relative';
                wrap.innerHTML = `
                    <img src="${item.image || '/images/placeholder.svg'}" class="rounded-circle border border-2 border-danger object-fit-cover shadow-sm" width="36" height="36" alt="${item.name || 'Sản phẩm'}" title="${item.name || ''}">
                    <button type="button" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark border border-light p-0 d-flex align-items-center justify-content-center" style="width: 16px; height: 16px; cursor: pointer;" aria-label="Xóa ${item.name || ''} khỏi so sánh">×</button>
                `;
                wrap.querySelector('button').onclick = () => {
                    const filtered = list.filter(i => i.id !== item.id);
                    saveCompareList(filtered);
                };
                thumbsWrap.appendChild(wrap);
            });
        }
    }

    // Toggle button click listener
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-compare-toggle');
        if (!btn) return;
        e.preventDefault();

        const id = Number(btn.dataset.compareId);
        const name = btn.dataset.compareName || 'Sản phẩm';
        const image = btn.dataset.compareImage || '';
        let list = getCompareList();

        const existingIndex = list.findIndex(i => i.id === id);
        if (existingIndex > -1) {
            // Remove
            list.splice(existingIndex, 1);
            saveCompareList(list);
            if (typeof window.showToast === 'function') {
                window.showToast(`Đã bỏ ${name} khỏi so sánh.`, 'info');
            }
        } else {
            // Add (limit 3)
            if (list.length >= 3) {
                if (typeof window.showToast === 'function') {
                    window.showToast('Bạn chỉ có thể so sánh tối đa 3 sản phẩm.', 'warning');
                }
                return;
            }
            list.push({ id, name, image });
            saveCompareList(list);
            if (typeof window.showToast === 'function') {
                window.showToast(`Đã thêm ${name} vào so sánh.`, 'success');
            }
        }
    });

    updateCompareDock();
});

