@extends('layouts.app')
@section('title', 'Thông báo của tôi')
@section('content')
<div class="account-page py-3 py-lg-4">
    <div class="account-page-heading mb-4"><div><span class="account-kicker">CẬP NHẬT MỚI NHẤT</span><h1 class="mb-2">Thông báo</h1><p class="text-muted mb-0">Theo dõi cập nhật về đơn hàng và hoạt động tài khoản.</p></div><a href="{{ route('account') }}" class="btn btn-outline-primary rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i>Về tài khoản</a></div>
    <div class="account-notification-list">
        @forelse($notifications as $notification)
            <a href="{{ route('account.notifications.read', $notification->id) }}" class="account-notification-item {{ $notification->read_at ? '' : 'is-unread' }}"><span class="account-hub-icon"><i class="bi bi-bell-fill"></i></span><span><strong>{{ $notification->data['title'] ?? 'Thông báo mới' }}</strong><small>{{ $notification->data['message'] ?? 'Bạn có một cập nhật mới.' }}</small><time>{{ $notification->created_at->diffForHumans() }}</time></span>@unless($notification->read_at)<b>Mới</b>@endunless</a>
        @empty
            <div class="account-empty-state"><i class="bi bi-bell-slash"></i><h4>Chưa có thông báo</h4><p class="text-muted">Bạn sẽ nhận được cập nhật tại đây.</p></div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center mt-4">{{ $notifications->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
