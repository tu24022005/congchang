<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use App\Services\ActivityLogService;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['customer', 'user'])
            ->withCount(['orders', 'loyaltyPointTransactions'])
            ->withSum(['orders as completed_spend' => fn ($orders) => $orders->where('status', 'completed')], 'total')
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $members = $query->paginate(15)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[A-Za-z]{2,}$/D', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.regex' => 'Email phải có tên miền hợp lệ, ví dụ: ten@gmail.com.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.regex' => 'Số điện thoại Việt Nam không hợp lệ.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'customer',
            'email_verified_at' => Carbon::now(),
        ]);

        ActivityLogService::record(
            'customer.created',
            'Đã tạo tài khoản khách hàng ' . $user->name . '.',
            $user,
            null,
            ['name' => $user->name, 'email' => $user->email]
        );

        return redirect()->route('admin.customers.index')
            ->with('success', 'Đã tạo tài khoản khách hàng thành công.');
    }

    public function edit(User $user)
    {
        abort_if(!in_array($user->role, ['customer', 'user'], true), 404);

        return view('admin.members.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if(!in_array($user->role, ['customer', 'user'], true), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[A-Za-z]{2,}$/D', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'email.regex' => 'Email phải có tên miền hợp lệ, ví dụ: ten@gmail.com.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $before = ['name' => $user->name, 'email' => $user->email];
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
            $user->login_attempts = 0;
            $user->login_locked_at = null;
        }
        $user->save();

        ActivityLogService::record(
            'customer.updated',
            'Đã cập nhật tài khoản khách hàng ' . $user->name . '.',
            $user,
            $before,
            ['name' => $user->name, 'email' => $user->email]
        );

        return redirect()->route('admin.members.show', $user)->with('success', 'Đã cập nhật tài khoản khách hàng.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if(!in_array($user->role, ['customer', 'user'], true), 404);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Không thể xoá tài khoản đang đăng nhập.');
        }

        $name = $user->name;
        ActivityLogService::record(
            'customer.deleted',
            'Đã xóa tài khoản khách hàng ' . $name . '.',
            $user,
            ['name' => $name, 'email' => $user->email],
            null
        );
        $user->delete();

        return redirect()->route('admin.members.index')->with('success', 'Đã xoá tài khoản ' . $name . '.');
    }

    public function show(User $user)
    {
        abort_if(!in_array($user->role, ['customer', 'user'], true), 404);

        $user->loadCount('orders');
        $user->completed_spend = $user->orders()->where('status', 'completed')->sum('total');
        $membershipTier = User::membershipTierFor($user->completed_spend);
        $transactions = $user->loyaltyPointTransactions()->latest()->paginate(15);

        return view('admin.members.show', compact('user', 'transactions', 'membershipTier'));
    }
}
