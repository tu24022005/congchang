@extends('admin.layouts.app')

@section('title', 'Thông báo khách hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Thông báo khách hàng</h2>
        <p class="text-muted mb-0">Các hoạt động mới từ khách hàng, không bao gồm tin nhắn chat.</p>
    </div>
    @if($notifications->whereNull('read_at')->count())
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button class="btn btn-outline-primary">
                <i class="bi bi-check2-all me-1"></i> Đánh dấu tất cả đã đọc
            </button>
        </form>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            @php($data = $notification->data)
            <a href="{{ route('admin.notifications.read', $notification->id) }}"
               class="list-group-item list-group-item-action py-3 {{ $notification->read_at ? '' : 'bg-light' }}">
                <div class="d-flex gap-3">
                    <i class="bi bi-bell{{ $notification->read_at ? '' : '-fill' }} text-primary fs-4"></i>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between gap-3">
                            <strong>{{ $data['title'] ?? 'Thông báo mới' }}</strong>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="text-muted">{{ $data['message'] ?? '' }}</div>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-5 text-center text-muted">
                <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                Chưa có thông báo nào.
            </div>
        @endforelse
    </div>
</div>

<div class="mt-3">{{ $notifications->links() }}</div>
@endsection
