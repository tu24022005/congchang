@extends('layouts.app')
@section('title', 'Danh mục Blog')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><h2 class="fw-bold">Danh mục Blog</h2><a href="{{ route('admin.posts.index') }}" class="btn btn-light">Quay lại bài viết</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="row g-4"><div class="col-lg-4"><div class="card border-0 shadow-sm rounded-4"><div class="card-body"><h5 class="fw-bold">Thêm danh mục</h5><form action="{{ route('admin.post-categories.store') }}" method="POST">@csrf
<label class="form-label">Tên danh mục</label><input name="name" class="form-control mb-2" required><label class="form-label">Mô tả</label><textarea name="description" class="form-control mb-3" rows="3"></textarea><button class="btn btn-primary w-100">Thêm danh mục</button></form></div></div></div>
<div class="col-lg-8"><div class="card border-0 shadow-sm rounded-4 overflow-hidden"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Tên</th><th>Số bài</th><th></th></tr></thead><tbody>@forelse($categories as $category)<tr><td><strong>{{ $category->name }}</strong><br><small class="text-muted">{{ $category->slug }}</small></td><td>{{ $category->posts_count }}</td><td class="text-end"><form action="{{ route('admin.post-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Xóa danh mục này?');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Xóa</button></form></td></tr>@empty<tr><td colspan="3" class="text-center py-4">Chưa có danh mục.</td></tr>@endforelse</tbody></table></div></div></div>
@endsection
