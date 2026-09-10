@extends('layouts.app')
@section('title', 'Thanh toán đơn hàng - Nguyễn Văn Tú')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm border-0 p-4 mx-auto checkout-card">
                <h3 class="text-danger mb-3">📱 Quét mã QR thanh toán</h3>
                
                <!-- Hiển thị thông tin cá nhân hóa -->
                <div class="alert alert-info py-2 mb-3">
                    <span class="d-block mb-1">Người thụ hưởng: <strong>NGUYỄN VĂN TÚ</strong></span>
                    <span class="d-block">Ngân hàng: <strong>Vietcombank (VCB)</strong></span>
                </div>

                <p class="text-muted mb-1">Số tiền cần thanh toán:</p>
                <h4 class="fw-bold text-success mb-3">{{ number_format($totalAmount, 0, ',', '.') }} đ</h4>
                
                <!-- Hiển thị ảnh mã QR thanh toán động từ API Server -->
                <div class="my-3">
                    <img src="{{ $qrCodeUrl }}" alt="Mã QR Thanh Toán Nguyễn Văn Tú" class="img-fluid border rounded p-2 shadow-sm checkout-qr-image">
                </div>
                
                <p class="small text-muted mb-4">
                    Sử dụng ứng dụng ngân hàng bất kỳ có hỗ trợ VietQR để quét mã. Nội dung và số tiền đã được điền tự động!
                </p>
                
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">&laquo; Quay lại giỏ hàng</a>
                    <a href="{{ route('orders.index') }}" class="btn btn-success fw-bold">Hoàn tất / Xem đơn hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection