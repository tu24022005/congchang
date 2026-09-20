@extends('layouts.app')
@section('title', 'Thêm Banner trang chủ')
@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Thêm Banner trang chủ</h2>
    <form action="{{ route('admin.home-banners.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.home-banners.form', ['banner' => null])
    </form>
</div>
@endsection
