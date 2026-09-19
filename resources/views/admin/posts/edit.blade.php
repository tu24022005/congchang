@extends('layouts.app')
@section('title', 'Sửa bài Blog')
@section('content')
<div class="admin-form-card"><div class="card-body p-4 p-lg-5">
<h2 class="fw-bold mb-4">Chỉnh sửa bài viết</h2>
<form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
    @include('admin.posts.form', ['post' => $post])
    <div class="d-flex justify-content-between"><a href="{{ route('admin.posts.index') }}" class="btn btn-light">Hủy</a><button class="btn btn-primary">Cập nhật</button></div>
</form></div></div>
@endsection
