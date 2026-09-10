@extends('layouts.app')
@section('title', 'Sửa Sản phẩm')
@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Chỉnh sửa sản phẩm</h2>

    <!-- ĐÃ SỬA: Thêm enctype="multipart/form-data" vào đây -->
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Tên sản phẩm</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label fw-bold">Danh mục</label>
            <select class="form-control" id="category_id" name="category_id" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- ĐÃ THÊM: Khu vực upload Ảnh chính -->
        <div class="mb-3">
            <label for="image" class="form-label fw-bold">Hình ảnh đại diện chính</label>
            @if($product->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="Ảnh hiện tại" width="120" class="rounded border shadow-sm">
                </div>
            @endif
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>

        <!-- ĐÃ ĐƯA VÀO TRONG FORM: Khu vực Bộ sưu tập ảnh -->
        <div class="mb-3 p-3 bg-light border rounded">
            <label class="form-label fw-bold text-primary">Thêm ảnh vào Bộ sưu tập (Có thể chọn nhiều ảnh cùng lúc)</label>
            <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
            <small class="text-muted fst-italic">Giữ phím Ctrl (hoặc kéo chuột) để chọn nhiều file cùng lúc.</small>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Mô tả</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $product->description }}</textarea>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label fw-bold">Số lượng</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $product->quantity }}" required min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label fw-bold">Giá</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ $product->price }}" required min="0">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary px-4">Cập nhật</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary px-4 ms-2">Quay lại</a>
        </div>
    </form>
</div>
@endsection