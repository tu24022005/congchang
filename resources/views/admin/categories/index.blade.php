@extends('layouts.app')
@section('title', 'Quản lý Danh mục')

@section('content')
<div class="container-fluid py-4">
    <!-- Tiêu đề & Nút Thêm mới -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0"><i class="bi bi-tags text-success me-2"></i>Quản lý Danh mục</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-circle me-1"></i> Thêm mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bảng Dữ liệu -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3" width="5%">STT</th>
                            <th class="py-3" width="15%">Hình ảnh</th>
                            <th class="py-3 text-start">Tên Danh mục</th>
                            <th class="py-3" width="20%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $key => $category)
                            <tr>
                                <td class="fw-bold text-muted">{{ $key + 1 }}</td>
                                <td>
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-thumbnail rounded-3 shadow-sm admin-thumb-image">
                                    @else
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted admin-thumb-placeholder">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-start fw-semibold fs-6 text-dark">{{ $category->name }}</td>
                                <td>
                                    <!-- Nút Hành động dạng Icon thu gọn -->
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3" title="Sửa">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i> Chưa có danh mục nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection