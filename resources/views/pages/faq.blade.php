@extends('layouts.app')
@section('title', 'Câu hỏi thường gặp - BeatyCare 🌸')
@section('meta_description', 'Giải đáp các thắc mắc thường gặp về đặt hàng, thanh toán COD, giao hàng toàn quốc và chính sách tại BeatyCare (Aloha Beauty).')
@section('canonical', route('pages.faq'))

@php
    $faqItems = [
        ['q' => 'Bạn có thể thanh toán bằng hình thức nào?', 'a' => 'BeatyCare 🌸 hỗ trợ thanh toán khi nhận hàng (COD) và thanh toán online an toàn qua cổng PayOS.'],
        ['q' => 'Bao lâu thì đơn hàng được giao?', 'a' => 'Thời gian dự kiến là 2-5 ngày làm việc tùy khu vực và đơn vị vận chuyển.'],
        ['q' => 'Tôi có thể lưu nhiều địa chỉ giao hàng không?', 'a' => 'Có. Vào Tài khoản của tôi, mở Sổ địa chỉ giao hàng để thêm, sửa, xóa hoặc đặt địa chỉ mặc định.'],
        ['q' => 'Làm thế nào để đổi trả sản phẩm?', 'a' => 'Xem chi tiết tại trang Chính sách đổi trả và vận chuyển, hoặc liên hệ bộ phận CSKH kèm mã đơn hàng trong vòng 7 ngày kể từ khi nhận hàng.'],
        ['q' => 'Tôi có thể theo dõi đơn hàng ở đâu?', 'a' => 'Đăng nhập và mở mục Đơn hàng của tôi để xem trạng thái cập nhật thời gian thực của từng đơn hàng.'],
    ];

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(function ($item) {
            return [
                '@type' => 'Question',
                'name' => $item['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['a'],
                ],
            ];
        }, $faqItems),
    ];
@endphp

@section('structured_data')
<template class="jsonld-template">
@json($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
</template>
@endsection

@section('content')
<div class="container py-5">
    <div class="text-center mb-5"><span class="text-primary fw-bold small text-uppercase">FAQ</span><h1 class="fw-bold mt-2">Câu hỏi thường gặp</h1></div>
    <div class="accordion mx-auto" id="faqAccordion" class="view-inline-1">
        @foreach($faqItems as $index => $item)
            <div class="accordion-item border-0 shadow-sm mb-2 rounded-3 overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $index ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                        {{ $item['q'] }}
                    </button>
                </h2>
                <div id="faq{{ $index }}" class="accordion-collapse collapse {{ !$index ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">{{ $item['a'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
