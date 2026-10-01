@extends('layouts.app')
@section('title', 'Điểm thành viên & Ưu đãi')

@section('content')
<link rel="stylesheet" href="{{ asset_v('css/views/loyalty-index-blade-php.css') }}">

<div class="container py-4 loyalty-page">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-3">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== HERO ===== --}}
    <div class="loyalty-hero mb-4">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <div class="view-inline-1">
                    ALOHA BEAUTY · ĐIỂM THÀNH VIÊN
                </div>
                <div class="points-num">{{ number_format($user->loyalty_points) }}</div>
                <div class="points-sub"><i class="bi bi-stars me-1"></i>điểm tích lũy hiện có</div>
            </div>
            <div class="text-end">
                <div class="view-inline-2">Hạng hiện tại</div>
                <div class="view-inline-3">
                    {{ $membershipTier['icon'] ?? '' }} {{ $membershipTier['name'] }}
                </div>
            </div>
        </div>

        {{-- Thống kê --}}
        <div class="loyalty-stat-row">
            <div class="loyalty-stat">
                <strong>{{ number_format($pointsEarned) }}</strong>
                <span>Điểm đã tích lũy</span>
            </div>
            <div class="loyalty-stat">
                <strong>{{ number_format($pointsSpent) }}</strong>
                <span>Điểm đã dùng</span>
            </div>
            <div class="loyalty-stat">
                <strong>{{ number_format($completedSpend, 0, ',', '.') }}đ</strong>
                <span>Doanh số tích lũy</span>
            </div>
        </div>

        {{-- Progress --}}
        <div class="loyalty-progress-wrap">
            <div class="loyalty-progress-label">
                <span>{{ $membershipTier['name'] }}</span>
                <span>{{ $nextTier ? $nextTier['name'] : 'Hạng cao nhất ✨' }}</span>
            </div>
            <div class="loyalty-progress-bar">
                <div class="loyalty-progress-fill" data-inline-width="{{ round($tierProgress) }}" class="inline-dynamic-width"></div>
            </div>
            <div class="loyalty-progress-hint">
                @if($nextTier)
                    @php $rem = max(0, $nextTier['threshold'] - $effectiveValue); @endphp
                    Còn <strong>{{ number_format($rem, 0, ',', '.') }}đ</strong> hoặc <strong>{{ number_format(ceil($rem / 10000)) }} điểm</strong> để lên hạng {{ $nextTier['name'] }}
                @else
                    <i class="bi bi-patch-check-fill me-1"></i>Bạn đang ở hạng cao nhất — Kim cương!
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="loyalty-hero-actions">
            <a href="{{ route('account.vouchers') }}" class="loyalty-hero-btn">
                <i class="bi bi-ticket-perforated"></i> Kho voucher
            </a>
            <a href="{{ route('account') }}" class="loyalty-hero-btn">
                <i class="bi bi-person-circle"></i> Tài khoản
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- CỘT TRÁI --}}
        <div class="col-lg-5">

            {{-- HẠNG THÀNH VIÊN --}}
            <div class="loy-card mb-4">
                <div class="loy-card-head">
                    <div class="lch-icon icon-purple"><i class="bi bi-award-fill"></i></div>
                    <div>
                        <div class="lch-title">Hạng thành viên</div>
                        <div class="lch-sub">6 cấp độ · mua sắm để thăng hạng</div>
                    </div>
                </div>
                <div class="loy-card-body">
                    <div class="tier-list">
                        @php
                            $currentIdx = collect($tiers)->search(fn ($t) => $t['name'] === $membershipTier['name']);
                        @endphp
                        @foreach($tiers as $idx => $tier)
                            @php
                                $isDone    = $idx < $currentIdx;
                                $isCurrent = $idx == $currentIdx;
                            @endphp
                            <div class="tier-item {{ $isCurrent ? 'is-current' : ($isDone ? 'is-done' : '') }}">
                                @if($isCurrent)<div class="tier-current-dot"></div>@endif
                                <div class="tier-icon">{{ $tier['icon'] }}</div>
                                <div class="tier-info">
                                    <div class="tier-name">{{ $tier['name'] }}</div>
                                    <div class="tier-req">
                                        @if($tier['threshold'] > 0)
                                            Chi tiêu {{ number_format($tier['threshold'], 0, ',', '.') }}đ
                                            hoặc tích {{ number_format($tier['points']) }} điểm
                                        @else
                                            Miễn phí khi đăng ký
                                        @endif
                                    </div>
                                </div>
                                @if($isDone)
                                    <span class="tier-badge done"><i class="bi bi-check2 me-1"></i>Đạt</span>
                                @elseif($isCurrent)
                                    <span class="tier-badge current"><i class="bi bi-star-fill me-1"></i>Hiện tại</span>
                                @else
                                    <span class="tier-badge next"><i class="bi bi-lock me-1"></i>Chưa mở</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 p-3 rounded-3 view-inline-4">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Điểm thưởng tích lũy theo mỗi đơn hàng hoàn thành. Hạng thành viên dựa trên tổng chi tiêu hoặc điểm tích lũy (lấy giá trị cao hơn).
                    </div>
                </div>
            </div>

        </div>

        {{-- CỘT PHẢI --}}
        <div class="col-lg-7">

            {{-- ĐỔI ĐIỂM --}}
            <div class="loy-card mb-4">
                <div class="loy-card-head">
                    <div class="lch-icon icon-amber"><i class="bi bi-gift-fill"></i></div>
                    <div>
                        <div class="lch-title">Đổi điểm lấy voucher</div>
                        <div class="lch-sub">Chọn gói phù hợp · voucher hiệu lực 30 ngày</div>
                    </div>
                </div>
                <div class="loy-card-body">
                    <div class="rewards-grid">
                        @foreach($rewards as $reward)
                            @php $canAfford = $user->loyalty_points >= $reward['points']; @endphp
                            <div class="reward-card {{ !$canAfford ? 'cant-afford' : '' }}"
                                 data-inline-color="{{ $reward['color'] }}" data-inline-color-light="{{ $reward['color'] }}" class="inline-dynamic-color">
                                <div class="rc-icon"><i class="bi {{ $reward['icon'] }}"></i></div>
                                <div class="rc-points">{{ number_format($reward['points']) }} điểm</div>
                                <div class="rc-label">{{ $reward['label'] }}</div>
                                @if($canAfford)
                                    <form action="{{ route('loyalty.redeem') }}" method="POST" class="view-inline-5">
                                        @csrf
                                        <input type="hidden" name="points" value="{{ $reward['points'] }}">
                                        <button type="submit" class="rc-btn"
                                                onclick="return confirm('Đổi {{ $reward['points'] }} điểm lấy {{ $reward['label'] }}?')">
                                            Đổi ngay
                                        </button>
                                    </form>
                                @else
                                    <div class="rc-insufficient">
                                        Cần thêm {{ number_format($reward['points'] - $user->loyalty_points) }} điểm
                                    </div>
                                    <button class="rc-btn" disabled>Chưa đủ điểm</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 p-3 rounded-3 d-flex align-items-center gap-2 view-inline-6">
                        <i class="bi bi-lightbulb-fill fs-5 view-inline-7"></i>
                        <span>Voucher được tạo tự động và gửi vào kho voucher của bạn. Mỗi mã dùng được 1 lần cho đơn hàng bất kỳ.</span>
                    </div>
                </div>
            </div>

            {{-- LỊCH SỬ ĐIỂM --}}
            <div class="loy-card">
                <div class="loy-card-head">
                    <div class="lch-icon icon-blue"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <div class="lch-title">Lịch sử điểm</div>
                        <div class="lch-sub">{{ $transactions->total() }} giao dịch</div>
                    </div>
                </div>
                <div class="loy-card-body">
                    @forelse($transactions as $tx)
                        <div class="tx-item">
                            <div class="tx-dot {{ $tx->points > 0 ? 'earn' : 'spend' }}">
                                <i class="bi {{ $tx->points > 0 ? 'bi-plus-lg' : 'bi-dash-lg' }}"></i>
                            </div>
                            <div class="tx-desc">
                                {{ $tx->description }}
                                <small>{{ $tx->created_at->format('d/m/Y · H:i') }}</small>
                            </div>
                            <div class="tx-pts {{ $tx->points > 0 ? 'earn' : 'spend' }}">
                                {{ $tx->points > 0 ? '+' : '' }}{{ number_format($tx->points) }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-receipt text-muted view-inline-8"></i>
                            <p class="text-muted mt-2 mb-0 view-inline-9">Chưa có giao dịch điểm nào.</p>
                        </div>
                    @endforelse

                    @if($transactions->hasPages())
                        <div class="mt-3">{{ $transactions->links() }}</div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
