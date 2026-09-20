@extends('layouts.app')

@section('title', 'Quản lý Banner trang chủ')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-images text-primary me-2"></i>Quản lý Banner trang chủ</h2>
            <p class="text-muted mb-0">Thêm, sửa, sắp xếp và bật/tắt banner hiển thị trên trang chủ.</p>
        </div>
        <a href="{{ route('admin.home-banners.create') }}" class="btn btn-primary rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Thêm banner
        </a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="row g-4">
        @forelse($banners as $banner)
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <img src="{{ $banner->image_source }}" alt="{{ $banner->alt_text ?: $banner->title }}" style="height:220px;object-fit:cover;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                @if($banner->badge)<span class="badge bg-warning text-dark mb-2">{{ $banner->badge }}</span>@endif
                                <h4 class="fw-bold mb-1">{{ $banner->title }}</h4>
                            </div>
                            <span class="badge {{ $banner->is_active ? 'bg-success' : 'bg-secondary' }} align-self-start">
                                {{ $banner->is_active ? 'Đang hiển thị' : 'Đang tắt' }}
                            </span>
                        </div>
                        <p class="text-muted mb-3">{{ $banner->description }}</p>
                        <div class="small text-muted mb-3">Thứ tự: <strong>{{ $banner->sort_order }}</strong></div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.home-banners.edit', $banner) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                            </a>
                            <form method="POST" action="{{ route('admin.home-banners.destroy', $banner) }}" onsubmit="return confirm('Xóa banner này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash me-1"></i> Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">Chưa có banner nào.</div></div>
        @endforelse
    </div>
</div>
@endsection
