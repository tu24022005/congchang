@extends('layouts.app')
@section('title', 'Câu hỏi thường gặp - BeatyCare 🌸')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5"><span class="text-primary fw-bold small text-uppercase">FAQ</span><h1 class="fw-bold mt-2">Câu hỏi thường gặp</h1></div>
    <div class="accordion mx-auto" id="faqAccordion" style="max-width: 850px">
        @foreach([
            ['Bạn có thể thanh toán bằng hình thức nào?', 'BeatyCare 🌸 hỗ trợ thanh toán khi nhận hàng (COD) và thanh toán online qua PayOS.'],
            ['Bao lâu thì đơn hàng được giao?', 'Thời gian dự kiến là 2-5 ngày làm việc tùy khu vực và đơn vị vận chuyển.'],
            ['Tôi có thể lưu nhiều địa chỉ giao hàng không?', 'Có. Vào Tài khoản của tôi, mở Sổ địa chỉ giao hàng để thêm, sửa, xóa hoặc đặt địa chỉ mặc định.'],
            ['Làm thế nào để đổi trả sản phẩm?', 'Xem chi tiết tại trang Chính sách đổi trả và vận chuyển, hoặc liên hệ CSKH kèm mã đơn hàng.'],
            ['Tôi có thể theo dõi đơn hàng ở đâu?', 'Đăng nhập và mở mục Đơn hàng của tôi để xem trạng thái cập nhật của từng đơn.'],
        ] as $index => $item)
            <div class="accordion-item border-0 shadow-sm mb-2 rounded-3 overflow-hidden"><h2 class="accordion-header"><button class="accordion-button {{ $index ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">{{ $item[0] }}</button></h2><div id="faq{{ $index }}" class="accordion-collapse collapse {{ !$index ? 'show' : '' }}" data-bs-parent="#faqAccordion"><div class="accordion-body text-muted">{{ $item[1] }}</div></div></div>
        @endforeach
    </div>
</div>
@endsection
