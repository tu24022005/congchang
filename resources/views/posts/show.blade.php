@extends('layouts.app')
@section('title', ($post->meta_title ?: $post->title) . ' - Blog BeatyCare')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($post->meta_description ?: ($post->excerpt ?: $post->title)), 155))
@section('og_title', $post->title)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($post->meta_description ?: ($post->excerpt ?: $post->title)), 155))
@section('og_image', $post->featured_image ? asset('storage/' . $post->featured_image) : asset('images/og-default.svg'))
@section('og_type', 'article')
@section('canonical', route('posts.show', ['slug' => $post->slug]))

@section('structured_data')
@php
    $blogSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->title,
        'image' => [$post->featured_image ? asset('storage/' . $post->featured_image) : asset('images/og-default.svg')],
        'datePublished' => $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String(),
        'dateModified' => $post->updated_at ? $post->updated_at->toIso8601String() : $post->created_at->toIso8601String(),
        'description' => \Illuminate\Support\Str::limit(strip_tags($post->meta_description ?: ($post->excerpt ?: $post->title)), 200),
        'author' => [
            '@type' => 'Person',
            'name' => $post->author?->name ?? 'Ban biên tập Aloha Beauty',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('shop.seo.site_name', 'Aloha Beauty'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset(config('shop.seo.default_og_image', 'images/og-default.svg')),
            ],
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => route('posts.show', ['slug' => $post->slug]),
        ],
    ];

    $breadcrumbElements = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Trang chủ',
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Tin tức & Blog',
            'item' => route('posts.index'),
        ],
    ];
    $pos = 3;
    if ($post->category) {
        $breadcrumbElements[] = [
            '@type' => 'ListItem',
            'position' => $pos++,
            'name' => $post->category->name,
            'item' => route('posts.index', ['category' => $post->category->id]),
        ];
    }
    $breadcrumbElements[] = [
        '@type' => 'ListItem',
        'position' => $pos,
        'name' => $post->title,
        'item' => url()->current(),
    ];

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbElements,
    ];
@endphp
<template class="jsonld-template">
@json([$blogSchema, $breadcrumbSchema], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
</template>
@endsection

@section('content')
<article class="container py-4 view-inline-1">
    <div class="text-center mb-4"><span class="text-primary fw-bold">{{ $post->category?->name ?? 'BLOG LÀM ĐẸP' }}</span><h1 class="display-5 fw-bold mt-2">{{ $post->title }}</h1><p class="text-muted">{{ $post->published_at?->format('d/m/Y H:i') }} @if($post->author) · {{ $post->author->name }} @endif</p></div>
    @if($post->featured_image)<img src="{{ asset('storage/'.$post->featured_image) }}" class="w-100 rounded-4 shadow-sm mb-4 view-inline-2" alt="{{ $post->title }}" fetchpriority="high" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">@endif
    @if($post->excerpt)<p class="lead fw-semibold">{{ $post->excerpt }}</p>@endif
    <div class="post-content lh-lg">{!! $post->content !!}</div>
    @if($relatedPosts->isNotEmpty())<hr class="my-5"><h3 class="fw-bold mb-3">Bài viết liên quan</h3><div class="row g-3">@foreach($relatedPosts as $related)<div class="col-md-4"><a href="{{ route('posts.show', $related->slug) }}" class="text-decoration-none fw-bold text-dark">{{ $related->title }}</a></div>@endforeach</div>@endif
</article>
@endsection
