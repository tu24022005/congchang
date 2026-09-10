@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa danh mục')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Chỉnh sửa Danh Mục</h4>
            </div>
            
            <div class="card-body">
                <!-- BẮT BUỘC PHẢI CÓ enctype ĐỂ GỬI ĐƯỢC FILE ẢNH -->
                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Tên danh mục</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- HIỂN THỊ ẢNH CŨ VÀ Ô CHỌN ẢNH MỚI -->
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold">Hình ảnh danh mục</label>
                        
                        @if($category->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $category->image) }}" alt="Ảnh hiện tại" width="100" class="rounded border">
                                <p class="text-muted small mb-0">Ảnh hiện tại</p>
                            </div>
                        @endif

                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection