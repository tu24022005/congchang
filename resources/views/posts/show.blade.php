@extends('layouts.app')
@section('title', $post->meta_title ?: $post->title)
@push('head')<meta name="description" content="{{ $post->meta_description ?: $post->excerpt }}">@endpush
@section('content')
<article class="container py-4" style="max-width:900px">
    <div class="text-center mb-4"><span class="text-primary fw-bold">{{ $post->category?->name ?? 'BLOG LÀM ĐẸP' }}</span><h1 class="display-5 fw-bold mt-2">{{ $post->title }}</h1><p class="text-muted">{{ $post->published_at?->format('d/m/Y H:i') }} @if($post->author) · {{ $post->author->name }} @endif</p></div>
    @if($post->featured_image)<img src="{{ asset('storage/'.$post->featured_image) }}" class="w-100 rounded-4 shadow-sm mb-4" style="max-height:460px;object-fit:cover" alt="{{ $post->title }}">@endif
    @if($post->excerpt)<p class="lead fw-semibold">{{ $post->excerpt }}</p>@endif
    <div class="post-content lh-lg">{!! $post->content !!}</div>
    @if($relatedPosts->isNotEmpty())<hr class="my-5"><h3 class="fw-bold mb-3">Bài viết liên quan</h3><div class="row g-3">@foreach($relatedPosts as $related)<div class="col-md-4"><a href="{{ route('posts.show', $related->slug) }}" class="text-decoration-none fw-bold text-dark">{{ $related->title }}</a></div>@endforeach</div>@endif
</article>
@endsection
