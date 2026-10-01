/* Hành vi dùng chung cho layout người dùng. */

/* Khởi tạo theme và trạng thái preloader. */
if (localStorage.getItem('beatycare-theme') === 'dark') {
            document.documentElement.classList.add('dark-mode');
        }
        if (sessionStorage.getItem('beatycare-preloader-shown') === '1') {
            document.documentElement.classList.add('preloader-skipped');
        }

/* Hiển thị nội dung modal chính sách hỗ trợ. */
document.getElementById('supportPolicyModal')?.addEventListener('show.bs.modal', function (event) {
            const policy = event.relatedTarget?.dataset.policy;
            const title = document.getElementById('supportPolicyTitle');
            const content = document.getElementById('supportPolicyContent');
            if (policy === 'returns') {
                title.textContent = 'Đổi trả & hoàn tiền';
                content.innerHTML = '<p class="text-muted">Aloha Beauty hỗ trợ đổi trả trong 7 ngày nếu sản phẩm bị lỗi, giao sai hoặc hư hỏng khi nhận.</p><ul class="text-muted ps-3"><li>Giữ nguyên sản phẩm, hộp và phụ kiện.</li><li>Gửi ảnh/video tình trạng sản phẩm qua chat hỗ trợ.</li><li>Thời gian xử lý: 2-3 ngày làm việc sau khi nhận đủ thông tin.</li><li>Hoàn tiền về phương thức thanh toán ban đầu sau khi xác nhận.</li></ul>';
            } else {
                title.textContent = 'Giao hàng & thanh toán';
                content.innerHTML = '<p class="text-muted">Aloha Beauty giao hàng toàn quốc và đóng gói cẩn thận để sản phẩm đến bạn an toàn.</p><ul class="text-muted ps-3"><li>Thời gian dự kiến: 2-5 ngày làm việc.</li><li>Thanh toán COD khi nhận hàng.</li><li>Thanh toán chuyển khoản nhanh qua PayOS.</li><li>Địa chỉ giao hàng có thể được ghim chính xác trên bản đồ lúc đặt hàng.</li></ul>';
            }
        });

/* Tìm kiếm sản phẩm trực tiếp trên thanh điều hướng. */
document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('live-search-input');
        const suggestionsBox = document.getElementById('search-suggestions');

        if (!searchInput || !suggestionsBox) return;

        let debounceTimer = null;
        let abortController = null;
        let selectedIndex = -1;

        // Hàm escape HTML an toàn chống XSS
        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Lấy danh sách tất cả các item có thể tương tác bằng bàn phím
        function getFocusableItems() {
            return Array.from(suggestionsBox.querySelectorAll('.search-nav-item'));
        }

        function setAriaExpanded(expanded) {
            searchInput.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            if (!expanded) {
                searchInput.removeAttribute('aria-activedescendant');
            }
        }

        function hideSuggestions() {
            if (abortController) {
                abortController.abort();
                abortController = null;
            }
            clearTimeout(debounceTimer);
            suggestionsBox.classList.add('d-none');
            setAriaExpanded(false);
            selectedIndex = -1;
            removeActiveState();
        }

        function removeActiveState() {
            const items = getFocusableItems();
            items.forEach(el => el.classList.remove('active'));
        }

        function updateActiveItem(index) {
            const items = getFocusableItems();
            if (items.length === 0) return;

            items.forEach((item, i) => {
                if (i === index) {
                    item.classList.add('active');
                    item.scrollIntoView({ block: 'nearest' });
                    if (item.id) {
                        searchInput.setAttribute('aria-activedescendant', item.id);
                    }
                } else {
                    item.classList.remove('active');
                }
            });
        }

        // Đóng gợi ý khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                hideSuggestions();
            }
        });

        // Mở lại gợi ý nếu input có sẵn nội dung và focus vào
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 1 && suggestionsBox.innerHTML.trim() !== '') {
                suggestionsBox.classList.remove('d-none');
                setAriaExpanded(true);
            }
        });

        // Bắt sự kiện phím điều hướng (ArrowDown, ArrowUp, Enter, Escape)
        searchInput.addEventListener('keydown', function(e) {
            const items = getFocusableItems();
            const isOpen = !suggestionsBox.classList.contains('d-none') && items.length > 0;

            if (e.key === 'Escape') {
                if (!suggestionsBox.classList.contains('d-none')) {
                    e.preventDefault();
                    hideSuggestions();
                }
                return;
            }

            if (!isOpen) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) >= items.length ? 0 : (selectedIndex + 1);
                updateActiveItem(selectedIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1) < 0 ? (items.length - 1) : (selectedIndex - 1);
                updateActiveItem(selectedIndex);
            } else if (e.key === 'Enter') {
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    e.preventDefault();
                    items[selectedIndex].click();
                }
            }
        });

        // Xử lý tìm kiếm với Debounce 250ms & AbortController
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 1) {
                hideSuggestions();
                suggestionsBox.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                fetch(`/search-suggestions?query=${encodeURIComponent(query)}`, {
                    signal: abortController.signal,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    selectedIndex = -1;
                    const hasProducts = Array.isArray(data.products) && data.products.length > 0;
                    const hasCategories = Array.isArray(data.categories) && data.categories.length > 0;
                    const hasKeywords = Array.isArray(data.keywords) && data.keywords.length > 0;

                    if (hasProducts || hasCategories || hasKeywords) {
                        let html = '';
                        let itemIndex = 0;

                        if (hasProducts) {
                            html += '<div class="search-suggestion-group" role="group" aria-label="Sản phẩm"><div class="search-suggestion-title">Sản phẩm</div><ul class="list-unstyled mb-0">';
                            data.products.forEach(item => {
                                const itemId = `search-item-${itemIndex++}`;
                                const safeName = escapeHtml(item.name);
                                const safePrice = escapeHtml(item.formatted_price);
                                const safeUrl = escapeHtml(item.detail_url);
                                const imgHtml = item.image_url 
                                    ? `<img src="${escapeHtml(item.image_url)}" class="me-3 rounded search-result-image" alt="${safeName}">`
                                    : `<div class="d-flex align-items-center justify-content-center bg-light rounded me-3 search-result-image"><i class="bi bi-box text-muted"></i></div>`;
                                
                                html += `
                                <li>
                                    <a href="${safeUrl}" id="${itemId}" role="option" class="d-flex align-items-center px-3 py-2 text-decoration-none text-dark search-item-hover search-nav-item">
                                        ${imgHtml}
                                        <div>
                                            <div class="fw-bold fs-6 text-truncate search-result-name">${safeName}</div>
                                            <div class="text-danger small fw-semibold">${safePrice}</div>
                                        </div>
                                    </a>
                                </li>`;
                            });
                            html += '</ul></div>';
                        }

                        if (hasCategories) {
                            html += '<div class="search-suggestion-group" role="group" aria-label="Danh mục"><div class="search-suggestion-title">Danh mục</div><ul class="list-unstyled mb-0">';
                            data.categories.forEach(item => {
                                const itemId = `search-item-${itemIndex++}`;
                                const safeName = escapeHtml(item.name);
                                const safeUrl = escapeHtml(item.url);
                                const count = Number(item.count) || 0;
                                html += `<li><a href="${safeUrl}" id="${itemId}" role="option" class="search-related-link search-nav-item"><i class="bi bi-grid-3x3-gap me-2"></i><span>${safeName}</span><small>${count} sản phẩm</small></a></li>`;
                            });
                            html += '</ul></div>';
                        }

                        if (hasKeywords) {
                            html += '<div class="search-suggestion-group" role="group" aria-label="Từ khóa liên quan"><div class="search-suggestion-title">Từ khóa liên quan</div><div class="search-related-keywords">';
                            data.keywords.forEach(keyword => {
                                const itemId = `search-item-${itemIndex++}`;
                                const safeKeyword = escapeHtml(keyword);
                                const searchParam = encodeURIComponent(keyword);
                                html += `<a href="/products?search=${searchParam}" id="${itemId}" role="option" class="search-keyword-chip search-nav-item">${safeKeyword}</a>`;
                            });
                            html += '</div></div>';
                        }

                        suggestionsBox.innerHTML = html;
                        suggestionsBox.classList.remove('d-none');
                        setAriaExpanded(true);

                        // Đồng bộ chuột hover với selectedIndex
                        getFocusableItems().forEach((el, idx) => {
                            el.addEventListener('mouseenter', () => {
                                selectedIndex = idx;
                                updateActiveItem(selectedIndex);
                            });
                        });
                    } else {
                        suggestionsBox.innerHTML = '<div class="p-3 text-center text-muted small" role="status"><i class="bi bi-emoji-frown me-1"></i> Không tìm thấy sản phẩm</div>';
                        suggestionsBox.classList.remove('d-none');
                        setAriaExpanded(true);
                    }
                })
                .catch(error => {
                    if (error.name === 'AbortError') return;
                    suggestionsBox.innerHTML = '<div class="p-3 text-center text-danger small" role="alert"><i class="bi bi-exclamation-circle me-1"></i> Không thể tìm kiếm, thử lại sau</div>';
                    suggestionsBox.classList.remove('d-none');
                    setAriaExpanded(true);
                });
            }, 250);
        });
    });

/* Nh?m ch?c n?ng 4 */
(function () {
                        const badge = document.getElementById('storefront-chat-badge');
                        const refreshChatBadge = () => fetch('/chat/users').then(response => response.json()).then(users => {
                            const total = users.reduce((sum, user) => sum + Number(user.unread_messages_count || 0), 0);
                            badge.textContent = total > 99 ? '99+' : total;
                            badge.classList.toggle('d-none', total === 0);
                        }).catch(() => {});
                        refreshChatBadge();
                        setInterval(refreshChatBadge, 15000);
                    })();

/* Nh?m ch?c n?ng 5 */
document.addEventListener("DOMContentLoaded", function() {
                const chatBtn = document.getElementById('chat-widget-button');
                const chatWin = document.getElementById('chat-widget-window');
                const chatBadge = document.getElementById('chat-badge');
                const tingSound = document.getElementById('ting-sound');
                
                let currentUserId = (document.body.dataset.userId || 'null');
                let unreadCount = 0;
                const customerAttachment = document.getElementById('customer-attachment');
                const customerAttachmentPreview = document.getElementById('customer-attachment-preview');

                chatBtn.addEventListener('click', () => {
                    chatWin.classList.toggle('d-none');
                    if(!chatWin.classList.contains('d-none')) { 
                        loadMessages(); 
                        updateSupportPresence();
                        chatBtn.style.transform = 'scale(0)'; 
                        unreadCount = 0;
                        chatBadge.classList.add('d-none');
                        chatBadge.innerText = '0';
                    }
                });
                
                document.getElementById('close-chat').addEventListener('click', () => {
                    chatWin.classList.add('d-none'); chatBtn.style.transform = 'scale(1)';
                });

                function loadMessages() {
                    fetch('/chat/messages').then(res => res.json()).then(data => {
                        document.getElementById('chat-messages').innerHTML = '';
                        if (data.length === 0) appendMessage({ is_admin: 1, message: 'Xin chào! Aloha Beauty có thể hỗ trợ bạn điều gì hôm nay?' });
                        data.forEach(msg => appendMessage(msg));
                        scrollToBottom();
                    });
                }

                function updateSupportPresence() {
                    fetch('/chat/presence').then(res => res.json()).then(status => {
                        const target = document.getElementById('support-presence');
                        if (!target) return;
                        target.innerHTML = status.online ? '<i class="bi bi-circle-fill text-success me-1"></i>Đang online' : (status.last_seen_minutes === null ? 'Chưa hoạt động' : 'Hoạt động ' + status.last_seen_minutes + ' phút trước');
                    });
                }

                function appendMessage(msg) {
                    let isMine = msg.is_admin == 0;
                    let attachment = msg.attachment_url ? `<img src="${msg.attachment_url}" alt="Ảnh đính kèm" style="max-width:180px;max-height:120px;border-radius:10px;display:block;margin-top:6px">` : '';
                    let html = `<div class="w-100 mb-2 chat-message-row">
                                    ${!isMine ? '<small class="d-block text-muted mb-1 chat-sender-label">Admin Shop</small>' : ''}
                                    <div class="msg-bubble ${isMine ? 'msg-mine' : 'msg-other'}">${msg.message || ''}${attachment}</div>
                                </div>`;
                    document.getElementById('chat-messages').insertAdjacentHTML('beforeend', html);
                    scrollToBottom();
                }

                function scrollToBottom() { let box = document.getElementById('chat-messages'); box.scrollTop = box.scrollHeight; }

                function sendMessage() {
                    let text = document.getElementById('btn-input').value.trim();
                    if(!text && !customerAttachment.files.length) return;
                    document.getElementById('btn-input').value = '';
                    const formData = new FormData();
                    formData.append('message', text);
                    if (customerAttachment.files[0]) formData.append('attachment', customerAttachment.files[0]);
                    fetch('/chat/message', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                        body: formData
                    }).then(response => response.json()).then(sent => { appendMessage({...sent.message, is_admin: 0, message: text}); });
                    customerAttachment.value = '';
                    customerAttachmentPreview.innerHTML = '';
                }

                customerAttachment.addEventListener('change', () => {
                    const file = customerAttachment.files[0];
                    customerAttachmentPreview.textContent = file ? 'Đã chọn: ' + file.name : '';
                });
                document.querySelectorAll('.chat-topic').forEach(button => button.addEventListener('click', () => {
                    document.getElementById('btn-input').value = button.dataset.topic;
                    document.getElementById('btn-input').focus();
                }));
                document.getElementById('customer-emoji').addEventListener('click', () => {
                    document.getElementById('btn-input').value += ' 😊';
                    document.getElementById('btn-input').focus();
                });

                document.getElementById('btn-chat').addEventListener('click', sendMessage);
                document.getElementById('btn-input').addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });

                var pusher = new Pusher('c7b756312af017cea0f9', { cluster: 'ap1' });
                pusher.subscribe('chat-channel').bind('message.sent', function(data) {
                    if(data.message.user_id == currentUserId && data.message.is_admin == 1) {
                        tingSound.currentTime = 0;
                        tingSound.play().catch(e => console.log('Âm thanh chờ tương tác người dùng: ', e));
                        if(!chatWin.classList.contains('d-none')) {
                            appendMessage(data.message);
                        } else {
                            appendMessage(data.message); 
                            
                            unreadCount++;
                            chatBadge.innerText = unreadCount > 99 ? '99+' : unreadCount;
                            chatBadge.classList.remove('d-none');
                            
                            chatBtn.classList.add('animate__animated', 'animate__tada');
                            setTimeout(() => chatBtn.classList.remove('animate__animated', 'animate__tada'), 1000);
                            
                            if ('Notification' in window && Notification.permission === 'granted') {
                                new Notification('Aloha Beauty có tin nhắn mới', {body: data.message.message});
                            }
                        }
                    }
                });
                const heartbeat = () => fetch('/chat/heartbeat', {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''}});
                heartbeat();
                setInterval(heartbeat, 60000);
                setInterval(updateSupportPresence, 60000);
            });

/* Nh?m ch?c n?ng 6 */
(function () {
                const sendPresence = () => fetch('/chat/heartbeat', {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''}}).catch(() => {});
                sendPresence();
                setInterval(sendPresence, 60000);
            })();

/* Nh?m ch?c n?ng 7 */
document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('input[type="password"]').forEach(function (input) {
                if (input.parentElement.querySelector('.password-toggle')) return;

                const container = input.parentElement;
                container.classList.add('password-field');

                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'password-toggle';
                button.setAttribute('aria-label', 'Hiện mật khẩu');
                button.innerHTML = '<i class="bi bi-eye"></i>';
                const positionToggle = function () {
                    button.style.top = (input.offsetTop + (input.offsetHeight / 2)) + 'px';
                };
                positionToggle();
                button.addEventListener('click', function () {
                    const visible = input.type === 'text';
                    input.type = visible ? 'password' : 'text';
                    button.setAttribute('aria-label', visible ? 'Hiện mật khẩu' : 'Ẩn mật khẩu');
                    button.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
                });
                container.appendChild(button);
                window.addEventListener('resize', positionToggle);
            });
        });

/* Nh?m ch?c n?ng 8 */
document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('global-theme-toggle');
            const body = document.body;
            
            // Khôi phục trạng thái
            const savedTheme = localStorage.getItem('beatycare-theme');
            if (savedTheme === 'dark') {
                body.classList.add('dark-mode');
                document.documentElement.classList.add('dark-mode');
                if (toggleBtn) {
                    toggleBtn.innerHTML = '🌙';
                    toggleBtn.style.background = 'rgba(30, 30, 30, 0.9)';
                    toggleBtn.style.color = 'white';
                }
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    body.classList.toggle('dark-mode');
                    document.documentElement.classList.toggle('dark-mode');
                    const isDark = body.classList.contains('dark-mode');
                    toggleBtn.innerHTML = isDark ? '🌙' : '☀️';
                    localStorage.setItem('beatycare-theme', isDark ? 'dark' : 'light');
                    
                    if (isDark) {
                        toggleBtn.style.background = 'rgba(30, 30, 30, 0.9)';
                        toggleBtn.style.color = 'white';
                    } else {
                        toggleBtn.style.background = 'rgba(255, 255, 255, 0.8)';
                        toggleBtn.style.color = '#333';
                    }
                });
            }
        });

        // XỬ LÝ ĐĂNG KÝ BẢN TIN FOOTER (PROMPT 3.6)
        async function handleNewsletterSubscribe(event) {
            event.preventDefault();
            const form = event.target;
            const btn = document.getElementById('btn-newsletter-submit');
            const emailInput = document.getElementById('newsletter-email');
            const email = emailInput ? emailInput.value.trim() : '';
            const hp = form.querySelector('input[name="hp_email"]')?.value;

            if (hp) return;
            if (!email) return;

            btn.disabled = true;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const res = await fetch(document.body.dataset.newsletterUrl || '/newsletter/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, hp_email: hp })
                });
                const data = await res.json();
                if (window.showToast) {
                    window.showToast(data.message, res.ok ? 'success' : 'warning');
                } else {
                    alert(data.message);
                }
                if (res.ok) form.reset();
            } catch(e) {
                console.error(e);
                if (window.showToast) window.showToast('Không thể kết nối máy chủ.', 'danger');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
