<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Voucher;
use App\Models\LoyaltyPointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use App\Services\ActivityLogService;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search' => trim($request->input('search', '')),
            'tier' => $request->input('tier', ''),
            'buyer_status' => $request->input('buyer_status', ''),
            'status' => $request->input('status', ''),
            'sort' => $request->input('sort', 'latest'),
        ];

        $baseQuery = User::whereIn('role', ['customer', 'user']);

        // 1. Thống kê KPI tổng quan khách hàng
        $totalCustomers = (clone $baseQuery)->count();
        $newThisMonth = (clone $baseQuery)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $buyersCount = (clone $baseQuery)->has('orders')->count();
        $conversionRate = $totalCustomers > 0 ? round(($buyersCount / $totalCustomers) * 100, 1) : 0;
        
        $totalCompletedSpend = (float) \App\Models\Order::whereHas('user', function ($q) {
            $q->whereIn('role', ['customer', 'user']);
        })->whereIn('status', ['paid', 'completed'])->sum('total');

        $avgSpendPerBuyer = $buyersCount > 0 ? round($totalCompletedSpend / $buyersCount) : 0;
        $totalLoyaltyPoints = (clone $baseQuery)->sum('loyalty_points');
        $lockedCount = (clone $baseQuery)->whereNotNull('login_locked_at')->count();
        $activeCount = $totalCustomers - $lockedCount;

        // 2. Query danh sách khách hàng
        $query = User::whereIn('role', ['customer', 'user'])
            ->withCount(['orders', 'loyaltyPointTransactions', 'collectedVouchers'])
            ->withSum(['orders as completed_spend' => fn ($orders) => $orders->whereIn('status', ['paid', 'completed'])], 'total');

        // Tìm kiếm
        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // Lọc theo trạng thái tài khoản
        if ($filters['status'] === 'active') {
            $query->whereNull('login_locked_at');
        } elseif ($filters['status'] === 'locked') {
            $query->whereNotNull('login_locked_at');
        }

        // Lọc theo lịch sử mua hàng
        if ($filters['buyer_status'] === 'has_orders') {
            $query->has('orders');
        } elseif ($filters['buyer_status'] === 'no_orders') {
            $query->doesntHave('orders');
        }

        // Lọc theo hạng thành viên (kết hợp doanh số và điểm tích lũy)
        if ($filters['tier'] === 'diamond') {
            $query->havingRaw('completed_spend >= 20000000 OR loyalty_points >= 2000');
        } elseif ($filters['tier'] === 'platinum') {
            $query->havingRaw('(completed_spend >= 10000000 OR loyalty_points >= 1000) AND (completed_spend < 20000000 AND loyalty_points < 2000)');
        } elseif ($filters['tier'] === 'gold') {
            $query->havingRaw('(completed_spend >= 5000000 OR loyalty_points >= 500) AND (completed_spend < 10000000 AND loyalty_points < 1000)');
        } elseif ($filters['tier'] === 'silver') {
            $query->havingRaw('(completed_spend >= 2000000 OR loyalty_points >= 200) AND (completed_spend < 5000000 AND loyalty_points < 500)');
        } elseif ($filters['tier'] === 'member') {
            $query->havingRaw('(completed_spend >= 1000000 OR loyalty_points >= 100) AND (completed_spend < 2000000 AND loyalty_points < 200)');
        } elseif ($filters['tier'] === 'new') {
            $query->havingRaw('(completed_spend IS NULL OR completed_spend < 1000000) AND (loyalty_points < 100)');
        }

        // Sắp xếp
        if ($filters['sort'] === 'spend_desc') {
            $query->orderByDesc('completed_spend');
        } elseif ($filters['sort'] === 'orders_desc') {
            $query->orderByDesc('orders_count');
        } elseif ($filters['sort'] === 'points_desc') {
            $query->orderByDesc('loyalty_points');
        } elseif ($filters['sort'] === 'name_asc') {
            $query->orderBy('name', 'asc');
        } else {
            $query->latest();
        }

        $members = $query->paginate(15)->withQueryString();

        // Danh sách voucher đang có hiệu lực để tặng quà
        $availableVouchers = Voucher::where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->latest()->get();

        return view('admin.members.index', [
            'members' => $members,
            'filters' => $filters,
            'counts' => [
                'total' => $totalCustomers,
                'new_this_month' => $newThisMonth,
                'buyers' => $buyersCount,
                'conversion_rate' => $conversionRate,
                'total_revenue' => $totalCompletedSpend,
                'avg_spend' => $avgSpendPerBuyer,
                'total_points' => $totalLoyaltyPoints,
                'locked' => $lockedCount,
                'active' => $activeCount,
            ],
            'availableVouchers' => $availableVouchers,
        ]);
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
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Họ và tên khách hàng không được để trống.',
            'email.regex' => 'Email phải có tên miền hợp lệ, ví dụ: ten@gmail.com.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.regex' => 'Số điện thoại Việt Nam không hợp lệ.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'avatar.image' => 'Ảnh đại diện phải là tệp hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng JPG, JPEG, PNG hoặc WEBP.',
            'avatar.max' => 'Dung lượng ảnh đại diện không được vượt quá 2MB.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'avatar_path' => $avatarPath,
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
            'phone' => ['nullable', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Họ và tên khách hàng không được để trống.',
            'email.regex' => 'Email phải có tên miền hợp lệ, ví dụ: ten@gmail.com.',
            'phone.regex' => 'Số điện thoại Việt Nam không hợp lệ.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'avatar.image' => 'Ảnh đại diện phải là tệp hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng JPG, JPEG, PNG hoặc WEBP.',
            'avatar.max' => 'Dung lượng ảnh đại diện không được vượt quá 2MB.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $before = ['name' => $user->name, 'email' => $user->email, 'phone' => $user->phone, 'avatar_path' => $user->avatar_path];
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;

        // Xử lý xóa ảnh đại diện nếu có yêu cầu
        if ($request->boolean('remove_avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = null;
        }

        // Xử lý upload ảnh đại diện mới
        if ($request->hasFile('avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
            $user->login_attempts = 0;
            $user->login_locked_at = null;
        }
        $user->save();

        ActivityLogService::record(
            'customer.updated',
            'Đã cập nhật thông tin tài khoản khách hàng ' . $user->name . '.',
            $user,
            $before,
            ['name' => $user->name, 'email' => $user->email, 'phone' => $user->phone, 'avatar_path' => $user->avatar_path]
        );

        return redirect()->route('admin.customers.show', $user)->with('success', 'Đã cập nhật tài khoản khách hàng thành công.');
    }

    public function updateAvatar(Request $request, User $user)
    {
        abort_if(!in_array($user->role, ['customer', 'user'], true), 404);

        if ($request->boolean('remove_avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = null;
            $user->save();

            ActivityLogService::record(
                'customer.avatar_removed',
                'Đã gỡ ảnh đại diện của khách hàng ' . $user->name . '.',
                $user
            );

            return back()->with('success', 'Đã gỡ ảnh đại diện của khách hàng thành công.');
        }

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'avatar.required' => 'Vui lòng chọn một tệp hình ảnh để tải lên.',
            'avatar.image' => 'Tệp chọn phải là hình ảnh hợp lệ.',
            'avatar.mimes' => 'Chỉ chấp nhận ảnh định dạng JPG, JPEG, PNG hoặc WEBP.',
            'avatar.max' => 'Dung lượng ảnh tối đa 2MB.',
        ]);

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        $user->save();

        ActivityLogService::record(
            'customer.avatar_updated',
            'Đã cập nhật ảnh đại diện mới cho khách hàng ' . $user->name . '.',
            $user
        );

        return back()->with('success', 'Đã cập nhật ảnh đại diện khách hàng thành công.');
    }

    public function adjustPoints(Request $request, User $user)
    {
        abort_if(!in_array($user->role, ['customer', 'user'], true), 404);

        $data = $request->validate([
            'type' => ['required', Rule::in(['add', 'subtract'])],
            'points' => ['required', 'integer', 'min:1', 'max:1000000'],
            'description' => ['required', 'string', 'max:255'],
        ], [
            'points.required' => 'Vui lòng nhập số điểm cần điều chỉnh.',
            'points.min' => 'Số điểm phải lớn hơn 0.',
            'description.required' => 'Vui lòng nhập lý do điều chỉnh điểm.',
        ]);

        $points = (int) $data['points'];
        $currentPoints = (int) $user->loyalty_points;

        if ($data['type'] === 'subtract') {
            if ($currentPoints < $points) {
                return back()->with('error', "Khách hàng hiện chỉ có {$currentPoints} điểm, không đủ để trừ {$points} điểm.");
            }
            $user->decrement('loyalty_points', $points);
            $amount = -$points;
            $msg = "Đã trừ {$points} điểm tích lũy của khách hàng {$user->name}.";
        } else {
            $user->increment('loyalty_points', $points);
            $amount = $points;
            $msg = "Đã cộng thành công {$points} điểm tích lũy cho khách hàng {$user->name}.";
        }

        LoyaltyPointTransaction::create([
            'user_id' => $user->id,
            'order_id' => null,
            'points' => $amount,
            'description' => $data['description'],
        ]);

        ActivityLogService::record(
            'customer.points_adjusted',
            $msg . ' Lý do: ' . $data['description'],
            $user,
            ['loyalty_points' => $currentPoints],
            ['loyalty_points' => $user->fresh()->loyalty_points]
        );

        return back()->with('success', $msg);
    }

    public function giveVoucher(Request $request, User $user)
    {
        abort_if(!in_array($user->role, ['customer', 'user'], true), 404);

        $data = $request->validate([
            'voucher_id' => ['required', 'exists:vouchers,id'],
        ], [
            'voucher_id.required' => 'Vui lòng chọn mã giảm giá cần tặng.',
        ]);

        $voucher = Voucher::findOrFail($data['voucher_id']);

        if ($user->collectedVouchers()->where('voucher_id', $voucher->id)->exists()) {
            return back()->with('error', "Khách hàng {$user->name} đã sở hữu mã voucher [{$voucher->code}] trong ví.");
        }

        $user->collectedVouchers()->attach($voucher->id);

        ActivityLogService::record(
            'customer.voucher_gifted',
            "Đã tặng mã voucher [{$voucher->code}] vào ví của khách hàng {$user->name}.",
            $user,
            null,
            ['voucher_code' => $voucher->code]
        );

        return back()->with('success', "Đã tặng mã giảm giá [{$voucher->code}] vào ví voucher của {$user->name} thành công!");
    }

    public function resetPassword(Request $request, User $user)
    {
        abort_if(!in_array($user->role, ['customer', 'user'], true), 404);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user->password = Hash::make($request->password);
        $user->login_attempts = 0;
        $user->login_locked_at = null;
        $user->save();

        ActivityLogService::record(
            'customer.password_reset',
            'Đã đặt lại mật khẩu mới cho khách hàng ' . $user->name . '.',
            $user,
            null,
            ['email' => $user->email]
        );

        return back()->with('success', 'Đã cập nhật mật khẩu mới thành công cho khách hàng ' . $user->name . '.');
    }

    public function toggleLock(Request $request, User $user)
    {
        abort_if(!in_array($user->role, ['customer', 'user'], true), 404);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Không thể tự khóa tài khoản đang đăng nhập.');
        }

        if ($user->login_locked_at) {
            $user->login_locked_at = null;
            $user->login_attempts = 0;
            $user->save();

            ActivityLogService::record(
                'customer.unlocked',
                'Đã mở khóa tài khoản khách hàng ' . $user->name . '.',
                $user
            );

            return back()->with('success', 'Đã mở khóa tài khoản khách hàng ' . $user->name . '.');
        } else {
            $user->login_locked_at = now();
            $user->save();

            ActivityLogService::record(
                'customer.locked',
                'Đã tạm thời khóa tài khoản khách hàng ' . $user->name . '.',
                $user
            );

            return back()->with('success', 'Đã khóa tài khoản khách hàng ' . $user->name . '. Khách hàng sẽ không thể đăng nhập.');
        }
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

        return redirect()->route('admin.customers.index')->with('success', 'Đã xoá tài khoản ' . $name . '.');
    }

    public function show(User $user)
    {
        abort_if(!in_array($user->role, ['customer', 'user'], true), 404);

        $user->loadCount(['orders', 'collectedVouchers', 'addresses']);
        $user->completed_spend = (float) $user->orders()->whereIn('status', ['paid', 'completed'])->sum('total');
        $membershipTier = User::membershipTierFor($user->completed_spend, (int) $user->loyalty_points);
        
        $transactions = $user->loyaltyPointTransactions()->latest()->paginate(10, ['*'], 'points_page');
        $orders = $user->orders()->latest()->paginate(10, ['*'], 'orders_page');
        $vouchers = $user->collectedVouchers()->latest()->get();
        $addresses = $user->addresses()->latest()->get();

        $availableVouchers = Voucher::where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->latest()->get();

        return view('admin.members.show', compact('user', 'transactions', 'orders', 'vouchers', 'addresses', 'membershipTier', 'availableVouchers'));
    }

    public function export(Request $request)
    {
        $customers = User::whereIn('role', ['customer', 'user'])
            ->withCount('orders')
            ->withSum(['orders as completed_spend' => fn ($orders) => $orders->whereIn('status', ['paid', 'completed'])], 'total')
            ->latest()
            ->get();

        $filename = 'aloha-danh-sach-khach-hang-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Mã KH', 'Họ và tên', 'Email', 'Số điện thoại', 'Hạng thành viên', 'Doanh số mua hàng (VNĐ)', 'Điểm tích lũy', 'Tổng số đơn', 'Trạng thái', 'Ngày đăng ký']);

            foreach ($customers as $c) {
                $spend = (float) ($c->completed_spend ?? 0);
                $tier = User::membershipTierFor($spend, (int) $c->loyalty_points)['name'];
                $status = $c->isLocked() ? 'Bị khóa' : 'Hoạt động';

                fputcsv($handle, [
                    '#' . $c->id,
                    $c->name,
                    $c->email,
                    $c->phone ?? 'N/A',
                    $tier,
                    number_format($spend, 0, ',', '.'),
                    number_format($c->loyalty_points),
                    $c->orders_count,
                    $status,
                    $c->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
