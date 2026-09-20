@extends('layouts.app')
@section('title', 'Chỉnh sửa Banner trang chủ')
@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Chỉnh sửa Banner trang chủ</h2>
    <form action="{{ route('admin.home-banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.home-banners.form', ['formAction' => route('admin.home-banners.update', $banner)])
    </form>
</div>
@endsection
