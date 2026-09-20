document.addEventListener('DOMContentLoaded', function () {
    const root = document.querySelector('[data-product-advisor]');
    if (!root) return;
    const panel = root.querySelector('[data-advisor-panel]');
    const messages = root.querySelector('[data-advisor-messages]');
    const form = root.querySelector('[data-advisor-form]');
    const addMessage = (html, className) => {
        const item = document.createElement('div');
        item.className = 'advisor-message ' + className;
        item.innerHTML = html;
        messages.appendChild(item);
        messages.scrollTop = messages.scrollHeight;
    };
    root.querySelector('[data-advisor-toggle]').addEventListener('click', () => panel.classList.toggle('d-none'));
    root.querySelector('[data-advisor-close]').addEventListener('click', () => panel.classList.add('d-none'));
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const data = new FormData(form);
        const need = data.get('need');
        addMessage('Mình cần tư vấn: ' + need, 'advisor-user');
        try {
            const response = await fetch('/product-advisor/recommend', {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'},
                body: data
            });
            if (!response.ok) throw new Error('advisor request failed');
            const result = await response.json();
            const products = result.products.map(product => `<a class="advisor-product" href="${product.url}">${product.image ? `<img src="${product.image}" alt="">` : ''}<span><strong>${product.name}</strong><br><small>${product.flash_sale ? 'FLASH SALE · ' : ''}${product.price}</small></span></a>`).join('');
            addMessage(`${result.message}<div class="advisor-products">${products}</div>`, 'advisor-bot');
        } catch (error) {
            addMessage('Mình chưa thể tải gợi ý lúc này. Bạn thử lại sau nhé.', 'advisor-bot');
        }
    });
});
