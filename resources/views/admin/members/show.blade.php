@extends('layouts.app')
@section('title', 'Hồ sơ khách hàng: ' . $user->name . ' - Aloha Beauty')

@push('styles')
<link rel="stylesheet" href="{{ asset_v('css/views/admin-members-show-blade-php.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- HEADER & ĐIỀU HƯỚNG -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2">
                <i class="bi bi-arrow-left me-1"></i> Danh sách khách hàng
            </a>
            <h3 class="fw-bold text-dark mb-0">Hồ sơ khách hàng: {{ $user->name }}</h3>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#adjustPointsModal">
                <i class="bi bi-award me-1"></i> Cộng/Trừ điểm
            </button>
            <button type="button" class="btn btn-outline-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#giveVoucherModal">
                <i class="bi bi-ticket-perforated me-1"></i> Tặng Voucher
            </button>
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                <i class="bi bi-key me-1"></i> Đổi mật khẩu
            </button>
            <a href="{{ route('admin.customers.edit', $user) }}" class="btn btn-warning rounded-pill px-3">
                <i class="bi bi-pencil me-1"></i> Chỉnh sửa
            </a>
            <form action="{{ route('admin.customers.toggle-lock', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $user->isLocked() ? 'Mở khóa tài khoản?' : 'Khóa tài khoản này?' }}');">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn {{ $user->isLocked() ? 'btn-outline-success' : 'btn-outline-danger' }} rounded-pill px-3">
                    <i class="bi {{ $user->isLocked() ? 'bi-unlock-fill' : 'bi-lock-fill' }} me-1"></i>
                    {{ $user->isLocked() ? 'Mở khóa' : 'Khóa tài khoản' }}
                </button>
            </form>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 bg-success-subtle text-success-emphasis" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 bg-danger-subtle text-danger-emphasis" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- CỘT TRÁI: THÔNG TIN HỒ SƠ KHÁCH HÀNG -->
        <div class="col-lg-4">
            <div class="customer-profile-card p-4">
                @php
                    $nameParts = explode(' ', trim($user->name));
                    $initials = count($nameParts) >= 2 
                        ? mb_strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1))
                        : mb_strtoupper(mb_substr($user->name, 0, 2));

                    $tierBadgeClasses = [
                        'Kim cương' => 'badge-tier-diamond',
                        'Bạch kim' => 'badge-tier-platinum',
                        'Vàng' => 'badge-tier-gold',
                        'Bạc' => 'badge-tier-silver',
                        'Thành viên' => 'badge-tier-member',
                        'Mới tham gia' => 'badge-tier-new',
                    ];
                    $tierBadgeClass = $tierBadgeClasses[$membershipTier['name']] ?? 'badge-tier-new';
                @endphp

                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block mx-auto mb-2">
                        <div class="profile-avatar-lg overflow-hidden mb-0">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="Ảnh đại diện của {{ $user->name }}" class="view-inline-1">
                            @else
                                {{ $user->initials }}
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-white border rounded-circle shadow-sm position-absolute bottom-0 end-0 p-0 d-flex align-items-center justify-content-center bg-white view-inline-2" title="Đổi ảnh đại diện khách hàng" data-bs-toggle="modal" data-bs-target="#changeAvatarModal">
                            <i class="bi bi-camera-fill text-primary view-inline-3"></i>
                        </button>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark">{{ $user->name }}</h4>
                    <p class="text-muted mb-2"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</p>
                    
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <span class="badge rounded-pill px-3 py-1 {{ $tierBadgeClass }}">
                            <i class="bi bi-star-fill me-1"></i>{{ $membershipTier['name'] }}
                        </span>
                        @if($user->isLocked())
                            <span class="badge bg-danger rounded-pill px-3 py-1">
                                <i class="bi bi-lock-fill me-1"></i>Đang bị khóa
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i>Hoạt động
                            </span>
                        @endif
                    </div>
                </div>

                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted"><i class="bi bi-telephone me-2"></i>Số điện thoại</span>
                        <strong class="text-dark">{{ $user->phone ?: 'Chưa cập nhật' }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted"><i class="bi bi-award me-2 text-warning"></i>Điểm tích lũy</span>
                        <strong class="text-warning fs-6">{{ number_format($user->loyalty_points) }} điểm</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted"><i class="bi bi-cash-coin me-2 text-success"></i>Doanh số hoàn thành</span>
                        <strong class="text-success fs-6">{{ number_format($user->completed_spend, 0, ',', '.') }} đ</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted"><i class="bi bi-receipt me-2 text-primary"></i>Tổng đơn hàng</span>
                        <strong class="text-dark">{{ $user->orders_count }} đơn</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted"><i class="bi bi-ticket-perforated me-2 text-info"></i>Voucher trong ví</span>
                        <strong class="text-info">{{ $user->collected_vouchers_count }} mã</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted"><i class="bi bi-calendar3 me-2"></i>Ngày gia nhập</span>
                        <span class="text-dark">{{ $user->created_at?->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-center">
                    <form action="{{ route('admin.customers.destroy', $user) }}" method="POST" onsubmit="return confirm('Xóa vĩnh viễn tài khoản khách hàng {{ $user->name }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i> Xóa tài khoản này
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: HỆ THỐNG TABS CHI TIẾT -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <ul class="nav nav-profile-tabs" id="customerDetailTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="tab-orders-btn" data-bs-toggle="tab" data-bs-target="#tab-orders" type="button">
                                <i class="bi bi-receipt me-1"></i> Đơn hàng ({{ $user->orders_count }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-points-btn" data-bs-toggle="tab" data-bs-target="#tab-points" type="button">
                                <i class="bi bi-award me-1"></i> Lịch sử điểm ({{ $transactions->total() }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-vouchers-btn" data-bs-toggle="tab" data-bs-target="#tab-vouchers" type="button">
                                <i class="bi bi-ticket-perforated me-1"></i> Ví Voucher ({{ $vouchers->count() }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-addresses-btn" data-bs-toggle="tab" data-bs-target="#tab-addresses" type="button">
                                <i class="bi bi-geo-alt me-1"></i> Sổ địa chỉ ({{ $addresses->count() }})
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="customerDetailTabsContent">
                        
                        <!-- TAB 1: LỊCH SỬ ĐƠN HÀNG -->
                        <div class="tab-pane fade show active" id="tab-orders" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 text-start">Mã đơn</th>
                                            <th>Ngày đặt</th>
                                            <th>Hình thức thanh toán</th>
                                            <th class="text-end">Giá trị đơn</th>
                                            <th>Trạng thái đơn</th>
                                            <th class="text-end pe-3">Chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $ord)
                                            @php
                                                $orderStatus = strtolower($ord->status ?? '');
                                                $payMethod = strtoupper($ord->payment_method ?? '');
                                            @endphp
                                            <tr>
                                                <td class="ps-3 text-start fw-bold text-primary">#{{ $ord->id }}</td>
                                                <td class="text-muted small">{{ $ord->created_at?->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if($payMethod === 'COD')
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-1">Tiền mặt (COD)</span>
                                                    @elseif(in_array($payMethod, ['PAYOS', 'ONLINE', 'BANK_TRANSFER']))
                                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">Chuyển khoản (PayOS)</span>
                                                    @else
                                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1">{{ $ord->payment_method ?: 'Tiền mặt' }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold text-danger">{{ number_format($ord->total, 0, ',', '.') }} đ</td>
                                                <td>
                                                    @if(in_array($orderStatus, ['processing', 'đang xử lý']))
                                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Chờ xác nhận</span>
                                                    @elseif($orderStatus === 'confirmed')
                                                        <span class="badge bg-info text-dark rounded-pill px-3 py-1">Đã xác nhận</span>
                                                    @elseif($orderStatus === 'packing')
                                                        <span class="badge bg-secondary rounded-pill px-3 py-1">Đang đóng gói</span>
                                                    @elseif($orderStatus === 'shipping')
                                                        <span class="badge bg-primary rounded-pill px-3 py-1">Đang giao hàng</span>
                                                    @elseif(in_array($orderStatus, ['paid', 'đã thanh toán']))
                                                        <span class="badge bg-success rounded-pill px-3 py-1">Đã thanh toán</span>
                                                    @elseif($orderStatus === 'completed')
                                                        <span class="badge bg-success rounded-pill px-3 py-1">Đã nhận hàng</span>
                                                    @elseif(in_array($orderStatus, ['cancelled', 'đã huỷ']))
                                                        <span class="badge bg-danger rounded-pill px-3 py-1">Đã hủy</span>
                                                    @elseif($orderStatus === 'refund_pending')
                                                        <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-1">Chờ hoàn tiền</span>
                                                    @elseif($orderStatus === 'refunded')
                                                        <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-1">Đã hoàn tiền</span>
                                                    @else
                                                        <span class="badge bg-dark rounded-pill px-3 py-1">{{ ucfirst($ord->status) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-3">
                                                    <a href="{{ route('orders.show', $ord->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                        Xem đơn
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="py-4 text-muted">Khách hàng chưa có đơn hàng nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($orders->hasPages())
                                <div class="mt-3 d-flex justify-content-center">
                                    {{ $orders->appends(request()->except('orders_page'))->links() }}
                                </div>
                            @endif
                        </div>

                        <!-- TAB 2: LỊCH SỬ ĐIỂM THƯỞNG -->
                        <div class="tab-pane fade" id="tab-points" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Lịch sử cộng / trừ điểm</h6>
                                <button type="button" class="btn btn-sm btn-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#adjustPointsModal">
                                    <i class="bi bi-plus-slash-minus me-1"></i> Điều chỉnh điểm
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Thời gian</th>
                                            <th>Nội dung / Lý do</th>
                                            <th class="text-end pe-3">Số điểm</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $tx)
                                            <tr>
                                                <td class="ps-3 text-muted small">{{ $tx->created_at?->format('d/m/Y H:i') }}</td>
                                                <td class="fw-semibold text-dark">{{ $tx->description }}</td>
                                                <td class="text-end pe-3 fw-bold fs-6 {{ $tx->points > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $tx->points > 0 ? '+' : '' }}{{ number_format($tx->points) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="py-4 text-center text-muted">Chưa có giao dịch tích điểm nào.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($transactions->hasPages())
                                <div class="mt-3 d-flex justify-content-center">
                                    {{ $transactions->appends(request()->except('points_page'))->links() }}
                                </div>
                            @endif
                        </div>

                        <!-- TAB 3: VÍ VOUCHERS -->
                        <div class="tab-pane fade" id="tab-vouchers" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Mã giảm giá trong ví của khách</h6>
                                <button type="button" class="btn btn-sm btn-info rounded-pill px-3 text-white" data-bs-toggle="modal" data-bs-target="#giveVoucherModal">
                                    <i class="bi bi-gift me-1"></i> Tặng Voucher ngay
                                </button>
                            </div>

                            <div class="row g-3">
                                @forelse($vouchers as $vc)
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 border bg-light d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-primary rounded-pill mb-1">[{{ $vc->code }}]</span>
                                                <div class="fw-bold text-dark">
                                                    @if($vc->type === 'percent') Giảm {{ $vc->value }}%
                                                    @elseif($vc->type === 'fixed') Giảm {{ number_format($vc->value, 0, ',', '.') }} đ
                                                    @else Miễn phí vận chuyển
                                                    @endif
                                                </div>
                                                <small class="text-muted d-block">
                                                    Hạn dùng: {{ $vc->expires_at ? $vc->expires_at->format('d/m/Y') : 'Không giới hạn' }}
                                                </small>
                                            </div>
                                            <i class="bi bi-ticket-perforated-fill text-info fs-2"></i>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4 text-muted">
                                        Khách hàng hiện chưa sở hữu mã giảm giá nào.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- TAB 4: SỔ ĐỊA CHỈ -->
                        <div class="tab-pane fade" id="tab-addresses" role="tabpanel">
                            <h6 class="fw-bold mb-3">Địa chỉ nhận hàng đã lưu</h6>
                            <div class="row g-3">
                                @forelse($addresses as $addr)
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 border bg-light">
                                            <div class="fw-bold text-dark mb-1">{{ $addr->recipient_name }}</div>
                                            <div class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $addr->phone }}</div>
                                            <div class="small text-dark">{{ $addr->address_detail }}, {{ $addr->ward }}, {{ $addr->district }}, {{ $addr->province }}</div>
                                            @if($addr->is_default)
                                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill mt-2">Mặc định</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4 text-muted">
                                        Khách hàng chưa lưu địa chỉ giao hàng nào trong tài khoản.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CỘNG/TRỪ ĐIỂM -->
<div class="modal fade" id="adjustPointsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.customers.adjust-points', $user) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-award-fill me-2 text-warning"></i>Điều chỉnh điểm thưởng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border d-flex justify-content-between align-items-center">
                        <span>Điểm hiện tại:</span>
                        <strong class="fs-5 text-warning">{{ number_format($user->loyalty_points) }} điểm</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Loại điều chỉnh</label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="add">➕ Cộng thêm điểm thưởng</option>
                            <option value="subtract">➖ Trừ bớt điểm</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Số điểm</label>
                        <input type="number" name="points" class="form-control rounded-3" min="1" max="1000000" placeholder="Ví dụ: 50" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Lý do điều chỉnh</label>
                        <input type="text" name="description" class="form-control rounded-3" placeholder="Ví dụ: Thưởng thành viên tích cực" required>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Xác nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TẶNG VOUCHER -->
<div class="modal fade" id="giveVoucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.customers.give-voucher', $user) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-ticket-perforated-fill me-2 text-info"></i>Tặng Voucher cho {{ $user->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Chọn mã giảm giá</label>
                        <select name="voucher_id" class="form-select rounded-3" required>
                            <option value="">-- Chọn một voucher --</option>
                            @foreach($availableVouchers as $vc)
                                <option value="{{ $vc->id }}">
                                    [{{ $vc->code }}] - 
                                    @if($vc->type === 'percent') Giảm {{ $vc->value }}%
                                    @elseif($vc->type === 'fixed') Giảm {{ number_format($vc->value, 0, ',', '.') }} đ
                                    @else Miễn phí vận chuyển
                                    @endif
                                    (Hạn: {{ $vc->expires_at ? $vc->expires_at->format('d/m/Y') : 'Vĩnh viễn' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4 text-white fw-bold">Tặng Voucher ngay</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL ĐẶT LẠI MẬT KHẨU -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.customers.reset-password', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-key-fill me-2 text-secondary"></i>Đặt lại mật khẩu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Mật khẩu mới (tối thiểu 8 ký tự)</label>
                        <input type="password" name="password" class="form-control rounded-3" minlength="8" required placeholder="Nhập mật khẩu mới...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Xác nhận lại mật khẩu</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" minlength="8" required placeholder="Nhập lại mật khẩu mới...">
                    </div>
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Cập nhật mật khẩu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CẬP NHẬT ẢNH ĐẠI DIỆN -->
<div class="modal fade" id="changeAvatarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.customers.avatar', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-camera-fill me-2 text-primary"></i>Ảnh đại diện khách hàng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <!-- Khung xem trước ảnh đại diện -->
                    <div class="position-relative d-inline-block mx-auto mb-3">
                        <div id="avatarPreviewContainer" class="profile-avatar-lg overflow-hidden mb-0 shadow-sm border view-inline-4">
                            @if($user->avatar_url)
                                <img id="avatarPreviewImg" src="{{ $user->avatar_url }}" alt="Ảnh đại diện của {{ $user->name }}" class="view-inline-1">
                            @else
                                <span id="avatarInitialsText" class="view-inline-5">{{ $user->initials }}</span>
                                <img id="avatarPreviewImg" src="" alt="Preview" class="d-none view-inline-1">
                            @endif
                        </div>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold text-muted" for="customerAvatarInput">Chọn ảnh mới (JPG, PNG, WEBP, tối đa 2MB)</label>
                        <input id="customerAvatarInput" type="file" name="avatar" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp">
                    </div>

                    @if($user->avatar_path)
                        <div class="form-check form-switch text-start mt-3 p-2 bg-light rounded-3 border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="remove_avatar" value="1" id="removeAvatarCheck">
                            <label class="form-check-label text-danger small fw-semibold" for="removeAvatarCheck">
                                <i class="bi bi-trash3 me-1"></i> Xóa ảnh đại diện hiện tại (dùng chữ cái mặc định)
                            </label>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Lưu ảnh đại diện</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset_v('js/views/admin-members-show-blade-php.js') }}" defer></script>
@endpush
@endsection
