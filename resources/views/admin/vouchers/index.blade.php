@extends('layouts.app')
@section('title', 'Quản lý Mã giảm giá')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-danger mb-0"><i class="bi bi-ticket-perforated-fill me-2"></i>Quản lý Voucher</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- FORM TẠO MÃ MỚI BÊN TRÁI -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 admin-panel-card">
                <div class="card-header bg-white border-bottom p-3">
                    <h5 class="fw-bold mb-0 text-primary">Tạo mã mới</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.vouchers.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mã Voucher (Ví dụ: TET2026)</label>
                            <input type="text" name="code" class="form-control text-uppercase" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Loại giảm giá</label>
                            <select name="type" class="form-select">
                                <option value="fixed">Giảm tiền mặt (VNĐ)</option>
                                <option value="percent">Giảm theo phần trăm (%)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mức giảm (Số tiền hoặc %)</label>
                            <input type="number" name="value" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Đơn tối thiểu để áp dụng</label>
                            <input type="number" name="min_order_value" class="form-control" value="0" required>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Số lượng mã</label>
                                <input type="number" name="usage_limit" class="form-control" min="1" placeholder="Không giới hạn">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Hạn sử dụng</label>
                                <input type="date" name="expires_at" class="form-control" min="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Thêm Voucher</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- DANH SÁCH MÃ BÊN PHẢI -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 admin-panel-card">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-3">Mã Code</th>
                                <th class="py-3">Mức giảm</th>
                                <th class="py-3">Đơn tối thiểu</th>
                                <th class="py-3">Số lượng</th>
                                <th class="py-3">Trạng thái</th>
                                <th class="py-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $voucher)
                                <tr>
                                    <td class="fw-bold text-success fs-5">{{ $voucher->code }}</td>
                                    <td class="fw-bold text-danger">
                                        {{ $voucher->type == 'fixed' ? number_format($voucher->value, 0, ',', '.') . ' đ' : $voucher->value . '%' }}
                                    </td>
                                    <td>{{ number_format($voucher->min_order_value, 0, ',', '.') }} đ</td>
                                    <td>
                                        <strong>{{ $voucher->used_count }}</strong>
                                        <span class="text-muted">/ {{ $voucher->usage_limit ?? '∞' }}</span>
                                    </td>
                                    <td>
                                        @if(!$voucher->isAvailable())
                                            <span class="badge bg-secondary">Đã đóng</span>
                                        @elseif($voucher->expires_at)
                                            <span class="badge bg-success">Đến {{ $voucher->expires_at->format('d/m/Y') }}</span>
                                        @else
                                            <span class="badge bg-success">Đang chạy</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- ĐÃ SỬA CHỖ NÀY THÀNH admin.vouchers.destroy -->
                                        <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-4 text-muted">Chưa có mã giảm giá nào</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection