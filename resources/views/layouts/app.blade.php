<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aloha Beauty')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @stack('head')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>
<body>

    <!-- THANH ĐIỀU HƯỚNG GỌN GÀNG -->
    <nav class="navbar navbar-expand-lg glass-navbar shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <i class="bi bi-flower1"></i> Aloha Beauty
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- CỤM BÊN TRÁI: DÀNH CHO KHÁCH -->
                <ul class="navbar-nav me-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link text-nowrap fw-semibold {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            Trang chủ
                        </a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link text-nowrap fw-semibold {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            Sản phẩm
                        </a>
                    </li>
                </ul>

                <!-- THANH TÌM KIẾM TRUNG TÂM CO GỢI Ý (LIVE SEARCH) -->
                <form action="{{ route('products.index') }}" method="GET" class="d-flex mx-lg-4 my-3 my-lg-0 flex-grow-1 position-relative live-search-form">
                    <div class="input-group shadow-sm live-search-group">
                        <span class="input-group-text bg-white border-0 text-dark ps-3 pe-2">
                            <i class="bi bi-search fw-bold search-icon"></i>
                        </span>
                        <input type="text" name="search" id="live-search-input" class="form-control border-0 shadow-none bg-white px-2 live-search-input" placeholder="Tìm kiếm mỹ phẩm, chăm sóc da..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                    
                    <!-- Khung Dropdown chứa kết quả gợi ý -->
                    <div id="search-suggestions" class="position-absolute w-100 bg-white shadow-lg rounded-4 d-none search-suggestions">
                        <!-- Kết quả JS sẽ đổ vào đây -->
                    </div>
                </form>

                <!-- CỤM BÊN PHẢI: TÀI KHOẢN & GIỎ HÀNG -->
                <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                        <li class="nav-item me-2"><a class="nav-link text-nowrap fw-semibold" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập</a></li>
                        <li class="nav-item"><a class="nav-link text-nowrap fw-semibold" href="{{ route('register') }}"><i class="bi bi-person-plus me-1"></i> Đăng ký</a></li>
                    @else
                        <!-- MENU QUẢN TRỊ (CHỈ HIỂN THỊ VỚI ADMIN) -->
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item dropdown me-4">
                                <a class="nav-link dropdown-toggle text-danger fw-bold text-nowrap" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-shield-lock fs-5 me-1"></i> Quản trị
                                </a>
                                <ul class="dropdown-menu border-0 shadow-sm">
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 text-primary me-2"></i>Tổng quan</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.orders.index') }}"><i class="bi bi-truck text-danger me-2"></i>Quản lý Đơn hàng</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.products.index') }}"><i class="bi bi-box-seam text-success me-2"></i>Kho sản phẩm</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags text-warning me-2"></i>Danh mục</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.vouchers.index') }}"><i class="bi bi-ticket-perforated text-success me-2"></i>Mã giảm giá</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.reports.index') }}"><i class="bi bi-bar-chart-line text-info me-2"></i>Báo cáo</a></li>
                                </ul>
                            </li>
                        @else
                            <!-- ĐƠN HÀNG CỦA KHÁCH -->
                            <li class="nav-item me-4">
                                <a class="nav-link text-nowrap text-dark fw-bold" href="{{ route('orders.index') }}">
                                    <i class="bi bi-receipt fs-5 me-1"></i> Đơn hàng
                                </a>
                            </li>
                        @endif

                        <!-- GIỎ HÀNG CHUNG -->
                        <li class="nav-item me-4">
                            <a class="nav-link text-nowrap position-relative fw-semibold" href="{{ route('cart.index') }}">
                                <i class="bi bi-cart3 fs-5 me-1"></i> Giỏ hàng
                                @php $cartCount = array_sum(array_column(session('cart', []), 'quantity')); @endphp
                                @if($cartCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count-badge">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </li>

                        <!-- USER PROFILE -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle active text-nowrap fw-bold" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5 me-1 text-primary"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                <li><a class="dropdown-item fw-bold py-2" href="{{ route('account') }}"><i class="bi bi-person-vcard text-primary me-2"></i> Tài khoản của tôi</a></li>
                                <li><a class="dropdown-item fw-bold py-2" href="{{ route('password.change') }}"><i class="bi bi-key text-warning me-2"></i> Đổi mật khẩu</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger fw-bold py-2" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- NỘI DUNG CHÍNH -->
    <main class="container py-4 flex-grow-1">
        @yield('content')
    </main>

    <!-- CHÂN TRANG (FOOTER) -->
    <footer class="glass-footer mt-5">
        <div class="container">
            <div class="footer-promise-row">
                <div class="footer-promise"><i class="bi bi-patch-check-fill"></i><div><strong>Chính hãng chọn lọc</strong><small>Nguồn gốc rõ ràng</small></div></div>
                <div class="footer-promise"><i class="bi bi-box-seam-fill"></i><div><strong>Đóng gói cẩn thận</strong><small>Giao hàng toàn quốc</small></div></div>
                <div class="footer-promise"><i class="bi bi-headset"></i><div><strong>Tư vấn tận tâm</strong><small>Hỗ trợ 08:00 - 22:00</small></div></div>
            </div>

            <div class="row gy-5 footer-main-row">
                <div class="col-xl-4 col-lg-5 col-md-6">
                    <div class="footer-brand-lockup"><span class="footer-brand-mark"><i class="bi bi-flower1"></i></span><div><strong>Aloha Beauty</strong><small>Beauty made personal</small></div></div>
                    <p class="footer-about">Mỹ phẩm và sản phẩm chăm sóc cá nhân được chọn lọc để bạn tự tin xây dựng khoảnh khắc self-care của riêng mình.</p>
                    <div class="footer-contact-list">
                        <a href="https://maps.google.com/?q=Cầu+Giấy,+Hà+Nội" target="_blank"><i class="bi bi-geo-alt-fill"></i><span>Cầu Giấy, Hà Nội</span></a>
                        <a href="tel:0338054668"><i class="bi bi-telephone-fill"></i><span>0338 054 668 <small>• Hotline tư vấn</small></span></a>
                        <a href="mailto:contact@phungthanhtuc.com"><i class="bi bi-envelope-fill"></i><span>contact@phungthanhtuc.com</span></a>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-xl-2">
                    <h6 class="footer-column-title">Mua sắm</h6>
                    <ul class="list-unstyled footer-menu">
                        <li><a href="{{ route('products.index') }}">Tất cả sản phẩm</a></li>
                        <li><a href="{{ route('products.index', ['category' => 1]) }}">Chăm sóc da</a></li>
                        <li><a href="{{ route('products.index', ['category' => 2]) }}">Trang điểm</a></li>
                        <li><a href="{{ route('products.index', ['category' => 3]) }}">Chăm sóc tóc</a></li>
                        <li><a href="{{ route('products.index', ['category' => 4]) }}">Chăm sóc cá nhân</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3 col-xl-2">
                    <h6 class="footer-column-title">Hỗ trợ</h6>
                    <ul class="list-unstyled footer-menu">
                        <li><a href="{{ route('cart.index') }}">Giỏ hàng của bạn</a></li>
                        <li><a href="{{ route('orders.index') }}">Theo dõi đơn hàng</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#supportPolicyModal" data-policy="returns">Đổi trả & hoàn tiền</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#supportPolicyModal" data-policy="shipping">Giao hàng & thanh toán</a></li>
                        <li><a href="mailto:contact@phungthanhtuc.com">Liên hệ Aloha</a></li>
                    </ul>
                </div>

                <div class="col-xl-4 col-lg-12 col-md-6">
                    <div class="footer-connect-panel">
                        <span class="footer-panel-kicker">STAY IN THE GLOW</span>
                        <h6>Đừng bỏ lỡ những ưu đãi xinh xắn</h6>
                        <p>Follow Aloha Beauty để cập nhật sản phẩm mới và tips chăm sóc bản thân.</p>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <a href="https://www.facebook.com/aimachan205/" target="_blank" class="footer-social footer-social-facebook" title="Facebook"><i class="bi bi-facebook"></i><span>Facebook</span></a>
                            <a href="https://www.youtube.com/@VanTu-vp6yn" target="_blank" class="footer-social footer-social-youtube" title="YouTube"><i class="bi bi-youtube"></i><span>YouTube</span></a>
                            <a href="https://twitter.com/" target="_blank" class="footer-social footer-social-x" title="X"><i class="bi bi-twitter-x"></i><span>X</span></a>
                        </div>
                        <div class="footer-payment-line"><span>Thanh toán an toàn</span><div><i class="bi bi-credit-card-2-front"></i><i class="bi bi-cash-coin"></i><i class="bi bi-qr-code-scan"></i></div></div>
                    </div>
                </div>
            </div>

            <div id="footer-policies" class="footer-policy-strip"><span><i class="bi bi-shield-check me-1"></i> Bảo mật thông tin khách hàng</span><span><i class="bi bi-arrow-repeat me-1"></i> Hỗ trợ đổi trả minh bạch</span><span><i class="bi bi-heart-fill me-1"></i> Chăm sóc bạn bằng sự tử tế</span></div>
            <div class="footer-bottom"><span>&copy; {{ date('Y') }} Aloha Beauty. All rights reserved.</span><span>Made with care in Hà Nội, Việt Nam.</span></div>
        </div>
    </footer>

    <div class="modal fade" id="supportPolicyModal" tabindex="-1" aria-labelledby="supportPolicyTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="supportPolicyTitle">Chính sách hỗ trợ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body pt-0" id="supportPolicyContent"></div>
                <div class="modal-footer border-0"><a href="tel:0338054668" class="btn btn-primary rounded-pill"><i class="bi bi-telephone me-1"></i>Gọi tư vấn</a></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('supportPolicyModal')?.addEventListener('show.bs.modal', function (event) {
            const policy = event.relatedTarget?.dataset.policy;
            const title = document.getElementById('supportPolicyTitle');
            const content = document.getElementById('supportPolicyContent');
            if (policy === 'returns') {
                title.textContent = 'Đổi trả & hoàn tiền';
                content.innerHTML = '<p class="text-muted">Aloha Beauty hỗ trợ đổi trả trong 7 ngày nếu sản phẩm bị lỗi, giao sai hoặc hư hỏng khi nhận.</p><ul class="text-muted ps-3"><li>Giữ nguyên sản phẩm, hộp và phụ kiện.</li><li>Gửi ảnh/video tình trạng sản phẩm qua chat hỗ trợ.</li><li>Thời gian xử lý: 2-3 ngày làm việc sau khi nhận đủ thông tin.</li><li>Hoàn tiền về phương thức thanh toán ban đầu sau khi xác nhận.</li></ul>';
            } else {
                title.textContent = 'Giao hàng & thanh toán';
                content.innerHTML = '<p class="text-muted">Aloha Beauty giao hàng toàn quốc và đóng gói cẩn thận để sản phẩm đến bạn an toàn.</p><ul class="text-muted ps-3"><li>Thời gian dự kiến: 2-5 ngày làm việc.</li><li>Thanh toán COD khi nhận hàng.</li><li>Thanh toán chuyển khoản nhanh qua PayOS.</li><li>Địa chỉ giao hàng có thể được ghim chính xác trên bản đồ lúc đặt hàng.</li></ul>';
            }
        });
    </script>

            <audio id="ting-sound" src="https://actions.google.com/sounds/v1/communications/incoming_message.ogg" preload="auto" class="d-none"></audio>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <!-- SCRIPT TÌM KIẾM TRỰC TIẾP (LIVE SEARCH) HOẠT ĐỘNG TOÀN CỤC -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('live-search-input');
        const suggestionsBox = document.getElementById('search-suggestions');

        if(searchInput && suggestionsBox) {
            // Ẩn hộp gợi ý khi click chuột ra ngoài
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                    suggestionsBox.classList.add('d-none');
                }
            });

            // Bắt sự kiện khi người dùng gõ phím
            searchInput.addEventListener('input', function() {
                let query = this.value.trim();
                
                if (query.length < 1) {
                    suggestionsBox.classList.add('d-none');
                    return;
                }

                // Gọi ngầm xuống Backend lấy dữ liệu
                fetch(`/search-suggestions?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsBox.innerHTML = ''; 
                        
                        if (data.length > 0) {
                            let html = '<ul class="list-unstyled mb-0 py-2">';
                            data.forEach(item => {
                                let imgHtml = item.image_url 
                                    ? `<img src="${item.image_url}" class="me-3 rounded search-result-image">`
                                    : `<div class="d-flex align-items-center justify-content-center bg-light rounded me-3 search-result-image"><i class="bi bi-box text-muted"></i></div>`;
                                
                                html += `
                                <li>
                                    <a href="${item.detail_url}" class="d-flex align-items-center px-3 py-2 text-decoration-none text-dark search-item-hover">
                                        ${imgHtml}
                                        <div>
                                            <div class="fw-bold fs-6 text-truncate search-result-name">${item.name}</div>
                                            <div class="text-danger small fw-semibold">${item.formatted_price}</div>
                                        </div>
                                    </a>
                                </li>`;
                            });
                            html += '</ul>';
                            suggestionsBox.innerHTML = html;
                            suggestionsBox.classList.remove('d-none');
                        } else {
                            suggestionsBox.innerHTML = '<div class="p-3 text-center text-muted small"><i class="bi bi-emoji-frown me-1"></i> Không tìm thấy sản phẩm</div>';
                            suggestionsBox.classList.remove('d-none');
                        }
                    })
                    .catch(error => console.error("Lỗi tìm kiếm:", error));
            });
        }
    });
    </script>

    <!-- ============================================================== -->
    <!-- LOGIC TỰ ĐỘNG PHÂN LUỒNG CHAT DỰA TRÊN ROLE CỦA USER ĐĂNG NHẬP -->
    <!-- ============================================================== -->
    @auth
        @if(Auth::user()->role === 'admin')
            @if(!request()->routeIs('admin.chat.index') && !request()->routeIs('welcome'))
                <a class="storefront-admin-chat-dock" href="{{ route('admin.chat.index') }}" title="Mở trung tâm chat khách hàng">
                    <i class="bi bi-chat-square-text-fill"></i><span>Chat hỗ trợ</span><b id="storefront-chat-badge" class="storefront-chat-badge d-none">0</b>
                </a>
                <style>
                    .storefront-admin-chat-dock { position: fixed; z-index: 1040; right: 0; top: 52%; display: flex; align-items: center; gap: .55rem; padding: .75rem .9rem .75rem .8rem; color: #fff; text-decoration: none; background: linear-gradient(135deg, #183b56, #1686a0); border-radius: 14px 0 0 14px; box-shadow: 0 8px 22px rgba(24,59,86,.24); transform: translateY(-50%); }
                    .storefront-admin-chat-dock:hover { color: #fff; padding-right: 1.2rem; }
                    .storefront-admin-chat-dock { position: relative; }
                    .storefront-admin-chat-dock i { font-size: 1.15rem; }
                    .storefront-admin-chat-dock span { font-size: .78rem; font-weight: 800; }
                    .storefront-admin-chat-dock .storefront-chat-badge { position: absolute; top: -7px; left: -7px; min-width: 20px; padding: .2rem .35rem; color: #fff; background: #e63950; border: 2px solid #fff; border-radius: 999px; font-size: .65rem; text-align: center; }
                </style>
                <script>
                    (function () {
                        const badge = document.getElementById('storefront-chat-badge');
                        const refreshChatBadge = () => fetch('/chat/users').then(response => response.json()).then(users => {
                            const total = users.reduce((sum, user) => sum + Number(user.unread_messages_count || 0), 0);
                            badge.textContent = total > 99 ? '99+' : total;
                            badge.classList.toggle('d-none', total === 0);
                        }).catch(() => {});
                        refreshChatBadge();
                        setInterval(refreshChatBadge, 15000);
                    })();
                </script>
            @endif
        @else
            <!-- KHUNG CHAT MÀU XANH DÀNH CHO KHÁCH HÀNG -->
            <div id="chat-widget-button" class="shadow-lg chat-widget-button chat-widget-button-user">
                <i class="bi bi-chat-dots-fill text-white fs-3"></i>
                <span id="chat-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white d-none chat-badge">0</span>
            </div>

            <div id="chat-widget-window" class="shadow-lg border-0 d-none chat-widget-window">
                <div class="p-3 text-white d-flex justify-content-between align-items-center chat-header-user">
                    <div><h6 class="mb-0 fw-bold"><i class="bi bi-headset me-2"></i>Hỗ trợ trực tuyến</h6><small id="support-presence" class="opacity-75">Đang kiểm tra trạng thái...</small></div>
                    <i class="bi bi-x-lg chat-close-button" id="close-chat"></i>
                </div>
                
                <div id="chat-messages" class="p-3 flex-grow-1 chat-messages"></div>
                
                <div class="p-3 border-top chat-input-area">
                    <div class="d-flex gap-1 mb-2 chat-topic-suggestions">
                        <button type="button" class="btn btn-sm btn-light border rounded-pill chat-topic" data-topic="Tôi muốn được tư vấn sản phẩm phù hợp">Tư vấn sản phẩm</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill chat-topic" data-topic="Tôi muốn kiểm tra tình trạng đơn hàng">Kiểm tra đơn hàng</button>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill chat-topic" data-topic="Tôi cần hỗ trợ đổi trả sản phẩm">Đổi trả</button>
                    </div>
                    <div id="customer-attachment-preview" class="small text-muted mb-2"></div>
                    <div class="input-group">
                        <label class="btn btn-light border rounded-circle me-1" title="Gửi ảnh"><i class="bi bi-image"></i><input type="file" id="customer-attachment" accept="image/*" hidden></label>
                        <button type="button" class="btn btn-light border rounded-circle me-1" id="customer-emoji" title="Thêm biểu tượng"><i class="bi bi-emoji-smile"></i></button>
                        <input type="text" id="btn-input" class="form-control rounded-pill me-2 border-0 shadow-sm" placeholder="Nhập tin nhắn...">
                        <button class="btn btn-primary rounded-circle shadow-sm chat-send-button" id="btn-chat"><i class="bi bi-send-fill"></i></button>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const chatBtn = document.getElementById('chat-widget-button');
                const chatWin = document.getElementById('chat-widget-window');
                const chatBadge = document.getElementById('chat-badge');
                const tingSound = document.getElementById('ting-sound');
                
                let currentUserId = {{ Auth::id() ?? 'null' }};
                let unreadCount = 0;
                const customerAttachment = document.getElementById('customer-attachment');
                const customerAttachmentPreview = document.getElementById('customer-attachment-preview');

                chatBtn.addEventListener('click', () => {
                    chatWin.classList.toggle('d-none');
                    if(!chatWin.classList.contains('d-none')) { 
                        loadMessages(); 
                        updateSupportPresence();
                        chatBtn.style.transform = 'scale(0)'; 
                        unreadCount = 0;
                        chatBadge.classList.add('d-none');
                        chatBadge.innerText = '0';
                    }
                });
                
                document.getElementById('close-chat').addEventListener('click', () => {
                    chatWin.classList.add('d-none'); chatBtn.style.transform = 'scale(1)';
                });

                function loadMessages() {
                    fetch('/chat/messages').then(res => res.json()).then(data => {
                        document.getElementById('chat-messages').innerHTML = '';
                        if (data.length === 0) appendMessage({ is_admin: 1, message: 'Xin chào! Aloha Beauty có thể hỗ trợ bạn điều gì hôm nay?' });
                        data.forEach(msg => appendMessage(msg));
                        scrollToBottom();
                    });
                }

                function updateSupportPresence() {
                    fetch('/chat/presence').then(res => res.json()).then(status => {
                        const target = document.getElementById('support-presence');
                        if (!target) return;
                        target.innerHTML = status.online ? '<i class="bi bi-circle-fill text-success me-1"></i>Đang online' : (status.last_seen_minutes === null ? 'Chưa hoạt động' : 'Hoạt động ' + status.last_seen_minutes + ' phút trước');
                    });
                }

                function appendMessage(msg) {
                    let isMine = msg.is_admin == 0;
                    let attachment = msg.attachment_url ? `<img src="${msg.attachment_url}" alt="Ảnh đính kèm" style="max-width:180px;max-height:120px;border-radius:10px;display:block;margin-top:6px">` : '';
                    let html = `<div class="w-100 mb-2 chat-message-row">
                                    ${!isMine ? '<small class="d-block text-muted mb-1 chat-sender-label">Admin Shop</small>' : ''}
                                    <div class="msg-bubble ${isMine ? 'msg-mine' : 'msg-other'}">${msg.message || ''}${attachment}</div>
                                </div>`;
                    document.getElementById('chat-messages').insertAdjacentHTML('beforeend', html);
                    scrollToBottom();
                }

                function scrollToBottom() { let box = document.getElementById('chat-messages'); box.scrollTop = box.scrollHeight; }

                function sendMessage() {
                    let text = document.getElementById('btn-input').value.trim();
                    if(!text && !customerAttachment.files.length) return;
                    document.getElementById('btn-input').value = '';
                    const formData = new FormData();
                    formData.append('message', text);
                    if (customerAttachment.files[0]) formData.append('attachment', customerAttachment.files[0]);
                    fetch('/chat/message', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    }).then(response => response.json()).then(sent => { appendMessage({...sent.message, is_admin: 0, message: text}); });
                    customerAttachment.value = '';
                    customerAttachmentPreview.innerHTML = '';
                }

                customerAttachment.addEventListener('change', () => {
                    const file = customerAttachment.files[0];
                    customerAttachmentPreview.textContent = file ? 'Đã chọn: ' + file.name : '';
                });
                document.querySelectorAll('.chat-topic').forEach(button => button.addEventListener('click', () => {
                    document.getElementById('btn-input').value = button.dataset.topic;
                    document.getElementById('btn-input').focus();
                }));
                document.getElementById('customer-emoji').addEventListener('click', () => {
                    document.getElementById('btn-input').value += ' 😊';
                    document.getElementById('btn-input').focus();
                });

                document.getElementById('btn-chat').addEventListener('click', sendMessage);
                document.getElementById('btn-input').addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });

                var pusher = new Pusher('c7b756312af017cea0f9', { cluster: 'ap1' });
                pusher.subscribe('chat-channel').bind('message.sent', function(data) {
                    if(data.message.user_id == currentUserId && data.message.is_admin == 1) {
                        tingSound.currentTime = 0;
                        tingSound.play().catch(e => console.log('Âm thanh chờ tương tác người dùng: ', e));
                        if(!chatWin.classList.contains('d-none')) {
                            appendMessage(data.message);
                        } else {
                            appendMessage(data.message); 
                            
                            unreadCount++;
                            chatBadge.innerText = unreadCount > 99 ? '99+' : unreadCount;
                            chatBadge.classList.remove('d-none');
                            
                            chatBtn.classList.add('animate__animated', 'animate__tada');
                            setTimeout(() => chatBtn.classList.remove('animate__animated', 'animate__tada'), 1000);
                            
                            if ('Notification' in window && Notification.permission === 'granted') {
                                new Notification('Aloha Beauty có tin nhắn mới', {body: data.message.message});
                            }
                        }
                    }
                });
                const heartbeat = () => fetch('/chat/heartbeat', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
                heartbeat();
                setInterval(heartbeat, 60000);
                setInterval(updateSupportPresence, 60000);
            });
            </script>
        @endif
    @endauth
    @auth
        <script>
            (function () {
                const sendPresence = () => fetch('/chat/heartbeat', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).catch(() => {});
                sendPresence();
                setInterval(sendPresence, 60000);
            })();
        </script>
    @endauth
    @stack('scripts')
</body>
</html>