@extends('layouts.app')

@section('title', 'Lịch sử hoạt động')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="admin-eyebrow"><i class="bi bi-clock-history me-1"></i> KIỂM SOÁT HỆ THỐNG</span>
            <h2 class="fw-bold mb-1">Lịch sử hoạt động</h2>
            <p class="text-muted mb-0">Theo dõi các thao tác của nhân viên và quản lý.</p>
        </div>
    </div>

    <form class="card border-0 shadow-sm rounded-4 mb-4" method="GET">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Người thực hiện</label>
                <select name="actor_id" class="form-select">
                    <option value="">Tất cả</option>
                    @foreach($actors as $actor)
                        <option value="{{ $actor->id }}" @selected((string) request('actor_id') === (string) $actor->id)>{{ $actor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Hành động</label>
                <input name="action" class="form-control" value="{{ request('action') }}" placeholder="Nhập nội dung cần tìm">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Lọc</button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-dark"><tr><th>Thời gian</th><th>Người thực hiện</th><th>Hành động</th><th>Mô tả</th><th>Chi tiết thay đổi</th></tr></thead>
                <tbody>
                @forelse($logs as $log)
                    @php
                        $actionLabels = [
                            'account.created' => 'Tạo tài khoản',
                            'role.updated' => 'Đổi vai trò tài khoản',
                            'account.deleted' => 'Xóa tài khoản',
                            'product.stock.updated' => 'Cập nhật tồn kho sản phẩm',
                            'product.variation-stock.updated' => 'Cập nhật tồn kho từng mã loại',
                            'order.status.updated' => 'Cập nhật trạng thái đơn hàng',
                            'order.refunded' => 'Xác nhận hoàn tiền',
                            'order.refund.approved' => 'Duyệt yêu cầu hoàn tiền',
                            'order.refund.rejected' => 'Từ chối yêu cầu hoàn tiền',
                        ];
                        $actionLabel = $actionLabels[$log->action] ?? $log->action;
                        $fieldLabels = [
                            'stock' => 'Tồn kho',
                            'quantity' => 'Số lượng',
                            'role' => 'Vai trò',
                            'name' => 'Họ tên',
                            'email' => 'Email',
                            'status' => 'Trạng thái',
                            'refund_status' => 'Trạng thái hoàn tiền',
                            'refund_reference' => 'Mã giao dịch hoàn tiền',
                            'reason' => 'Lý do',
                        ];
                        $valueLabels = [
                            'processing' => 'Đang xử lý',
                            'confirmed' => 'Đã xác nhận',
                            'packing' => 'Đang đóng gói',
                            'shipping' => 'Đang giao hàng',
                            'paid' => 'Đã thanh toán',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                            'refund_pending' => 'Đang chờ hoàn tiền',
                            'refunded' => 'Đã hoàn tiền',
                            'requested' => 'Đã yêu cầu hoàn tiền',
                            'approved' => 'Đã duyệt hoàn tiền',
                            'rejected' => 'Đã từ chối hoàn tiền',
                            'admin' => 'Quản trị viên',
                            'manager' => 'Quản lý',
                            'warehouse_staff' => 'Nhân viên kho',
                            'customer_service' => 'Nhân viên chăm sóc khách hàng',
                            'customer' => 'Khách hàng',
                            'user' => 'Khách hàng',
                        ];
                        $translateField = fn ($field) => $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', (string) $field));
                        $translateValue = fn ($value) => $valueLabels[$value] ?? $value;
                        $details = collect([
                            'Trước khi thay đổi' => $log->before,
                            'Sau khi thay đổi' => $log->after,
                            'Thông tin thêm' => $log->metadata,
                        ])->filter(fn ($value) => filled($value));
                    @endphp
                    <tr>
                        <td class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->actor?->name ?? 'Tài khoản đã xóa' }}</td>
                        <td><span class="badge text-bg-light">{{ $actionLabel }}</span></td>
                        <td>{{ $log->description }}</td>
                        <td>
                            @if($details->isNotEmpty())
                                <details>
                                    <summary class="small text-primary">Xem chi tiết thay đổi</summary>
                                    <div class="small mt-2">
                                        @foreach($details as $label => $values)
                                            <div class="fw-semibold text-dark mt-2">{{ $label }}</div>
                                            @foreach($values as $key => $value)
                                                <div class="d-flex justify-content-between gap-3 border-bottom py-1">
                                                    <span class="text-muted">{{ $translateField($key) }}</span>
                                                    <span class="text-end">
                                                        @if(is_array($value))
                                                            @foreach($value as $nestedKey => $nestedValue)
                                                                @if(is_array($nestedValue))
                                                                    <span class="d-block">Mã loại #{{ $nestedKey }}:
                                                                        @foreach($nestedValue as $detailKey => $detailValue)
                                                                            {{ $translateField($detailKey) }} {{ $translateValue($detailValue) }}@if(!$loop->last), @endif
                                                                        @endforeach
                                                                    </span>
                                                                @else
                                                                    <span class="d-block">{{ $translateField($nestedKey) }}: {{ $translateValue($nestedValue) }}</span>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            {{ $translateValue($value) }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Chưa có hoạt động nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())<div class="card-footer bg-white border-0 p-3">{{ $logs->links() }}</div>@endif
    </div>
</div>
@endsection
