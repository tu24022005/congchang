<!-- resources/views/auth/login.blade.php --> 
{{-- Hứng thông báo đăng ký thành công từ hàm register chuyển sang --}} 
@if (session('success')) 
 <div class="alert alert-success"> 
 {{ session('success') }} 
 </div> 
@endif 
{{-- Hứng thông báo lỗi chung (nếu có) --}} 
@if (session('error')) 
 <div class="alert alert-danger"> 
 {{ session('error') }} 
 </div> 
@endif 
<form action="{{ route('login') }}" method="POST"> 
 @csrf 
 <div class="form-group mb-3"> 
 <label for="email">Email</label> 
 {{-- Dùng old('email') để giữ lại email nếu nhập sai mật khẩu --}} 
 <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>   
 {{-- Hiển thị lỗi sai email ngay dưới ô input --}} 
 @error('email') 
 <span class="text-danger small">{{ $message }}</span> 
 @enderror 
 </div> 
 <div class="form-group mb-4"> 
 <label for="password">Password</label> 
 <input type="password" name="password" id="password" class="form-control" required>  
 {{-- Hiển thị lỗi sai password ngay dưới ô input --}} 
 @error('password') 
 <span class="text-danger small">{{ $message }}</span> 
 @enderror 
 </div> 
 <button type="submit" class="btn btn-primary">Login</button> 
</form>

