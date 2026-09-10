@extends('admin.layouts.app')
@section('title', 'Thêm danh mục mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Thêm Danh Mục Mới</h4>
            </div>
            
            <div class="card-body">
                <!-- BẮT BUỘC PHẢI CÓ enctype ĐỂ UPLOAD ĐƯỢC ẢNH -->
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Tên danh mục</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Nhập tên danh mục...">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Ô CHỌN ẢNH -->
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold">Hình ảnh danh mục</label>
                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" class="btn btn-success">Lưu danh mục</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection