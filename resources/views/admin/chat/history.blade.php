@extends('layouts.app')

@section('title', 'Lịch sử chat')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="admin-eyebrow"><i class="bi bi-clock-history me-1"></i> TRUNG TÂM HỖ TRỢ</span>
            <h2 class="fw-bold mb-1">Lịch sử chat</h2>
            <p class="text-muted mb-0">Xem lại toàn bộ tin nhắn đã trao đổi với khách hàng.</p>
        </div>
        <a href="{{ route('admin.chat.index') }}" class="btn btn-primary rounded-pill"><i class="bi bi-chat-dots me-1"></i> Quay lại chat</a>
    </div>

    <form method="GET" class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Khách hàng</label>
                <select name="user_id" class="form-select">
                    <option value="">Tất cả khách hàng</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Ngày chat</label>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Từ khóa</label>
                <input type="search" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Tìm nội dung tin nhắn">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Lọc</button>
                <a href="{{ route('admin.chat.history') }}" class="btn btn-outline-secondary">Xóa</a>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>Thời gian</th><th>Khách hàng</th><th>Người gửi</th><th>Nội dung</th><th>Trạng thái</th></tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                        <tr>
                            <td class="text-nowrap">{{ $message->created_at->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $message->user?->name ?? 'Tài khoản đã xóa' }}</strong><small class="d-block text-muted">{{ $message->user?->email }}</small></td>
                            <td><span class="badge {{ $message->is_admin ? 'text-bg-primary' : 'text-bg-light' }}">{{ $message->is_admin ? 'Nhân viên hỗ trợ' : 'Khách hàng' }}</span></td>
                            <td style="min-width: 280px; white-space: pre-wrap;">{{ $message->message ?: 'Đã gửi tệp đính kèm' }}@if($message->attachment_url)<a href="{{ $message->attachment_url }}" target="_blank" class="d-block small text-primary"><i class="bi bi-paperclip me-1"></i>Xem tệp đính kèm</a>@endif</td>
                            <td><span class="badge {{ $message->is_read ? 'text-bg-success' : 'text-bg-warning' }}">{{ $message->is_read ? 'Đã đọc' : 'Chưa đọc' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">Chưa có tin nhắn phù hợp.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($messages->hasPages())<div class="card-footer bg-white border-0 p-3">{{ $messages->links() }}</div>@endif
    </div>
</div>
@endsection
