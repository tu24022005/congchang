<div class="product-advisor" data-product-advisor>
    <button type="button" class="product-advisor-toggle" data-advisor-toggle aria-label="Mở tư vấn chọn sản phẩm">
        <i class="bi bi-stars"></i><span>Tư vấn da</span>
    </button>
    <section class="product-advisor-panel d-none" data-advisor-panel aria-label="Tư vấn chọn sản phẩm">
        <div class="product-advisor-heading">
            <div><strong><i class="bi bi-stars me-1"></i>Aloha Skin Guide</strong><small>Tư vấn chọn sản phẩm theo loại da</small></div>
            <button type="button" class="btn-close" data-advisor-close aria-label="Đóng"></button>
        </div>
        <div class="product-advisor-messages" data-advisor-messages>
            <div class="advisor-message advisor-bot">Bạn thuộc loại da nào và đang muốn cải thiện điều gì?</div>
        </div>
        <form data-advisor-form>
            @csrf
            <select name="skin_type" class="form-select form-select-sm mb-2" required>
                <option value="">Chọn loại da</option>
                <option value="dry">Da khô</option>
                <option value="oily">Da dầu</option>
                <option value="combination">Da hỗn hợp</option>
                <option value="sensitive">Da nhạy cảm</option>
                <option value="normal">Da thường</option>
            </select>
            <div class="input-group input-group-sm">
                <input name="need" class="form-control" placeholder="Ví dụ: cấp ẩm, trị mụn..." required>
                <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i></button>
            </div>
        </form>
    </section>
</div>
<link rel="stylesheet" href="{{ asset('css/product-advisor.css') }}">
<script src="{{ asset('js/product-advisor.js') }}"></script>
