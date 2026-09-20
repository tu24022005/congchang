@extends('layouts.app')
@section('title', 'Tài khoản của tôi')

@section('content')
<div class="account-page py-3 py-lg-4">
    <div class="account-page-heading mb-4">
        <div>
            <span class="account-kicker">KHÔNG GIAN CỦA BẠN</span>
            <h1 class="mb-2">Tài khoản của tôi</h1>
            <p class="text-muted mb-0">Quản lý thông tin cá nhân và bảo mật tài khoản Aloha Beauty.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-receipt me-2"></i>Đơn hàng của tôi
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="account-sidebar h-100">
                <div class="account-avatar"><i class="bi bi-person"></i></div>
                <h2>{{ $user->name }}</h2>
                <p class="text-muted mb-4">{{ $user->email }}</p>
                <p class="text-muted mb-4"><i class="bi bi-telephone me-1"></i>{{ $user->phone ?: 'Chưa cập nhật số điện thoại' }}</p>
                <div class="account-status {{ $user->hasVerifiedEmail() ? 'is-verified' : 'is-pending' }}">
                    <i class="bi {{ $user->hasVerifiedEmail() ? 'bi-patch-check-fill' : 'bi-exclamation-circle-fill' }}"></i>
                    <span>{{ $user->hasVerifiedEmail() ? 'Email đã xác thực' : 'Email chưa xác thực' }}</span>
                </div>
                <div class="account-meta-list mt-4">
                    <div><i class="bi bi-calendar3"></i><span>Thành viên từ<strong>{{ $user->created_at?->format('d/m/Y') }}</strong></span></div>
                    <div><i class="bi bi-shield-check"></i><span>Bảo mật<strong>Mật khẩu riêng tư</strong></span></div>
                    <div><i class="bi bi-stars"></i><span>Điểm thành viên<strong>{{ number_format($user->loyalty_points) }} điểm</strong></span></div>
                    <div><i class="bi bi-award"></i><span>Hạng thành viên<strong class="{{ $membershipTier['class'] }}">{{ $membershipTier['name'] }}</strong></span></div>
                </div>
                <div class="small text-muted mt-3">Doanh số đơn hoàn thành: <strong>{{ number_format($completedSpend, 0, ',', '.') }} đ</strong></div>
                <a href="{{ route('loyalty.index') }}" class="btn btn-outline-primary w-100 rounded-pill mt-3">Đổi điểm lấy voucher</a>
                @if (!$user->hasVerifiedEmail())
                    <a href="{{ route('verification.notice') }}" class="btn btn-outline-primary w-100 rounded-pill mt-4">Xác thực email</a>
                @endif
            </div>
        </div>

        <div class="col-lg-8">
            <div class="account-panel mb-4">
                <div class="account-panel-heading">
                    <div><span class="account-panel-icon"><i class="bi bi-person-lines-fill"></i></span><div><h3>Thông tin cá nhân</h3><p class="mb-0">Cập nhật thông tin hiển thị của bạn.</p></div></div>
                </div>
                <form action="{{ route('account.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required autocomplete="name">
                            @error('name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label">Địa chỉ email</label>
                            <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="email">
                            @error('email')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="personal_phone" class="form-label">Số điện thoại cá nhân</label>
                            <input id="personal_phone" type="tel" name="personal_phone" class="form-control" value="{{ old('personal_phone', $user->phone) }}" pattern="^(0|\+84)(3|5|7|8|9)[0-9]{8}$" autocomplete="tel">
                            @error('personal_phone')<div class="field-error">{{ $message }}</div>@enderror
                            <div class="form-text">Dùng cho thông tin tài khoản, không tự động thay thế số người nhận hàng.</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check2 me-2"></i>Lưu thay đổi</button>
                    </div>
                </form>
            </div>

            <div class="account-panel account-security-panel">
                <div class="account-panel-heading">
                    <div><span class="account-panel-icon account-panel-icon-gold"><i class="bi bi-shield-lock-fill"></i></span><div><h3>Bảo mật tài khoản</h3><p class="mb-0">Đổi mật khẩu định kỳ để tài khoản luôn an toàn.</p></div></div>
                    <a href="{{ route('password.change') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Đổi mật khẩu</a>
                </div>
                <div class="account-security-note"><i class="bi bi-lock-fill"></i><span>Mật khẩu của bạn được mã hóa và không hiển thị cho bất kỳ ai.</span></div>
            </div>

            <div class="account-panel mt-4">
                <div class="account-panel-heading">
                    <div><span class="account-panel-icon"><i class="bi bi-geo-alt-fill"></i></span><div><h3>Sổ địa chỉ giao hàng</h3><p class="mb-0">Lưu nhiều địa chỉ để chọn nhanh khi đặt hàng.</p></div></div>
                </div>

                @foreach ($addresses as $address)
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <strong>{{ $address->label }}</strong>
                                @if ($address->is_default)<span class="badge text-bg-primary ms-2">Mặc định</span>@endif
                                <div>{{ $address->recipient_name }} - {{ $address->phone }}</div>
                                <div class="text-muted">{{ $address->address }}</div>
                            </div>
                            <div class="d-flex gap-2 align-items-start">
                                @unless ($address->is_default)
                                    <form method="POST" action="{{ route('addresses.default', $address) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-primary">Đặt mặc định</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('addresses.destroy', $address) }}" onsubmit="return confirm('Xóa địa chỉ này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
                            </div>
                        </div>
                        <details class="mt-2">
                            <summary class="small text-primary">Chỉnh sửa</summary>
                            <form method="POST" action="{{ route('addresses.update', $address) }}" class="row g-2 mt-2">
                                @csrf @method('PUT')
                                <div class="col-md-3"><input name="label" class="form-control" value="{{ $address->label }}" required></div>
                                <div class="col-md-3"><input name="recipient_name" class="form-control" value="{{ $address->recipient_name }}" required></div>
                                <div class="col-md-3"><input name="recipient_phone" type="tel" class="form-control" value="{{ $address->phone }}" placeholder="SĐT người nhận" autocomplete="shipping tel" required></div>
                                <div class="col-md-9"><input name="address" class="form-control" value="{{ $address->address }}" required></div>
                                <div class="col-md-3 form-check ms-2"><input type="checkbox" name="is_default" value="1" class="form-check-input" @checked($address->is_default)> <label class="form-check-label">Đặt mặc định</label></div>
                                <div class="col-12"><button class="btn btn-primary btn-sm">Lưu địa chỉ</button></div>
                            </form>
                        </details>
                    </div>
                @endforeach

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 mt-3">
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('addresses.store') }}" class="row g-2">
                    @csrf
                    <div class="col-md-3"><input name="label" class="form-control" placeholder="Nhãn: Nhà riêng" required></div>
                    <div class="col-md-3"><input name="recipient_name" class="form-control" placeholder="Tên người nhận" required></div>
                    <div class="col-md-3"><input name="recipient_phone" type="tel" class="form-control" placeholder="SĐT người nhận" autocomplete="shipping tel" required></div>
                    <div class="col-md-9"><input name="address" class="form-control" placeholder="Địa chỉ chi tiết" required></div>
                    <div class="col-md-3 form-check ms-2"><input type="checkbox" name="is_default" value="1" class="form-check-input"> <label class="form-check-label">Đặt mặc định</label></div>
                    <div class="col-12"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Lưu địa chỉ</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
