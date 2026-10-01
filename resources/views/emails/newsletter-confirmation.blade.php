<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đăng ký nhận tin BeatyCare</title>
<link rel="stylesheet" href="{{ asset_v('css/views/emails-newsletter-confirmation.css') }}">
</head>
<body class="email-inline-1">
    <div class="email-inline-2">
        <div class="email-inline-3">
            <h1 class="email-inline-4">BeatyCare 🌸</h1>
            <p class="email-inline-5">Vẻ đẹp rạng ngời &bull; Chăm sóc tự nhiên</p>
        </div>

        <h2 class="email-inline-6">Xin chào bạn,</h2>
        
        <p class="email-inline-7">
            Cảm ơn bạn đã quan tâm và đăng ký nhận bản tin làm đẹp, cẩm nang chăm sóc da và các ưu đãi độc quyền từ <strong>BeatyCare (Aloha Beauty)</strong>.
        </p>

        <p class="email-inline-7">
            Vui lòng nhấn vào nút bên dưới để hoàn tất việc xác nhận đăng ký nhận tin:
        </p>

        <div class="email-inline-8">
            <a href="{{ $confirmUrl }}" class="email-inline-9">
                Xác nhận đăng ký ngay
            </a>
        </div>

        <p class="email-inline-10">
            Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này hoặc <a href="{{ $unsubscribeUrl }}" class="email-inline-11">hủy đăng ký</a>.
        </p>

        <hr class="email-inline-12">

        <div class="email-inline-13">
            &copy; {{ date('Y') }} Aloha Beauty - BeatyCare. All rights reserved.
        </div>
    </div>
</body>
</html>
