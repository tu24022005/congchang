@extends('admin.layouts.app')
@section('title', 'Danh sách đăng ký nhận tin')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Đăng ký nhận tin (Newsletter)</h1>
            <p class="text-muted small mb-0">Danh sách khách hàng đăng ký nhận thông tin ưu đãi</p>
        </div>
        <a href="{{ route('admin.newsletter.export') }}" class="btn btn-outline-success rounded-pill">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Xuất file CSV
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Trạng thái</th>
                        <th>Ngày đăng ký</th>
                        <th>Ngày xác nhận</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $s)
                        <tr>
                            <td>#{{ $s->id }}</td>
                            <td class="fw-semibold">{{ $s->email }}</td>
                            <td>
                                @if($s->isConfirmed())
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">Đã xác nhận</span>
                                @elseif($s->unsubscribed_at)
                                    <span class="badge bg-secondary rounded-pill px-3">Đã hủy</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3">Chờ xác nhận</span>
                                @endif
                            </td>
                            <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $s->confirmed_at ? $s->confirmed_at->format('d/m/Y H:i') : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Chưa có ai đăng ký nhận tin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscribers->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $subscribers->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
