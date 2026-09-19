@extends('layouts.app')
@section('title', 'Chat khách hàng')

@section('content')
<style>
    .inbox-page { --ink: #1d2b3a; --muted: #718096; --line: #e7edf3; }
    .inbox-hero { background: linear-gradient(135deg, #183b56, #257a8c); color: #fff; border-radius: 18px; padding: 1.4rem 1.6rem; }
    .inbox-shell { display: grid; grid-template-columns: 310px minmax(0, 1fr); height: min(690px, calc(100vh - 245px)); min-height: 500px; overflow: hidden; background: #fff; border: 1px solid var(--line); border-radius: 18px; box-shadow: 0 16px 42px rgba(32,48,71,.1); }
    .inbox-sidebar { border-right: 1px solid var(--line); background: #fbfcfe; }
    .inbox-sidebar-head { padding: 1rem; border-bottom: 1px solid var(--line); }
    .conversation-list { min-height: 0; overflow-y: auto; }
    .conversation-item { display: flex; align-items: center; gap: .75rem; width: 100%; padding: .85rem 1rem; border: 0; border-bottom: 1px solid #f0f3f7; background: transparent; text-align: left; transition: background .2s; }
    .conversation-item:hover, .conversation-item.active { background: #eaf8fb; }
    .conversation-avatar { display: grid; flex: 0 0 38px; place-items: center; width: 38px; height: 38px; color: #087f9c; background: #d9f4f8; border-radius: 50%; font-weight: 800; }
    .conversation-name { color: var(--ink); font-weight: 800; font-size: .86rem; }
    .conversation-preview { max-width: 170px; overflow: hidden; color: var(--muted); font-size: .74rem; text-overflow: ellipsis; white-space: nowrap; }
    .presence-line { color: var(--muted); font-size: .7rem; }
    .presence-line.online { color: #15966a; }
    .inbox-main { display: flex; min-width: 0; min-height: 0; overflow: hidden; flex-direction: column; background: #fff; }
    .conversation-head { display: flex; align-items: center; justify-content: space-between; min-height: 76px; padding: 1rem 1.35rem; border-bottom: 1px solid var(--line); }
    .online-dot { display: inline-block; width: 8px; height: 8px; margin-right: .35rem; background: #19a974; border-radius: 50%; }
    .message-stream { flex: 1; min-height: 0; padding: 1.4rem; overflow-y: auto; overscroll-behavior: contain; background: linear-gradient(180deg, #f8fbfd, #fff); }
    .message-row { display: flex; margin-bottom: .8rem; }
    .message-row.mine { justify-content: flex-end; }
    .message-bubble { max-width: min(72%, 620px); padding: .72rem .9rem; color: #304052; background: #edf2f7; border-radius: 14px 14px 14px 4px; white-space: pre-wrap; word-break: break-word; }
    .message-row.mine .message-bubble { color: #fff; background: #1686a0; border-radius: 14px 14px 4px 14px; }
    .message-time { display: block; margin-top: .25rem; color: #93a0ae; font-size: .68rem; }
    .message-row.mine .message-time { color: #d9f4f8; text-align: right; }
    .composer { position: relative; z-index: 2; display: block; flex: 0 0 auto; min-height: 126px; padding: 1rem 1.35rem; border-top: 1px solid var(--line); background: #fff; }
    .composer textarea { resize: none; border-color: var(--line); border-radius: 12px; }
    .composer textarea:focus { border-color: #27a7bd; box-shadow: 0 0 0 .2rem rgba(39,167,189,.12); }
    .quick-replies { display: flex; gap: .45rem; overflow-x: auto; padding-bottom: .6rem; }
    .quick-reply { flex: 0 0 auto; color: #287184; border: 1px solid #cdebf0; border-radius: 999px; background: #f2fbfd; font-size: .72rem; padding: .35rem .65rem; }
    .quick-reply:hover { color: #fff; background: #1686a0; }
    .chat-attachment-preview { max-width: 180px; max-height: 100px; border-radius: 10px; object-fit: cover; }
    .inbox-toast { position: fixed; z-index: 1080; top: 1.25rem; right: 1.25rem; display: none; max-width: 320px; padding: .85rem 1rem; color: #fff; background: #183b56; border-radius: 12px; box-shadow: 0 12px 28px rgba(24,59,86,.24); }
    .empty-inbox { display: grid; height: 100%; min-height: 540px; place-items: center; color: var(--muted); text-align: center; }
    @media (max-width: 767.98px) { .inbox-shell { grid-template-columns: 1fr; height: auto; min-height: 0; } .inbox-sidebar { border-right: 0; border-bottom: 1px solid var(--line); } .conversation-list { max-height: 230px; } .message-stream { min-height: 420px; } .message-bubble { max-width: 88%; } }
</style>

<div class="inbox-page py-2">
    <div class="inbox-hero d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><div class="small text-white-50 mb-1"><i class="bi bi-headset me-1"></i> TRUNG TÂM HỖ TRỢ</div><h1 class="h3 fw-bold mb-1">Chat khách hàng</h1><p class="mb-0 text-white-50">Theo dõi và trả lời hội thoại theo thời gian thực.</p></div>
        <a href="{{ route('admin.chat.history') }}" class="btn btn-light rounded-pill"><i class="bi bi-clock-history me-1"></i> Lịch sử chat</a>
        <div class="text-end"><div class="small text-white-50">Kênh hỗ trợ</div><strong><span class="online-dot bg-white"></span>Đang hoạt động</strong></div>
    </div>

    <div class="inbox-shell">
        <aside class="inbox-sidebar">
            <div class="inbox-sidebar-head">
                <div class="d-flex justify-content-between align-items-center mb-3"><strong class="text-dark">Hội thoại</strong><span class="badge rounded-pill bg-info-subtle text-info-emphasis" id="conversation-count">0</span></div>
                <div class="input-group input-group-sm"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input type="search" id="conversation-search" class="form-control border-start-0" placeholder="Tìm khách hàng..."></div>
            </div>
            <div id="conversation-list" class="conversation-list"><div class="empty-inbox"><div><i class="bi bi-chat-square-text fs-2 d-block mb-2"></i>Đang tải hội thoại...</div></div></div>
        </aside>

        <section class="inbox-main">
            <div id="conversation-head" class="conversation-head">
                <div><h2 class="h5 fw-bold mb-1">Chọn một hội thoại</h2><small class="text-muted">Tin nhắn của khách sẽ xuất hiện tại đây.</small></div>
            </div>
            <div id="message-stream" class="message-stream"><div class="empty-inbox"><div><i class="bi bi-chat-left-dots fs-1 d-block mb-3 text-info"></i><strong>Chưa chọn khách hàng</strong><p class="small mb-0 mt-1">Chọn một cuộc trò chuyện ở danh sách bên trái.</p></div></div></div>
            <div class="composer" id="message-composer">
                <div class="quick-replies" id="quick-replies">
                    <button type="button" class="quick-reply" data-message="Chào bạn, Aloha Beauty có thể hỗ trợ gì cho bạn hôm nay?">Gửi lời chào</button>
                    <button type="button" class="quick-reply" data-message="Aloha Beauty đã nhận được yêu cầu của bạn và sẽ phản hồi sớm nhất nhé!">Đã nhận yêu cầu</button>
                    <button type="button" class="quick-reply" data-message="Bạn gửi giúp Aloha Beauty thêm mã đơn hàng để kiểm tra nhanh hơn nhé!">Xin mã đơn hàng</button>
                </div>
                <form id="message-form" class="d-flex gap-2 align-items-end">
                    @csrf
                    <div class="flex-grow-1">
                        <div id="attachment-preview" class="small text-muted mb-2"></div>
                        <textarea id="message-input" class="form-control" rows="2" placeholder="Viết tin nhắn..." maxlength="2000" disabled></textarea>
                    </div>
                    <label class="btn btn-light border rounded-circle flex-shrink-0" style="width:46px;height:46px;padding-top:12px" title="Gửi ảnh"><i class="bi bi-image"></i><input type="file" id="attachment-input" accept="image/*" hidden disabled></label>
                    <button type="button" id="emoji-button" class="btn btn-light border rounded-circle flex-shrink-0" style="width:46px;height:46px" disabled title="Thêm biểu tượng"><i class="bi bi-emoji-smile"></i></button>
                    <button id="send-message" type="submit" class="btn btn-primary rounded-circle flex-shrink-0" style="width:46px;height:46px" disabled title="Gửi tin nhắn"><i class="bi bi-send-fill"></i></button>
                </form>
                <div class="small text-muted mt-2"><i class="bi bi-lightning-charge me-1"></i>Realtime qua kênh hỗ trợ Aloha Beauty</div>
            </div>
        </section>
    </div>
</div>

<div id="inbox-toast" class="inbox-toast"><i class="bi bi-chat-dots-fill me-2"></i><span id="inbox-toast-text">Tin nhắn mới</span></div>
<audio id="inbox-ding" preload="auto" src="https://actions.google.com/sounds/v1/communications/incoming_message.ogg"></audio>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
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
        fetch('/chat/message', { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: formData }).then(response => response.json()).then(sent => {
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
    const heartbeat = () => fetch('/chat/heartbeat', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
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
</script>
@endsection
