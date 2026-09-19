@extends('layouts.app')
@section('title', 'Thành viên')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><span class="text-uppercase small text-primary fw-bold">QUẢN LÝ KHÁCH HÀNG</span><h2 class="fw-bold mb-1">Thành viên</h2><p class="text-muted mb-0">Theo dõi tài khoản, đơn hàng và điểm thưởng của khách hàng.</p></div>
    </div>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form method="GET" class="mb-3"><div class="input-group"><input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm theo tên hoặc email"><button class="btn btn-primary">Tìm kiếm</button></div></form>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Thành viên</th><th>Email</th><th>Hạng</th><th>Doanh số hoàn thành</th><th>Điểm hiện có</th><th>Số đơn</th><th>Ngày tham gia</th><th></th></tr></thead>
                    <tbody>
                    @forelse($members as $member)
                        <tr>
                            @php($memberTier = \App\Models\User::membershipTierFor($member->completed_spend ?? 0))
                            <td class="fw-semibold">{{ $member->name }}</td>
                            <td class="text-muted">{{ $member->email }}</td>
                            <td><span class="badge bg-light {{ $memberTier['class'] }}">{{ $memberTier['name'] }}</span></td>
                            <td>{{ number_format($member->completed_spend ?? 0, 0, ',', '.') }} đ</td>
                            <td><span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">{{ number_format($member->loyalty_points) }} điểm</span></td>
                            <td>{{ $member->orders_count }}</td>
                            <td>{{ $member->created_at?->format('d/m/Y') }}</td>
                            <td><a href="{{ route('admin.members.show', $member) }}" class="btn btn-sm btn-outline-primary rounded-pill">Xem chi tiết</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Chưa có thành viên.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $members->links() }}
        </div>
    </div>
</div>
@endsection
