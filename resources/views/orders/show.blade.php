@extends('layouts.app')
@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
@php
    $statusSteps = [
        'processing' => ['label' => 'Chờ xác nhận', 'icon' => 'bi-hourglass-split'],
        'confirmed' => ['label' => 'Đã xác nhận', 'icon' => 'bi-check2'],
        'packing' => ['label' => 'Đang đóng gói', 'icon' => 'bi-box-seam'],
        'shipping' => ['label' => 'Đang giao hàng', 'icon' => 'bi-truck'],
        'paid' => ['label' => 'Đã thanh toán', 'icon' => 'bi-credit-card'],
        'completed' => ['label' => 'Đã nhận hàng', 'icon' => 'bi-check2-circle'],
    ];
    $currentStep = array_search($order->status, array_keys($statusSteps), true);
    $currentStep = $currentStep === false ? 0 : $currentStep;
    $itemCount = $order->items->sum('quantity');
    $subtotal = $order->items->sum(fn ($item) => $item->price * $item->quantity);
@endphp

<div class="container py-4 order-detail-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="order-detail-eyebrow"><i class="bi bi-bag-heart me-1"></i> ALOHA BEAUTY / TRUNG TÂM ĐƠN HÀNG</span>
            <h3 class="fw-bold mb-1 storefront-title"><i class="bi bi-receipt text-primary me-2"></i>Đơn hàng #{{ $order->id }}</h3>
            <p class="text-muted mb-0">Đặt lúc {{ $order->created_at->format('d/m/Y \l\ú\c H:i') }}</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light border rounded-pill shadow-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> In đơn</button>
            <a href="{{ Auth::user()->role === 'admin' ? route('admin.orders.index') : route('orders.index') }}" class="btn btn-secondary rounded-pill shadow-sm"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
        </div>
    </div>

    <div class="order-hero-card mb-4">
        <div><span class="order-hero-label">TỔNG THANH TOÁN</span><strong>{{ number_format($order->total, 0, ',', '.') }} đ</strong><small>{{ $itemCount }} sản phẩm trong đơn</small></div>
        <div class="order-hero-meta"><span class="order-live-dot"></span><span>{{ $order->status === 'completed' ? 'Đơn hàng đã hoàn tất' : 'Đơn hàng đang được xử lý' }}</span></div>
    </div>

    @if($order->status !== 'cancelled')
        <div class="card border-0 shadow-sm rounded-4 mb-4 order-panel-card order-timeline-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4"><h5 class="fw-bold mb-0"><i class="bi bi-signpost-2 text-primary me-2"></i>Hành trình đơn hàng</h5><span class="small text-muted">Cập nhật theo trạng thái</span></div>
                <div class="order-timeline">
                    @foreach($statusSteps as $step => $stepData)
                        @php $stepIndex = array_search($step, array_keys($statusSteps), true); @endphp
                        <div class="order-timeline-step {{ $stepIndex <= $currentStep ? 'is-done' : '' }} {{ $step === $order->status ? 'is-current' : '' }}">
                            <span class="order-timeline-icon"><i class="bi {{ $stepData['icon'] }}"></i></span><strong>{{ $stepData['label'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="bi bi-x-circle me-2"></i>Đơn hàng này đã được hủy.</div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Thông tin đơn hàng -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 order-panel-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-file-text text-primary me-2"></i>Thông tin đơn hàng</h5>
                    <p class="mb-2"><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p class="mb-2">
                        <strong>Trạng thái:</strong>
                        @if(strtolower($order->status) == 'processing' || $order->status == 'Đang xử lý')
                            <span class="badge bg-warning text-dark rounded-pill px-3">Đang xử lý</span>
                        @elseif(strtolower($order->status) == 'confirmed')
                            <span class="badge bg-info text-dark rounded-pill px-3">Đã xác nhận đơn</span>
                        @elseif(strtolower($order->status) == 'packing')
                            <span class="badge bg-secondary rounded-pill px-3">Đang gói hàng</span>
                        @elseif(strtolower($order->status) == 'shipping')
                            <span class="badge bg-primary rounded-pill px-3">Đang vận chuyển</span>
                        @elseif(strtolower($order->status) == 'paid' || $order->status == 'Đã thanh toán')
                            <span class="badge bg-success rounded-pill px-3">Đã thanh toán</span>
                        @elseif(strtolower($order->status) == 'completed')
                            <span class="badge bg-success rounded-pill px-3">Đã nhận hàng</span>
                        @elseif(strtolower($order->status) == 'cancelled' || $order->status == 'Đã huỷ')
                            <span class="badge bg-danger rounded-pill px-3">Đã huỷ</span>
                        @else
                            <span class="badge bg-secondary rounded-pill px-3">{{ ucfirst($order->status) }}</span>
                        @endif
                    </p>
                    <p class="mb-2">
                        <strong>Phương thức thanh toán:</strong> 
                        {{ $order->payment_method == 'COD' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản Ngân hàng (PayOS)' }}
                    </p>
                    
                    @if($order->shipping_provider)
                        <p class="mb-2 mt-3"><strong>Đơn vị vận chuyển:</strong> <span class="badge bg-info text-dark">{{ $order->shipping_provider }}</span></p>
                    @endif
                    @if($order->shipping_date)
                        <p class="mb-2"><strong>Ngày giao dự kiến:</strong> <span class="text-primary fw-bold">{{ \Carbon\Carbon::parse($order->shipping_date)->format('d/m/Y') }}</span></p>
                    @endif

                    <div class="order-summary-box mt-3">
                        <div><span>Tạm tính sản phẩm</span><strong>{{ number_format($subtotal, 0, ',', '.') }} đ</strong></div>
                        <div><span>Phí dịch vụ</span><strong>{{ number_format(max(0, $order->total - $subtotal), 0, ',', '.') }} đ</strong></div>
                        <div class="order-summary-total"><span>Tổng thanh toán</span><strong>{{ number_format($order->total, 0, ',', '.') }} đ</strong></div>
                    </div>

                    @if($order->status !== 'completed' && $order->status !== 'cancelled')
                        <hr class="my-4" style="border-color: rgba(0,0,0,0.1);">
                        <form action="{{ route('orders.confirm_receipt', $order->id) }}" method="POST" class="d-grid animate__animated animate__fadeInUp">
                            @csrf
                            <button type="submit" class="btn text-white fw-bold py-2 shadow-sm" 
                                    style="background: linear-gradient(135deg, #00b09b, #96c93d); border-radius: 12px; letter-spacing: 0.5px;" 
                                    onclick="return confirm('Bạn xác nhận đã nhận được kiện hàng này nguyên vẹn chứ?')">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i> XÁC NHẬN ĐÃ NHẬN HÀNG
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Thông tin người nhận -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 order-panel-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-person-vcard text-primary me-2"></i>Thông tin người nhận</h5>
                    <div class="receiver-info"><div class="receiver-avatar">{{ strtoupper(substr($order->customer_name ?? Auth::user()->name, 0, 1)) }}</div><div><strong>{{ $order->customer_name ?? Auth::user()->name }}</strong><small>{{ $order->customer_phone ?? 'Chưa cập nhật' }}</small></div></div>
                    <div class="order-address mt-3"><i class="bi bi-geo-alt-fill text-danger me-2"></i><span>{{ $order->customer_address ?? 'Chưa cập nhật' }}</span><button type="button" class="btn btn-sm btn-link p-0 ms-auto" onclick="copyOrderText('{{ addslashes($order->customer_address ?? 'Chưa cập nhật') }}')" title="Sao chép địa chỉ"><i class="bi bi-copy"></i></button></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 order-panel-card">
        <div class="card-body p-0">
            <div class="d-flex justify-content-between align-items-center p-4 border-bottom"><h5 class="fw-bold mb-0"><i class="bi bi-bag-check text-primary me-2"></i>Sản phẩm đã đặt</h5><span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $itemCount }} món</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-3 ps-4 order-image-column">Hình ảnh</th>
                            <th class="py-3 text-start">Tên sản phẩm</th>
                            <th class="py-3">Đơn giá</th>
                            <th class="py-3">Số lượng</th>
                            <th class="py-3">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td class="ps-4">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="img-thumbnail rounded-3 shadow-sm storefront-thumb-image">
                                    @else
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted border shadow-sm storefront-thumb-placeholder">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-start">
                                    <strong class="d-block">{{ $item->product->name ?? 'Sản phẩm đã bị xóa' }}</strong>
                                    <small class="text-muted">{{ $item->product->category->name ?? 'Mỹ phẩm & chăm sóc cá nhân' }}</small>
                                    @if($item->variation)
                                        <small class="d-block text-primary">SKU: {{ $item->variation->sku ?: 'Chưa có SKU' }}{{ $item->variation->color ? ' · ' . $item->variation->color : '' }}{{ $item->variation->size_value ? ' · ' . rtrim(rtrim($item->variation->size_value, '0'), '.') . $item->variation->size_unit : '' }}</small>
                                    @endif

                                    @php $existingReview = $item->product ? $item->product->reviews->where('user_id', Auth::id())->first() : null; @endphp
                                    @if($existingReview)
                                        <div class="my-review-box mt-2">
                                            <div class="small fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>Bạn đã đánh giá sản phẩm này</div>
                                            <div class="text-warning">{{ str_repeat('★', $existingReview->rating) }}<span class="text-muted">{{ str_repeat('★', 5 - $existingReview->rating) }}</span></div>
                                            @if($existingReview->comment)<div class="small text-muted">{{ $existingReview->comment }}</div>@endif
                                            @if($existingReview->media_paths)<div class="d-flex gap-1 mt-1">@foreach($existingReview->media_paths as $path)<img src="{{ asset('storage/' . $path) }}" alt="Ảnh đánh giá" class="review-thumbnail">@endforeach</div>@endif
                                            <button type="button" class="btn btn-link btn-sm p-0 mt-1 text-decoration-none" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $item->product->id }}"><i class="bi bi-pencil-square me-1"></i>Chỉnh sửa / thêm ảnh</button>
                                        </div>
                                    @elseif(strtolower($order->status) == 'completed' && $item->product)
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill mt-2 fw-bold" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $item->product->id }}">
                                            <i class="bi bi-star-fill me-1"></i> Đánh giá sản phẩm
                                        </button>
                                    @endif
                                </td>
                                <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                <td class="fw-bold">{{ $item->quantity }}</td>
                                <td class="text-danger fw-bold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- KHU VỰC ĐỘC QUYỀN CỦA ADMIN -->
    @if(Auth::user()->role === 'admin')
    <div class="card border-primary shadow-lg rounded-4 mt-2 mb-4 order-payment-card">
        <div class="card-header bg-primary text-white fw-bold p-3 order-payment-header">
            <i class="bi bi-gear-fill me-2"></i>ĐIỀU PHỐI ĐƠN HÀNG (Chỉ dành cho Admin)
        </div>
        <div class="card-body p-4">
            <form action="{{ route('orders.update_status', $order->id) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">TRẠNG THÁI HIỆN TẠI</label>
                        <select name="status" class="form-select border-primary shadow-sm">
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Chờ xác nhận (Đang xử lý)</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận đơn</option>
                            <option value="packing" {{ $order->status == 'packing' ? 'selected' : '' }}>Đang gói hàng</option>
                            <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Đang vận chuyển</option>
                            <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Đã thanh toán (Hoàn thành)</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã huỷ</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">ĐƠN VỊ VẬN CHUYỂN</label>
                        <select name="shipping_provider" class="form-select border-primary shadow-sm">
                            <option value="">-- Chưa chỉ định --</option>
                            <option value="Giao Hàng Tiết Kiệm" {{ $order->shipping_provider == 'Giao Hàng Tiết Kiệm' ? 'selected' : '' }}>Giao Hàng Tiết Kiệm</option>
                            <option value="Viettel Post" {{ $order->shipping_provider == 'Viettel Post' ? 'selected' : '' }}>Viettel Post</option>
                            <option value="Shopee Express" {{ $order->shipping_provider == 'Shopee Express' ? 'selected' : '' }}>Shopee Express</option>
                            <option value="J&T Express" {{ $order->shipping_provider == 'J&T Express' ? 'selected' : '' }}>J&T Express</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">NGÀY VẬN CHUYỂN / GIAO HÀNG</label>
                        <input type="date" name="shipping_date" class="form-control border-primary shadow-sm" value="{{ $order->shipping_date }}">
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
                        <i class="bi bi-save2 me-2"></i>Lưu trạng thái
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<!-- ========================================================================= -->
<!-- KHU VỰC CHỨA CÁC MODAL ĐÁNH GIÁ (BẮT BUỘC ĐỂ Ở NGOÀI CÙNG) -->
<!-- ========================================================================= -->
@if(strtolower($order->status) == 'completed')
    @foreach($order->items as $item)
        @if($item->product)
            <div class="modal fade" id="reviewModal{{ $item->product->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow-lg">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold">Đánh giá sản phẩm</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        @php $existingReview = $item->product->reviews->where('user_id', Auth::id())->first(); @endphp
                        <form action="{{ $existingReview ? route('products.reviews.update', [$item->product, $existingReview]) : route('products.reviews.store', $item->product) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if($existingReview) @method('PATCH') @endif
                            <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            
                            <div class="modal-body text-center pt-2">
                                <img src="{{ asset('storage/' . $item->product->image) }}" width="70" class="rounded-3 mb-3 shadow-sm">
                                <h6 class="fw-bold mb-3">{{ $item->product->name }}</h6>
                                
                                <div class="star-rating-custom mb-3">
                                    <input type="radio" id="star5_{{ $item->product->id }}" name="rating" value="5" @checked($existingReview?->rating === 5) required />
                                    <label for="star5_{{ $item->product->id }}" title="Tuyệt vời"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star4_{{ $item->product->id }}" name="rating" value="4" @checked($existingReview?->rating === 4) />
                                    <label for="star4_{{ $item->product->id }}" title="Rất tốt"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star3_{{ $item->product->id }}" name="rating" value="3" @checked($existingReview?->rating === 3) />
                                    <label for="star3_{{ $item->product->id }}" title="Bình thường"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star2_{{ $item->product->id }}" name="rating" value="2" @checked($existingReview?->rating === 2) />
                                    <label for="star2_{{ $item->product->id }}" title="Tệ"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star1_{{ $item->product->id }}" name="rating" value="1" @checked($existingReview?->rating === 1) />
                                    <label for="star1_{{ $item->product->id }}" title="Rất tệ"><i class="bi bi-star-fill"></i></label>
                                </div>

                                <textarea name="comment" class="form-control rounded-3 bg-light border-0 p-3" rows="3" placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm này nhé! (Tùy chọn)">{{ old('comment', $existingReview?->comment) }}</textarea>
                                <div class="text-start mt-3">
                                    <label for="review-images-{{ $item->product->id }}" class="form-label fw-bold small"><i class="bi bi-images me-1"></i>Thêm ảnh (tối đa 3 ảnh)</label>
                                    <input type="file" name="images[]" id="review-images-{{ $item->product->id }}" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>
                                    <small class="text-muted">Mỗi ảnh tối đa 2MB. Review này còn {{ max(0, 3 - count($existingReview?->media_paths ?? [])) }} lượt ảnh.</small>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0">
                                <button type="submit" class="btn text-white w-100 rounded-pill fw-bold" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);">{{ $existingReview ? 'Cập nhật đánh giá' : 'Gửi Đánh Giá' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

<style>
    /* CSS hiệu ứng chọn sao đánh giá */
    .star-rating-custom { display: inline-flex; flex-direction: row-reverse; gap: 5px; }
    .star-rating-custom input { display: none; }
    .star-rating-custom label { color: #e4e5e9; font-size: 2rem; cursor: pointer; transition: color 0.2s; }
    .star-rating-custom input:checked ~ label,
    .star-rating-custom label:hover,
    .star-rating-custom label:hover ~ label { color: #ffc107; }
    .my-review-box { padding: .45rem .65rem; background: #f0fbf5; border: 1px solid #cceedd; border-radius: .5rem; }
    .review-thumbnail { width: 42px; height: 42px; object-fit: cover; border-radius: .35rem; border: 1px solid #dbe4ef; }
</style>

<script>
function copyOrderText(value) {
    navigator.clipboard.writeText(value).then(function () {
        const notice = document.createElement('div');
        notice.className = 'order-copy-notice';
        notice.innerHTML = '<i class="bi bi-check-circle me-2"></i>Đã sao chép thông tin';
        document.body.appendChild(notice);
        setTimeout(() => notice.remove(), 1800);
    });
}
</script>
@endsection