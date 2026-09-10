@extends('layouts.app')
@section('title', 'Thêm Sản phẩm mới')
@section('content')
<div class="container">
<h2>Thêm sản phẩm mới</h2>
<form action="{{ route('admin.products.store') }}" method="POST">
@csrf
<div class="mb-3">
<label for="name" class="form-label">Tên sản phẩm</label>
<input type="text" class="form-control" id="name" name="name" required>

</div>
<div class="mb-3">
<label for="category_id" class="form-label">Danh mục</label>
<select class="form-control" id="category_id" name="category_id" required>
<option value="">-- Chọn danh mục --</option>
@foreach ($categories as $category)
<option value="{{ $category->id }}">{{ $category->name }}</option>
@endforeach
</select>
</div>
<div class="mb-3">
<label for="description" class="form-label">Mô tả</label>
<textarea class="form-control" id="description" name="description" rows="3"></textarea>
</div>
<div class="mb-3">
<label for="quantity" class="form-label">Số lượng</label>
<input type="number" class="form-control" id="quantity" name="quantity" required>
</div>
<div class="mb-3">
<label for="price" class="form-label">Giá</label>
<input type="number" step="0.01" class="form-control" id="price" name="price" required>
</div>
<button type="submit" class="btn btn-success">Lưu sản phẩm</button>
<a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại</a>
</form>
</div>
@endsection