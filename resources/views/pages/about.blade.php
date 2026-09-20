@extends('layouts.app')
@section('title', 'Giới thiệu - Aloha Beauty')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="text-primary fw-bold small text-uppercase">Về Aloha Beauty</span>
        <h1 class="fw-bold mt-2">Chăm sóc vẻ đẹp theo cách dịu dàng hơn</h1>
        <p class="text-muted mx-auto" style="max-width: 680px">Aloha Beauty mang đến những sản phẩm làm đẹp được chọn lọc để bạn tự tin chăm sóc bản thân mỗi ngày.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4"><div class="card h-100 border-0 shadow-sm p-4"><i class="bi bi-heart-fill text-danger fs-2 mb-3"></i><h4>Chọn lọc tận tâm</h4><p class="text-muted mb-0">Ưu tiên nguồn gốc rõ ràng, thông tin minh bạch và trải nghiệm an tâm cho khách hàng.</p></div></div>
        <div class="col-md-4"><div class="card h-100 border-0 shadow-sm p-4"><i class="bi bi-stars text-warning fs-2 mb-3"></i><h4>Đẹp theo nhu cầu</h4><p class="text-muted mb-0">Danh mục sản phẩm đa dạng cho chăm sóc da, tóc và cơ thể, phù hợp nhiều phong cách.</p></div></div>
        <div class="col-md-4"><div class="card h-100 border-0 shadow-sm p-4"><i class="bi bi-chat-heart text-primary fs-2 mb-3"></i><h4>Luôn lắng nghe</h4><p class="text-muted mb-0">Đội ngũ Aloha sẵn sàng tư vấn và đồng hành cùng bạn sau mỗi đơn hàng.</p></div></div>
    </div>
</div>
@endsection
