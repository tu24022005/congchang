<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\ActivityLogService;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::in(['admin', 'manager', 'warehouse_staff', 'customer_service'])],
            'status' => ['nullable', Rule::in(['active', 'locked'])],
        ]);

        $baseStaffQuery = User::whereIn('role', ['admin', 'manager', 'warehouse_staff', 'customer_service']);

        // Thống kê nhân sự theo vai trò & trạng thái
        $totalStaff = (clone $baseStaffQuery)->count();
        $adminCount = (clone $baseStaffQuery)->where('role', 'admin')->count();
        $managerCount = (clone $baseStaffQuery)->where('role', 'manager')->count();
        $warehouseCount = (clone $baseStaffQuery)->where('role', 'warehouse_staff')->count();
        $csCount = (clone $baseStaffQuery)->where('role', 'customer_service')->count();
        $lockedCount = (clone $baseStaffQuery)->whereNotNull('login_locked_at')->count();
        $activeCount = $totalStaff - $lockedCount;

        $staff = (clone $baseStaffQuery)
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
            ->when(($filters['status'] ?? null) === 'locked', fn ($query) => $query->whereNotNull('login_locked_at'))
            ->when(($filters['status'] ?? null) === 'active', fn ($query) => $query->whereNull('login_locked_at'))
            ->latest()
            ->get();

        return view('admin.staff.index', [
            'staff' => $staff,
            'filters' => $filters,
            'counts' => [
                'total' => $totalStaff,
                'admin' => $adminCount,
                'manager' => $managerCount,
                'warehouse' => $warehouseCount,
                'cs' => $csCount,
                'locked' => $lockedCount,
                'active' => $activeCount,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', 'unique:users,phone'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'manager', 'warehouse_staff', 'customer_service'])],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên nhân viên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'phone.regex' => 'Số điện thoại Việt Nam không hợp lệ.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'avatar.image' => 'Ảnh đại diện phải là tệp hình ảnh.',
            'avatar.mimes' => 'Chấp nhận các định dạng JPG, JPEG, PNG, WEBP.',
            'avatar.max' => 'Dung lượng ảnh tối đa 2MB.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        $staff = User::create($data);

        ActivityLogService::record(
            'staff.created',
            'Đã tạo tài khoản nhân viên ' . $staff->name . ' với vai trò ' . $this->roleLabel($staff->role) . '.',
            $staff,
            null,
            ['name' => $staff->name, 'email' => $staff->email, 'role' => $staff->role]
        );

        return back()->with('success', 'Đã thêm thành công tài khoản nhân viên ' . $staff->name . '.');
    }

    public function update(Request $request, User $user)
    {
        $isSelf = $user->id === $request->user()->id;

        // Cho phép cả đổi nhanh vai trò lẫn cập nhật toàn bộ thông tin
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
            'role' => ['required', Rule::in(['admin', 'manager', 'warehouse_staff', 'customer_service'])],
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'email.unique' => 'Email này đã có người sử dụng.',
            'phone.regex' => 'Số điện thoại Việt Nam không hợp lệ.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'avatar.image' => 'Ảnh đại diện phải là tệp hình ảnh.',
            'avatar.mimes' => 'Chấp nhận các định dạng JPG, JPEG, PNG, WEBP.',
            'avatar.max' => 'Dung lượng ảnh tối đa 2MB.',
        ]);

        if ($isSelf && $data['role'] !== $user->role) {
            return back()->with('error', 'Bạn không thể tự thay đổi vai trò của tài khoản đang đăng nhập.');
        }

        // Bảo vệ quản trị viên cuối cùng
        if ($user->role === 'admin' && $data['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Hệ thống cần ít nhất 1 Quản trị viên tối cao. Không thể hạ quyền tài khoản này.');
        }

        $before = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'avatar_path' => $user->avatar_path,
        ];

        $user->name = $data['name'] ?? $user->name;
        $user->email = $data['email'] ?? $user->email;
        $user->phone = array_key_exists('phone', $data) ? $data['phone'] : $user->phone;
        $user->role = $data['role'];

        if ($request->boolean('remove_avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        ActivityLogService::record(
            'staff.updated',
            'Đã cập nhật thông tin và vai trò của nhân viên ' . $user->name . '.',
            $user,
            $before,
            ['name' => $user->name, 'email' => $user->email, 'phone' => $user->phone, 'role' => $user->role]
        );

        return back()->with('success', 'Đã cập nhật thông tin thành công cho nhân viên ' . $user->name . '.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user->password = Hash::make($request->password);
        $user->login_attempts = 0;
        $user->login_locked_at = null;
        $user->save();

        ActivityLogService::record(
            'staff.password_reset',
            'Đã đặt lại mật khẩu mới cho nhân viên ' . $user->name . '.',
            $user,
            null,
            ['email' => $user->email]
        );

        return back()->with('success', 'Đã đặt lại mật khẩu thành công cho tài khoản ' . $user->name . '.');
    }

    public function toggleLock(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Không thể tự khóa tài khoản đang đăng nhập.');
        }

        if ($user->role === 'admin' && is_null($user->login_locked_at) && User::where('role', 'admin')->whereNull('login_locked_at')->count() <= 1) {
            return back()->with('error', 'Không thể khóa Quản trị viên duy nhất đang hoạt động.');
        }

        if ($user->login_locked_at) {
            $user->login_locked_at = null;
            $user->login_attempts = 0;
            $user->save();

            ActivityLogService::record(
                'staff.unlocked',
                'Đã mở khóa tài khoản nhân viên ' . $user->name . '.',
                $user
            );

            return back()->with('success', 'Đã mở khóa tài khoản ' . $user->name . '. Nhân viên có thể đăng nhập bình thường.');
        } else {
            $user->login_locked_at = now();
            $user->save();

            ActivityLogService::record(
                'staff.locked',
                'Đã tạm thời khóa tài khoản nhân viên ' . $user->name . '.',
                $user
            );

            return back()->with('success', 'Đã khóa tài khoản ' . $user->name . '. Nhân viên này không thể đăng nhập vào hệ thống.');
        }
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Không thể xoá tài khoản đang đăng nhập.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Không thể xoá quản trị viên cuối cùng của hệ thống.');
        }

        $name = $user->name;
        $before = ['name' => $user->name, 'email' => $user->email, 'role' => $user->role];

        ActivityLogService::record(
            'staff.deleted',
            'Đã xóa tài khoản nhân viên ' . $name . '.',
            $user,
            $before,
            null
        );

        $user->delete();

        return back()->with('success', 'Đã xoá tài khoản ' . $name . ' khỏi hệ thống.');
    }

    private function roleLabel(string $role): string
    {
        return match ($role) {
            'admin' => 'Quản trị viên',
            'manager' => 'Quản lý',
            'warehouse_staff' => 'Nhân viên kho',
            'customer_service' => 'Nhân viên CSKH',
            default => $role,
        };
    }
}
