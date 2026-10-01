/* admin/chat/index.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('conversation-list');
    const stream = document.getElementById('message-stream');
    const head = document.getElementById('conversation-head');
    const search = document.getElementById('conversation-search');
    const input = document.getElementById('message-input');
    const sendButton = document.getElementById('send-message');
    const count = document.getElementById('conversation-count');
    const quickReplies = document.getElementById('quick-replies');
    const attachmentInput = document.getElementById('attachment-input');
    const attachmentPreview = document.getElementById('attachment-preview');
    const emojiButton = document.getElementById('emoji-button');
    const ding = document.getElementById('inbox-ding');
    const toast = document.getElementById('inbox-toast');
    const toastText = document.getElementById('inbox-toast-text');
    let users = [];
    let activeUserId = null;

    const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char]));
    const formatTime = value => value ? new Date(value).toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit' }) : '';

    function renderUsers() {
        const term = search.value.trim().toLowerCase();
        const filtered = users.filter(user => user.name.toLowerCase().includes(term));
        count.textContent = users.length;
        if (!filtered.length) {
            list.innerHTML = '<div class="empty-inbox px-3"><div><i class="bi bi-person-x fs-2 d-block mb-2"></i>Chưa có khách phù hợp</div></div>';
            return;
        }
        list.innerHTML = filtered.map(user => {
            const last = user.messages?.[0];
            const unread = Number(user.unread_messages_count || 0);
            const presence = user.is_online ? '<span class="presence-line online"><span class="online-dot"></span>Đang online</span>' : `<span class="presence-line">${user.last_seen_minutes === null ? 'Chưa hoạt động' : 'Hoạt động ' + user.last_seen_minutes + ' phút trước'}</span>`;
            return `<button type="button" class="conversation-item ${activeUserId === user.id ? 'active' : ''}" data-user-id="${user.id}">
                <span class="conversation-avatar">${escapeHtml(user.name.charAt(0).toUpperCase())}</span>
                <span class="min-w-0 flex-grow-1"><span class="conversation-name d-block">${escapeHtml(user.name)}</span>${presence}<span class="conversation-preview d-block">${escapeHtml(last?.message || 'Chưa có nội dung')}</span></span>
                ${unread ? `<span class="badge rounded-pill bg-danger">${unread}</span>` : ''}
            </button>`;
        }).join('');
        list.querySelectorAll('[data-user-id]').forEach(button => button.addEventListener('click', () => openConversation(Number(button.dataset.userId))));
    }

    function openConversation(userId) {
        const user = users.find(item => item.id === userId);
        if (!user) return;
        activeUserId = userId;
        head.innerHTML = `<div><h2 class="h5 fw-bold mb-1">${escapeHtml(user.name)}</h2><small id="active-presence" class="text-muted">Đang kiểm tra trạng thái...</small></div><span class="badge bg-light text-dark">#${user.id}</span>`;
        input.disabled = false;
        sendButton.disabled = false;
        attachmentInput.disabled = false;
        emojiButton.disabled = false;
        renderUsers();
        loadMessages(userId);
        updatePresence(userId);
    }

    function loadMessages(userId) {
        fetch(`/chat/messages?user_id=${userId}`).then(response => response.json()).then(messages => {
            stream.innerHTML = messages.length ? messages.map(renderMessage).join('') : '<div class="empty-inbox"><div>Chưa có tin nhắn trong hội thoại này.</div></div>';
            scrollBottom();
            const user = users.find(item => item.id === userId);
            if (user) user.unread_messages_count = 0;
            renderUsers();
        });
    }

    function renderMessage(message) {
        const mine = Number(message.is_admin) === 1;
        const attachment = message.attachment_url ? `<img src="${message.attachment_url}" alt="Ảnh đính kèm" class="chat-attachment-preview d-block mt-2">` : '';
        return `<div class="message-row ${mine ? 'mine' : ''}"><div><div class="message-bubble">${escapeHtml(message.message)}${attachment}</div><span class="message-time">${mine ? 'Bạn' : 'Khách hàng'} · ${formatTime(message.created_at)}</span></div></div>`;
    }

    function scrollBottom() { stream.scrollTop = stream.scrollHeight; }

    function notifyIncoming(userName, message) {
        toastText.textContent = `${userName}: ${message}`;
        toast.style.display = 'block';
        clearTimeout(window.inboxToastTimer);
        window.inboxToastTimer = setTimeout(() => toast.style.display = 'none', 4500);
        ding.currentTime = 0;
        ding.play().catch(() => {});
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Tin nhắn mới từ ' + userName, {body: message});
        }
    }

    function updatePresence(userId) {
        fetch(`/chat/presence?user_id=${userId}`).then(response => response.json()).then(status => {
            const target = document.getElementById('active-presence');
            if (!target || activeUserId !== userId) return;
            target.className = status.online ? 'presence-line online' : 'presence-line';
            target.innerHTML = status.online ? '<span class="online-dot"></span>Đang online' : (status.last_seen_minutes === null ? 'Chưa hoạt động' : `Hoạt động ${status.last_seen_minutes} phút trước`);
        });
    }

    document.getElementById('message-form').addEventListener('submit', function (event) {
        event.preventDefault();
        const message = input.value.trim();
        if (!activeUserId || (!message && !attachmentInput.files.length)) return;
        const formData = new FormData();
        formData.append('message', message);
        formData.append('receiver_id', activeUserId);
        if (attachmentInput.files[0]) formData.append('attachment', attachmentInput.files[0]);
        input.value = '';
        fetch('/chat/message', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''},
            body: formData
        }).then(response => response.json()).then(sent => {
            stream.insertAdjacentHTML('beforeend', renderMessage({...sent.message, is_admin: 1, message, created_at: new Date().toISOString()}));
            attachmentInput.value = '';
            attachmentPreview.innerHTML = '';
            scrollBottom();
        });
    });
    input.addEventListener('keydown', event => { if (event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); document.getElementById('message-form').requestSubmit(); } });
    search.addEventListener('input', renderUsers);
    quickReplies.querySelectorAll('[data-message]').forEach(button => button.addEventListener('click', () => {
        if (input.disabled) return;
        input.value = button.dataset.message;
        input.focus();
    }));
    attachmentInput.addEventListener('change', () => {
        const file = attachmentInput.files[0];
        attachmentPreview.innerHTML = file ? `<i class="bi bi-paperclip me-1"></i>${escapeHtml(file.name)}` : '';
    });
    emojiButton.addEventListener('click', () => { input.value += ' 😊'; input.focus(); });

    fetch('/chat/users').then(response => response.json()).then(data => { users = data; renderUsers(); });
    document.addEventListener('click', () => {
        ding.load();
        if ('Notification' in window && Notification.permission === 'default') Notification.requestPermission();
    }, {once: true});
    const heartbeat = () => fetch('/chat/heartbeat', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''}
    });
    heartbeat();
    setInterval(heartbeat, 60000);
    setInterval(() => { if (activeUserId) updatePresence(activeUserId); }, 60000);

    const pusher = new Pusher('c7b756312af017cea0f9', {cluster: 'ap1'});
    pusher.subscribe('chat-channel').bind('message.sent', function (data) {
        if (Number(data.message.is_admin) === 0) {
            const user = users.find(item => item.id === data.message.user_id);
            if (activeUserId === data.message.user_id) {
                stream.insertAdjacentHTML('beforeend', renderMessage(data.message));
                scrollBottom();
            } else if (user) {
                user.unread_messages_count = Number(user.unread_messages_count || 0) + 1;
                user.messages = [{message: data.message.message, created_at: data.message.created_at}];
            } else {
                fetch('/chat/users').then(response => response.json()).then(data => { users = data; renderUsers(); });
            }
            notifyIncoming(user?.name || 'Khách hàng', data.message.message);
            renderUsers();
        }
    });
});
