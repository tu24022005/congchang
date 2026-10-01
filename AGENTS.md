# Quy tắc & Ngữ cảnh Dự án BeatyCare (Aloha Beauty)

Bạn đang làm việc trên project Laravel 12 tên "BeatyCare" (thương hiệu hiển thị trong footer là "Aloha Beauty"), web bán mỹ phẩm bằng tiếng Việt.

## STACK THỰC TẾ
- Backend: Laravel 12, Blade, Sanctum, Socialite (Google), PayOS, Pusher (chat), spatie/laravel-backup.
- Frontend: Bootstrap 5.3.3 + Bootstrap Icons + animate.css tải qua CDN trong resources/views/layouts/app.blade.php. JS thuần (vanilla), KHÔNG dùng React/Vue/Alpine.
- CSS/JS tự viết: `public/css` (style.css, animations.css, auth.css, product-advisor.css) và `public/js` (animations.js, interactions.js, product-advisor.js, register.js). Nạp trực tiếp qua asset().
- Dark mode: class `dark-mode` trên `<html>`, lưu trong localStorage `beatycare-theme`. Mọi UI mới PHẢI hoạt động cả light và dark mode.
- Global helper/effects đã có: `window.flyToCart`, `window.showToast`, ripple effect, `reveal-up` / `reveal-stagger` (IntersectionObserver), page transition trong `interactions.js`. Tái sử dụng, không viết trùng.
- Routes & Controllers: `routes/web.php`. Controller ở `app/Http/Controllers` (có thư mục `Admin`). Giỏ hàng: `CartController`, `CartService`, models `Cart`/`CartItem`. Đơn hàng: `OrderController`. Sản phẩm: `ProductController`.

## NGUYÊN TẮC BẮT BUỘC KHI LÀM VIỆC
1. Trước khi sửa, đọc kỹ file liên quan và tóm tắt ngắn kế hoạch. Không đoán.
2. Không phá vỡ tính năng hiện có. Không đổi tên route/name đang dùng trong view (nếu đổi, grep và sửa hết).
3. Giữ nguyên ngôn ngữ giao diện tiếng Việt, văn phong thân thiện.
4. Animation: (a) tôn trọng `prefers-reduced-motion: reduce`, (b) chỉ animate `transform` và `opacity`, (c) không gây layout shift (CLS).
5. Mobile-first: kiểm tra 360px, 768px, 1280px.
6. Code gọn gàng, comment tiếng Việt ở chỗ phức tạp. Không thêm thư viện nặng nếu JS/CSS thuần giải quyết được.
7. Sau mỗi tác vụ: kiểm tra route (`php artisan route:list`), test nếu có, liệt kê file sửa/tạo và hướng dẫn kiểm tra thủ công.
8. Không commit dữ liệu nhạy cảm, không sửa trực tiếp `.env` (chỉ cập nhật `.env.example` khi cần key mới).
