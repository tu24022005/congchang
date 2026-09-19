@extends('layouts.app')
@section('title', 'Viết bài Blog')
@section('content')
<div class="admin-form-card"><div class="card-body p-4 p-lg-5">
<h2 class="fw-bold mb-4">Viết bài mới</h2>
<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">@csrf
    @include('admin.posts.form', ['post' => null])
    <div class="d-flex justify-content-between"><a href="{{ route('admin.posts.index') }}" class="btn btn-light">Hủy</a><button class="btn btn-primary">Lưu bài viết</button></div>
</form></div></div>
@endsection
