@extends('layouts.app')
@section('title', 'Liên hệ - Aloha Beauty | BeatyCare 🌸')
@section('meta_description', 'Liên hệ với BeatyCare (Aloha Beauty) qua hotline 0338 054 668, email contact@phungthanhtuc.com hoặc ghé thăm showroom để được trải nghiệm và tư vấn sắc đẹp trực tiếp.')
@section('canonical', route('pages.contact'))

@section('content')
<div class="container py-5">
    <div class="text-center mb-5 reveal-up">
        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold text-uppercase mb-2">
            <i class="bi bi-chat-heart me-1"></i> Trung tâm hỗ trợ khách hàng
        </span>
        <h1 class="display-6 fw-bold text-dark mt-2">BeatyCare 🌸 luôn sẵn sàng lắng nghe</h1>
        <p class="text-muted mx-auto view-inline-1">
            Dù bạn cần tư vấn chu trình dưỡng da hay thắc mắc về đơn hàng, đội ngũ Aloha Beauty luôn có mặt hỗ trợ nhanh chóng nhất.
        </p>
    </div>

    <div class="row g-4 mb-5">
        <!-- CỘT THÔNG TIN LIÊN HỆ -->
        <div class="col-lg-5 reveal-up">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Thông tin cửa hàng</h4>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="p-3 rounded-circle bg-danger-subtle text-danger fs-5 flex-shrink-0">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Showroom chính</div>
                        <p class="text-muted small mb-0">Tầng 1, Aloha Beauty Center, Phường Cầu Giấy, Hà Nội </p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="p-3 rounded-circle bg-success-subtle text-success fs-5 flex-shrink-0">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Hotline hỗ trợ & Đặt hàng</div>
                        <a href="tel:0338054668" class="text-decoration-none fw-bold text-success fs-5">0338 054 668</a>
                        <small class="d-block text-muted">Phục vụ 8:00 - 21:00 (Tất cả các ngày trong tuần)</small>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="p-3 rounded-circle bg-primary-subtle text-primary fs-5 flex-shrink-0">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Hòm thư điện tử</div>
                        <a href="mailto:contact@phungthanhtuc.com" class="text-decoration-none text-primary">contact@phungthanhtuc.com</a>
                        <small class="d-block text-muted">Phản hồi thư trong vòng 2-4 giờ làm việc</small>
                    </div>
                </div>

                <div class="p-3 rounded-3 bg-light border mt-auto">
                    <div class="fw-semibold text-dark mb-1"><i class="bi bi-clock-history me-1 text-primary"></i> Khung giờ trực tuyến:</div>
                    <div class="small text-muted d-flex justify-content-between">
                        <span>Thứ 2 - Thứ 7:</span>
                        <strong class="text-dark">08:00 - 21:30</strong>
                    </div>
                    <div class="small text-muted d-flex justify-content-between">
                        <span>Chủ nhật & Ngày lễ:</span>
                        <strong class="text-dark">09:00 - 18:00</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT FORM GỬI TIN NHẮN -->
        <div class="col-lg-7 reveal-up">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-white">
                <h4 class="fw-bold mb-2 text-dark"><i class="bi bi-send-fill text-primary me-2"></i>Gửi tin nhắn cho chúng tôi</h4>
                <p class="text-muted small mb-4">Điền thông tin bên dưới, chuyên viên tư vấn sẽ liên hệ lại ngay với bạn.</p>

                <form id="contact-form" onsubmit="handleContactSubmit(event)">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" id="contact-name" class="form-control rounded-3" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" id="contact-phone" class="form-control rounded-3" placeholder="0901 234 567" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Địa chỉ Email <span class="text-danger">*</span></label>
                            <input type="email" id="contact-email" class="form-control rounded-3" placeholder="ban@gmail.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Vấn đề cần hỗ trợ</label>
                            <select id="contact-topic" class="form-select rounded-3">
                                <option value="product_advisor">Tư vấn chọn mỹ phẩm phù hợp làn da</option>
                                <option value="order_status">Tra cứu / Thay đổi thông tin đơn hàng</option>
                                <option value="return_refund">Yêu cầu bảo hành / Đổi trả sản phẩm</option>
                                <option value="partnership">Hợp tác kinh doanh / Phân phối sỉ</option>
                                <option value="other">Ý kiến đóng góp khác</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Nội dung chi tiết <span class="text-danger">*</span></label>
                            <textarea id="contact-message" class="form-control rounded-3" rows="4" placeholder="Nhập câu hỏi hoặc mô tả yêu cầu của bạn..." required></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" id="btn-contact-submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold w-100 shadow-sm">
                                <i class="bi bi-paperplane-fill me-2"></i>Gửi thông tin liên hệ
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset_v('js/views/pages-contact-blade-php.js') }}" defer></script>
@endpush
@endsection
