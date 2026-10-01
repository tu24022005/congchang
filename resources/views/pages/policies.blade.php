@extends('layouts.app')
@section('title', 'Chính sách mua hàng & Bảo mật - Aloha Beauty | BeatyCare 🌸')
@section('meta_description', 'Xem chi tiết chính sách đổi trả hoàn tiền 7 ngày, miễn phí vận chuyển từ 500.000đ, chính sách đồng kiểm và bảo mật thông tin tại BeatyCare (Aloha Beauty).')
@section('canonical', route('pages.policies'))

@section('content')
<div class="container py-5">
    <!-- TIÊU ĐỀ TRANG -->
    <div class="text-center mb-5 reveal-up">
        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold text-uppercase mb-2">
            <i class="bi bi-shield-check me-1"></i> Quyền lợi khách hàng
        </span>
        <h1 class="display-6 fw-bold text-dark mt-2">Chính sách minh bạch tại BeatyCare 🌸</h1>
        <p class="text-muted mx-auto" style="max-width: 650px;">
            Mọi quy định tại Aloha Beauty đều hướng đến mục tiêu đảm bảo bạn có trải nghiệm mua sắm mỹ phẩm an tâm, hài lòng và trọn vẹn nhất.
        </p>
    </div>

    <div class="row g-4 mb-5">
        <!-- 1. CHÍNH SÁCH ĐỔI TRẢ & HOÀN TIỀN -->
        <div class="col-lg-6 reveal-up">
            <article class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-white">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 rounded-circle bg-danger-subtle text-danger fs-4 flex-shrink-0">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Chính sách đổi trả & Hoàn tiền</h4>
                        <small class="text-muted">Áp dụng trong vòng 7 ngày kể từ khi nhận hàng</small>
                    </div>
                </div>
                <hr class="my-3 opacity-25">
                <ul class="text-secondary lh-lg mb-0 ps-3">
                    <li><strong>Điều kiện đổi trả:</strong> Sản phẩm còn nguyên tem niêm phong, chưa qua sử dụng, còn đầy đủ vỏ hộp, phụ kiện và quà tặng kèm (nếu có).</li>
                    <li><strong>Trường hợp được đổi trả miễn phí:</strong> Sản phẩm bị vỡ, hỏng hóc do vận chuyển; giao sai phân loại/mẫu mã; sản phẩm có lỗi từ nhà sản xuất.</li>
                    <li><strong>Cam kết dị ứng mỹ phẩm:</strong> Nếu có chỉ định kích ứng từ bác sĩ da liễu trong 3 ngày đầu sử dụng, BeatyCare hỗ trợ thu hồi và đổi sản phẩm phù hợp.</li>
                    <li><strong>Thời gian hoàn tiền:</strong> Từ 1 - 3 ngày làm việc qua tài khoản ngân hàng sau khi kho tiếp nhận và thẩm định hàng hoàn.</li>
                </ul>
            </article>
        </div>

        <!-- 2. CHÍNH SÁCH VẬN CHUYỂN & GIAO HÀNG -->
        <div class="col-lg-6 reveal-up">
            <article class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-white">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 rounded-circle bg-success-subtle text-success fs-4 flex-shrink-0">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Chính sách vận chuyển</h4>
                        <small class="text-muted">Giao hàng toàn quốc - Nhanh chóng & An toàn</small>
                    </div>
                </div>
                <hr class="my-3 opacity-25">
                <ul class="text-secondary lh-lg mb-0 ps-3">
                    <li><strong>Miễn phí vận chuyển (Freeship):</strong> Tự động áp dụng cho mọi đơn hàng có giá trị từ <strong>{{ number_format(config('shop.free_shipping_threshold', 500000), 0, ',', '.') }}đ</strong> trên toàn quốc.</li>
                    <li><strong>Thời gian giao hàng tiêu chuẩn:</strong>
                        <ul class="mt-1 ps-3">
                            <li>Nội thành TP.HCM / Hà Nội: 1 - 2 ngày làm việc.</li>
                            <li>Các tỉnh thành khác: 2 - 4 ngày làm việc.</li>
                        </ul>
                    </li>
                    <li><strong>Đối tác vận chuyển uy tín:</strong> Giao Hàng Nhanh (GHN), Giao Hàng Tiết Kiệm (GHTK), Viettel Post với mã vận đơn tra cứu trực tuyến.</li>
                    <li><strong>Đóng gói chuyên dụng:</strong> Chống sốc 3 lớp bọt khí và dán băng keo niêm phong thương hiệu BeatyCare.</li>
                </ul>
            </article>
        </div>

        <!-- 3. CHÍNH SÁCH ĐỒNG KIỂM KHI NHẬN HÀNG -->
        <div class="col-lg-6 reveal-up">
            <article class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-white">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 rounded-circle bg-warning-subtle text-warning-emphasis fs-4 flex-shrink-0">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Quy định đồng kiểm khi nhận hàng</h4>
                        <small class="text-muted">Quyền lợi kiểm tra sản phẩm trước khi thanh toán</small>
                    </div>
                </div>
                <hr class="my-3 opacity-25">
                <ul class="text-secondary lh-lg mb-0 ps-3">
                    <li>Khách hàng được quyền <strong>mở kiện hàng bên ngoài</strong> để kiểm tra số lượng sản phẩm, quà tặng và tình trạng vật lý có bị đổ vỡ hay không trước khi thanh toán COD cho shipper.</li>
                    <li>Không hỗ trợ thử/test sản phẩm (không xé tem niêm phong mỹ phẩm, không bôi thử lên da) khi chưa thanh toán.</li>
                    <li>Nếu phát hiện hàng bị móp méo nặng hoặc thiếu món, quý khách vui lòng từ chối nhận hoặc chụp ảnh biên bản cùng shipper và gọi ngay Hotline <strong>0338 054 668</strong>.</li>
                </ul>
            </article>
        </div>

        <!-- 4. CHÍNH SÁCH BẢO MẬT THÔNG TIN -->
        <div class="col-lg-6 reveal-up">
            <article class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-white">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 rounded-circle bg-primary-subtle text-primary fs-4 flex-shrink-0">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Bảo mật thông tin khách hàng</h4>
                        <small class="text-muted">Tuân thủ nghiêm ngặt quyền riêng tư cá nhân</small>
                    </div>
                </div>
                <hr class="my-3 opacity-25">
                <ul class="text-secondary lh-lg mb-0 ps-3">
                    <li><strong>Thu thập thông tin:</strong> Tên, số điện thoại, email và địa chỉ nhận hàng chỉ được sử dụng cho mục đích giao hàng và chăm sóc hậu mãi.</li>
                    <li><strong>Tuyệt đối không chia sẻ:</strong> BeatyCare cam kết không bán, chia sẻ dữ liệu khách hàng cho bên thứ ba vì bất kỳ mục đích quảng cáo rác nào.</li>
                    <li><strong>Mã hóa an toàn:</strong> Dữ liệu thanh toán ngân hàng được bảo mật theo tiêu chuẩn quốc tế và mã hóa qua cổng PayOS.</li>
                </ul>
            </article>
        </div>
    </div>
</div>
@endsection
