@extends('layouts.app')
@section('title', 'Về chúng tôi - Aloha Beauty | BeatyCare 🌸')
@section('meta_description', 'Khám phá câu chuyện thương hiệu Aloha Beauty (BeatyCare). Sứ mệnh tôn vinh vẻ đẹp tự nhiên, cam kết 100% mỹ phẩm chính hãng, đền bù 200% nếu phát hiện hàng giả.')
@section('canonical', route('pages.about'))

@section('content')
<div class="container py-5">
    <!-- HERO HEADER -->
    <div class="text-center mb-5 reveal-up">
        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
            <i class="bi bi-flower1 me-1"></i> Câu chuyện thương hiệu Aloha Beauty
        </span>
        <h1 class="display-5 fw-bold text-dark mb-3">Tôn vinh vẻ đẹp tự nhiên & rạng ngời</h1>
        <p class="lead text-muted mx-auto" style="max-width: 720px; line-height: 1.8;">
            BeatyCare ra đời với khát khao mang đến giải pháp chăm sóc làn da và sắc đẹp an toàn, minh bạch và dịu lành nhất cho phụ nữ Việt Nam.
        </p>
    </div>

    <!-- STATS HIGHLIGHT -->
    <div class="row g-4 mb-5 text-center reveal-stagger">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle">
                <div class="display-6 fw-bold text-danger mb-1">100%</div>
                <div class="fw-semibold text-dark">Chính hãng cam kết</div>
                <small class="text-muted">Đầy đủ hóa đơn & tem phụ</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle">
                <div class="display-6 fw-bold text-primary mb-1">50.000+</div>
                <div class="fw-semibold text-dark">Khách hàng tin chọn</div>
                <small class="text-muted">Hơn 98% đánh giá 5 sao</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle">
                <div class="display-6 fw-bold text-success mb-1">7 Ngày</div>
                <div class="fw-semibold text-dark">Đổi trả linh hoạt</div>
                <small class="text-muted">An tâm tuyệt đối khi mua</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle">
                <div class="display-6 fw-bold text-warning mb-1">24/7</div>
                <div class="fw-semibold text-dark">Tư vấn chuyên sâu</div>
                <small class="text-muted">Đồng hành cùng làn da bạn</small>
            </div>
        </div>
    </div>

    <!-- SỨ MỆNH & GIÁ TRỊ CỐT LÕI -->
    <div class="row g-4 align-items-center mb-5 reveal-up">
        <div class="col-lg-6">
            <div class="p-4 p-md-5 rounded-4 shadow-sm" style="background: linear-gradient(135deg, rgba(254, 242, 242, 0.7), rgba(255, 237, 213, 0.6)); border: 1px solid rgba(254, 205, 211, 0.4);">
                <span class="badge bg-danger rounded-pill px-3 py-1 mb-3">Sứ mệnh của chúng tôi</span>
                <h2 class="fw-bold mb-3">Vì làn da khỏe mạnh từ gốc</h2>
                <p class="text-secondary lh-lg mb-3">
                    Chúng tôi tin rằng mỹ phẩm không chỉ giúp che khuyết điểm mà là chiếc chìa khóa nuôi dưỡng sự tự tin vốn có. Aloha Beauty nói KHÔNG với các sản phẩm trôi nổi, kém chất lượng và corticoid độc hại.
                </p>
                <p class="text-secondary lh-lg mb-0">
                    Từng thỏi son, giọt serum hay hũ kem dưỡng tại BeatyCare đều được kiểm định nguồn gốc xuất xứ nghiêm ngặt từ các thương hiệu uy tín toàn cầu (Hàn Quốc, Nhật Bản, Pháp, Mỹ...).
                </p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-4 shadow-sm border border-light">
                    <div class="p-3 rounded-circle bg-danger-subtle text-danger fs-4 flex-shrink-0">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Cam kết bồi thường 200%</h5>
                        <p class="text-muted small mb-0">Nếu phát hiện bất kỳ sản phẩm hàng giả, hàng nhái nào được phân phối bởi Aloha Beauty.</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-4 shadow-sm border border-light">
                    <div class="p-3 rounded-circle bg-primary-subtle text-primary fs-4 flex-shrink-0">
                        <i class="bi bi-magic"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Tư vấn cá nhân hóa chu trình</h5>
                        <p class="text-muted small mb-0">Trợ lý ảo BeatyCare AI & đội ngũ chuyên viên sẵn sàng phân tích tình trạng da giúp bạn chọn đúng sản phẩm.</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-4 shadow-sm border border-light">
                    <div class="p-3 rounded-circle bg-success-subtle text-success fs-4 flex-shrink-0">
                        <i class="bi bi-box2-heart"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Đóng gói an toàn 3 lớp</h5>
                        <p class="text-muted small mb-0">Hộp carton cứng, màng bóng khí chống sốc và thiệp cảm ơn thơm ngát gửi gắm trọn vẹn tình cảm đến tay bạn.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA BẮT ĐẦU TRẢI NGHIỆM -->
    <div class="text-center p-5 rounded-4 shadow-sm reveal-up" style="background: linear-gradient(135deg, #fff1f2, #fdf2f8); border: 1px solid #fecdd3;">
        <h3 class="fw-bold text-dark mb-2">Sẵn sàng nâng niu làn da của bạn hôm nay?</h3>
        <p class="text-muted mb-4">Khám phá hàng ngàn ưu đãi flash sale và quà tặng độc quyền tại BeatyCare.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('products.index') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                <i class="bi bi-bag-heart me-2"></i>Mua sắm ngay
            </a>
            <a href="{{ route('pages.contact') }}" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-bold">
                <i class="bi bi-chat-dots me-2"></i>Liên hệ tư vấn
            </a>
        </div>
    </div>
</div>
@endsection
