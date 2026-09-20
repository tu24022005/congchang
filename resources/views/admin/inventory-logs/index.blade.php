@extends('layouts.app')

@section('title', 'Lịch sử nhập xuất kho')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Lịch sử nhập xuất kho</h2>
    </div>

    <form method="GET" class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Sản phẩm</label>
                <select name="product_id" class="form-select">
                    <option value="">Tất cả sản phẩm</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Loại giao dịch</label>
                <select name="type" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="in" @selected(request('type') === 'in')>Nhập kho</option>
                    <option value="out" @selected(request('type') === 'out')>Xuất kho</option>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Lọc</button></div>
            <div class="col-md-2"><a href="{{ route('admin.inventory-logs.index') }}" class="btn btn-outline-secondary w-100">Xóa lọc</a></div>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Thời gian</th><th>Sản phẩm / biến thể</th><th>Loại</th><th>Thay đổi</th><th>Tồn trước</th><th>Tồn sau</th><th>Lý do</th><th>Người thao tác</th></tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $log->product?->name ?? 'Sản phẩm đã xóa' }}</strong><small class="d-block text-muted">{{ $log->variation?->sku ?: ($log->variation ? 'Mã loại #' . $log->variation->id : 'Biến thể đã xóa') }}</small></td>
                            <td><span class="badge {{ $log->type === 'in' ? 'bg-success' : 'bg-danger' }}">{{ $log->type === 'in' ? 'Nhập' : 'Xuất' }}</span></td>
                            <td class="fw-bold {{ $log->quantity > 0 ? 'text-success' : 'text-danger' }}">{{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}</td>
                            <td>{{ $log->stock_before }}</td><td>{{ $log->stock_after }}</td>
                            <td>{{ $log->reason }}@if($log->reference) <small class="d-block text-muted">#{{ $log->reference_id }}</small>@endif</td>
                            <td>{{ $log->user?->name ?? 'Hệ thống' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Chưa có lịch sử tồn kho.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
