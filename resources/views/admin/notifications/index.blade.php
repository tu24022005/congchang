@extends('admin.layouts.app')

@section('title', 'Thông báo khách hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Thông báo khách hàng</h2>
        <p class="text-muted mb-0">Các hoạt động mới từ khách hàng, không bao gồm tin nhắn chat.</p>
    </div>
</div>
@php
    $notificationCategories = [
        '' => ['label' => 'Tất cả', 'icon' => 'bell'],
        'order' => ['label' => 'Khách đặt đơn', 'icon' => 'cart-check'],
        'cancelled' => ['label' => 'Khách huỷ đơn', 'icon' => 'x-circle'],
        'refund' => ['label' => 'Tiền hoàn', 'icon' => 'cash-coin'],
        'review' => ['label' => 'Đánh giá', 'icon' => 'star'],
        'stock' => ['label' => 'Báo có hàng', 'icon' => 'box-seam'],
    ];
@endphp
<div class="d-flex flex-wrap gap-2 mb-4">
    @foreach($notificationCategories as $key => $item)
        <a href="{{ route('admin.notifications.index', $key === '' ? [] : ['category' => $key]) }}"
           class="btn btn-sm rounded-pill {{ $category === $key ? 'btn-primary' : 'btn-light border' }}">
            <i class="bi bi-{{ $item['icon'] }} me-1"></i>{{ $item['label'] }}
        </a>
    @endforeach
</div>

<div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            @php($data = $notification->data)
            @php($notificationCategory = $notificationCategories[$data['category'] ?? ''] ?? ['label' => 'Khác', 'icon' => 'bell'])
            <a href="{{ route('admin.notifications.read', $notification->id) }}"
               class="list-group-item list-group-item-action py-3 {{ $notification->read_at ? '' : 'bg-light' }}">
                <div class="d-flex gap-3">
                    <i class="bi bi-{{ $notificationCategory['icon'] }} text-primary fs-4"></i>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between gap-3">
                            <strong>{{ $data['title'] ?? 'Thông báo mới' }}</strong>
                            @if(($data['category'] ?? '') !== '')
                                <span class="badge bg-light text-primary border ms-2">{{ $notificationCategory['label'] }}</span>
                            @endif
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
