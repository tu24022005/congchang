<!DOCTYPE html> 
<html lang="vi"> 
<head> 
 <meta charset="UTF-8"> 
 <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
 <title>Xác thực tài khoản Email</title> 
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> </head> 
<body class="bg-light d-flex justify-content-center align-items-center vh-100"> 
 <div class="card shadow p-4 auth-verify-card">
 <div class="card-body text-center"> 
 <h3 class="card-title mb-3">Xác thực địa chỉ Email của bạn</h3> 
  
 <p class="text-muted mb-4"> 
 Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, bạn vui lòng kiểm tra hộp thư đến (hoặc hòm thư rác/Spam) để nhấp  vào đường liên kết xác thực email. 
 </p> 
 @if (session('status') == 'verification-link-sent') 
 <div class="alert alert-success mb-3" role="alert"> 
 Một đường liên kết xác thực mới đã được gửi đến địa chỉ email bạn dùng khi đăng ký.


 </div> 
 @endif 
 <div class="d-grid gap-2"> 
 <!-- Form gửi lại email xác thực --> 
 <form method="POST" action="{{ route('verification.send') }}"> 
 @csrf 
 <button type="submit" class="btn btn-primary w-100 mb-2">Gửi lại email xác thực</button>  </form> 
 <!-- Nút Đăng xuất --> 
 <form method="POST" action="{{ route('logout') }}"> 
 @csrf 
 <button type="submit" class="btn btn-outline-secondary w-100">Đăng xuất</button>  </form> 
 </div> 
 </div> 
 </div> 
</body> 
</html>

