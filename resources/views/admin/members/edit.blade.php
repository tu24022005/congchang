@extends('layouts.app')

@section('title', 'Chỉnh sửa tài khoản khách hàng: ' . $user->name . ' - Aloha Beauty')

@push('styles')
<link rel="stylesheet" href="{{ asset_v('css/views/admin-members-edit-blade-php.css') }}">
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('admin.customers.show', $user) }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2">
                <i class="bi bi-arrow-left me-1"></i> Quay lại hồ sơ khách hàng
            </a>
            <h2 class="fw-bold mb-0 text-dark">Chỉnh sửa thông tin khách hàng</h2>
            <small class="text-muted">Cập nhật hồ sơ, ảnh đại diện và mật khẩu của khách hàng <strong>{{ $user->name }}</strong> (#{{ $user->id }}).</small>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4 bg-danger-subtle text-danger-emphasis">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Vui lòng kiểm tra lại thông tin:</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="edit-customer-card p-4 p-lg-5">
        <form method="POST" action="{{ route('admin.customers.update', $user) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- KHỐI ẢNH ĐẠI DIỆN -->
            <div class="p-3 bg-light rounded-4 border mb-4">
                <div class="d-flex flex-column flex-sm-row align-items-center gap-4">
                    <div class="avatar-edit-box position-relative" id="avatarPreviewBox">
                        @if($user->avatar_url)
                            <img id="avatarPreviewImg" src="{{ $user->avatar_url }}" alt="Ảnh đại diện" class="view-inline-1">
                        @else
                            <span id="avatarInitialsText">{{ $user->initials }}</span>
                            <img id="avatarPreviewImg" src="" alt="Preview" class="d-none view-inline-1">
                        @endif
                    </div>
                    <div class="flex-grow-1 w-100 text-center text-sm-start">
                        <label for="avatarInput" class="form-label fw-bold text-dark mb-1">
                            <i class="bi bi-camera me-1 text-primary"></i>Ảnh đại diện khách hàng
                        </label>
                        <p class="small text-muted mb-2">Chấp nhận JPG, PNG, WEBP. Dung lượng tối đa 2MB. Ảnh sẽ được tự động cắt tròn tỉ lệ 1:1.</p>
                        <input id="avatarInput" type="file" name="avatar" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp">
                        @error('avatar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

                        @if($user->avatar_path)
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="removeAvatarCheck">
                                <label class="form-check-label text-danger small fw-semibold" for="removeAvatarCheck">
                                    <i class="bi bi-trash3 me-1"></i> Xóa ảnh đại diện hiện tại (chuyển về chữ cái mặc định)
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- THÔNG TIN CƠ BẢN -->
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-person-lines-fill me-2 text-primary"></i>Thông tin tài khoản
            </h5>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="name">Họ và tên <span class="text-danger">*</span></label>
                    <input id="name" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required placeholder="Nguyễn Văn A">
                    @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="phone">Số điện thoại <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                    <input id="phone" type="tel" name="phone" class="form-control rounded-3" value="{{ old('phone', $user->phone) }}" pattern="^(0|\+84)(3|5|7|8|9)[0-9]{8}$" autocomplete="tel" placeholder="0987654321">
                    @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" for="email">Địa chỉ Email <span class="text-danger">*</span></label>
                    <input id="email" type="email" name="email" class="form-control rounded-3" value="{{ old('email', $user->email) }}" pattern="^[^@\s]+@[^@\s]+\.[A-Za-z]{2,}$" required autocomplete="email" placeholder="example@gmail.com">
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- ĐỔI MẬT KHẨU -->
            <div class="border-top pt-4 mb-4">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-key-fill me-2 text-warning"></i>Đặt lại mật khẩu
                </h5>
                <p class="small text-muted mb-3">Chỉ điền vào 2 ô dưới đây nếu bạn muốn thay đổi mật khẩu đăng nhập của khách hàng.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="password">Mật khẩu mới</label>
                        <input id="password" type="password" name="password" class="form-control rounded-3" minlength="8" autocomplete="new-password" placeholder="Tối thiểu 8 ký tự...">
                        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="password_confirmation">Xác nhận mật khẩu mới</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control rounded-3" minlength="8" autocomplete="new-password" placeholder="Nhập lại mật khẩu mới...">
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 pt-3 border-top">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-check2-circle me-1"></i> Lưu thay đổi
                </button>
                <a href="{{ route('admin.customers.show', $user) }}" class="btn btn-light border rounded-pill px-4">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset_v('js/views/admin-members-edit-blade-php.js') }}" defer></script>
@endpush
