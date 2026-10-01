@extends('layouts.app')
@section('title', 'Chat khách hàng')

@section('content')
<link rel="stylesheet" href="{{ asset_v('css/views/admin-chat-index-blade-php.css') }}">

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
                    <label class="btn btn-light border rounded-circle flex-shrink-0 view-inline-1" title="Gửi ảnh"><i class="bi bi-image"></i><input type="file" id="attachment-input" accept="image/*" hidden disabled></label>
                    <button type="button" id="emoji-button" class="btn btn-light border rounded-circle flex-shrink-0 view-inline-2" disabled title="Thêm biểu tượng"><i class="bi bi-emoji-smile"></i></button>
                    <button id="send-message" type="submit" class="btn btn-primary rounded-circle flex-shrink-0 view-inline-2" disabled title="Gửi tin nhắn"><i class="bi bi-send-fill"></i></button>
                </form>
                <div class="small text-muted mt-2"><i class="bi bi-lightning-charge me-1"></i>Realtime qua kênh hỗ trợ Aloha Beauty</div>
            </div>
        </section>
    </div>
</div>

<div id="inbox-toast" class="inbox-toast"><i class="bi bi-chat-dots-fill me-2"></i><span id="inbox-toast-text">Tin nhắn mới</span></div>
<audio id="inbox-ding" preload="auto" src="https://actions.google.com/sounds/v1/communications/incoming_message.ogg"></audio>

<script src="{{ asset_v('js/views/admin-chat-index-blade-php.js') }}" defer></script>
@endsection
