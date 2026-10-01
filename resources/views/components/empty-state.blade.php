@props([
    'type' => 'cart', // cart, wishlist, search, order, notification, review, chat, default
    'title' => 'Không có dữ liệu',
    'description' => 'Hiện tại chưa có mục nào để hiển thị tại đây.',
    'actionLabel' => null,
    'actionUrl' => null,
    'actionIcon' => 'bi-arrow-right',
    'secondaryLabel' => null,
    'secondaryUrl' => null,
])

<div class="empty-state-card text-center py-5 px-3 mx-auto view-inline-1" role="status">
    <div class="empty-state-illustration mb-4 position-relative d-inline-block">
        <div class="empty-state-aura view-inline-2">
            <div class="empty-state-icon-wrap view-inline-3">
                @if($type === 'cart')
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-danger" aria-hidden="true">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        <path d="M12 9l4 4m0-4l-4 4" stroke="#e11d48" stroke-width="1.8"></path>
                    </svg>
                @elseif($type === 'wishlist')
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-danger" aria-hidden="true">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                @elseif($type === 'search')
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-danger" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="8" y1="11" x2="14" y2="11" stroke="#e11d48"></line>
                    </svg>
                @elseif($type === 'order')
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-danger" aria-hidden="true">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                @elseif($type === 'notification')
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-danger" aria-hidden="true">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                @elseif($type === 'review')
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-danger" aria-hidden="true">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                @elseif($type === 'chat')
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-danger" aria-hidden="true">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                @else
                    <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-danger" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                @endif
            </div>
        </div>
    </div>

    <h4 class="fw-bold text-dark mb-2 empty-state-title">{{ $title }}</h4>
    <p class="text-muted small mb-4 empty-state-desc view-inline-4">{{ $description }}</p>

    @if($actionLabel && $actionUrl)
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="{{ $actionUrl }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                @if($actionIcon)<i class="bi {{ $actionIcon }} me-1"></i>@endif
                {{ $actionLabel }}
            </a>
            @if($secondaryLabel && $secondaryUrl)
                <a href="{{ $secondaryUrl }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">
                    {{ $secondaryLabel }}
                </a>
            @endif
        </div>
    @endif

    {{ $slot ?? '' }}
</div>
