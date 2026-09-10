<!-- resources/views/auth/register.blade.php --> 
{{-- Hiển thị thông báo thành công/thất bại từ Controller --}} 
@if (session('success')) 
 <div class="alert alert-success"> 
 {{ session('success') }} 
 </div> 
@endif

@if (session('error')) 
 <div class="alert alert-danger"> 
 {{ session('error') }} 
 </div> 
@endif 
<form action="{{ url('register') }}" method="POST"> 
 @csrf 
 <div class="form-group mb-3"> 
 <label for="name">Name</label> 
 {{-- Dùng old('name') để giữ lại text nếu bị lỗi --}} 
 <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>  {{-- Hiển thị lỗi riêng của trường name --}} 
 @error('name') 
 <span class="text-danger small">{{ $message }}</span> 
 @enderror 
 </div> 
 <div class="form-group mb-3"> 
 <label for="email">Email</label> 
 <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>  @error('email') 
 <span class="text-danger small">{{ $message }}</span> 
 @enderror 
 </div> 
 <div class="form-group mb-3"> 
 <label for="password">Password</label> 
 <input type="password" name="password" id="password" class="form-control" required>  @error('password') 
 <span class="text-danger small">{{ $message }}</span> 
 @enderror 
 </div>
 <div class="form-group mb-4"> 
 <label for="password_confirmation">Confirm Password</label> 
 <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>  </div> 
 <button type="submit" class="btn btn-primary">Register</button> 
</form>

