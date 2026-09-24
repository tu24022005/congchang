<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\ActivityLogService;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::in(['admin', 'manager', 'warehouse_staff', 'customer_service'])],
        ]);

        $staff = User::whereIn('role', ['admin', 'manager', 'warehouse_staff', 'customer_service'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
            ->latest()
            ->get();

        return view('admin.staff.index', [
            'staff' => $staff,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'manager', 'warehouse_staff', 'customer_service'])],
        ]);

        $staff = User::create($data);
        ActivityLogService::record('account.created', 'Đã tạo tài khoản ' . $staff->name . '.', $staff, null, ['name' => $staff->name, 'email' => $staff->email, 'role' => $staff->role]);

        return back()->with('success', 'Đã tạo tài khoản ' . $data['name'] . '.');
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Không thể tự đổi quyền tài khoản đang đăng nhập.');
        }

        $data = $request->validate([
            'role' => ['required', Rule::in(['admin', 'manager', 'warehouse_staff', 'customer_service'])],
        ]);
        $before = ['role' => $user->role];
        $user->update(['role' => $data['role']]);
        ActivityLogService::record('role.updated', 'Đã đổi vai trò của ' . $user->name . '.', $user, $before, ['role' => $user->role]);

        return back()->with('success', 'Đã cập nhật vai trò cho ' . $user->name . '.');
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
        ActivityLogService::record('account.deleted', 'Đã xóa tài khoản ' . $name . '.', $user, $before, null);
        $user->delete();

        return back()->with('success', 'Đã xoá tài khoản ' . $name . '.');
    }
}
