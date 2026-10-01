<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyPointTransaction;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoyaltyController extends Controller
{
    // Cấu hình hạng thành viên
    const TIERS = [
        ['name' => 'Mới tham gia', 'threshold' => 0,        'points' => 0,    'icon' => '🌱', 'color' => '#9ca3af', 'bg' => '#f3f4f6'],
        ['name' => 'Thành viên',   'threshold' => 1000000,  'points' => 100,  'icon' => '🥉', 'color' => '#92400e', 'bg' => '#fef3c7'],
        ['name' => 'Bạc',          'threshold' => 2000000,  'points' => 200,  'icon' => '🥈', 'color' => '#475569', 'bg' => '#f1f5f9'],
        ['name' => 'Vàng',         'threshold' => 5000000,  'points' => 500,  'icon' => '🥇', 'color' => '#b45309', 'bg' => '#fffbeb'],
        ['name' => 'Bạch kim',     'threshold' => 10000000, 'points' => 1000, 'icon' => '💎', 'color' => '#0891b2', 'bg' => '#ecfeff'],
        ['name' => 'Kim cương',    'threshold' => 20000000, 'points' => 2000, 'icon' => '👑', 'color' => '#7c3aed', 'bg' => '#f5f3ff'],
    ];

    // Danh sách quà có thể đổi điểm
    const REWARDS = [
        ['points' => 100,  'label' => 'Voucher giảm 10.000đ',   'icon' => 'bi-ticket-perforated-fill', 'color' => '#ef4444', 'value' => 10000,   'type' => 'fixed'],
        ['points' => 500,  'label' => 'Voucher giảm 60.000đ',   'icon' => 'bi-ticket-perforated-fill', 'color' => '#f59e0b', 'value' => 60000,   'type' => 'fixed'],
        ['points' => 1000, 'label' => 'Voucher giảm 150.000đ',  'icon' => 'bi-ticket-perforated-fill', 'color' => '#10b981', 'value' => 150000,  'type' => 'fixed'],
        ['points' => 2000, 'label' => 'Voucher giảm 350.000đ',  'icon' => 'bi-ticket-perforated-fill', 'color' => '#3b82f6', 'value' => 350000,  'type' => 'fixed'],
        ['points' => 5000, 'label' => 'Voucher giảm 1.000.000đ','icon' => 'bi-gift-fill',              'color' => '#7c3aed', 'value' => 1000000, 'type' => 'fixed'],
    ];

    public function index()
    {
        $user = Auth::user();
        $transactions = LoyaltyPointTransaction::where('user_id', $user->id)->latest()->paginate(10);

        // Tính tier hiện tại
        $completedSpend = (float) $user->orders()->whereIn('status', ['paid', 'completed'])->sum('total');
        $effectiveValue = max($completedSpend, (float) ($user->loyalty_points * 10000));
        $membershipTier = User::membershipTierFor($completedSpend, (int) $user->loyalty_points);

        $tiers = self::TIERS;
        $nextTier = collect($tiers)->first(fn ($t) => $effectiveValue < $t['threshold']);
        $prevThreshold = collect($tiers)->last(fn ($t) => $effectiveValue >= $t['threshold'])['threshold'] ?? 0;
        $tierProgress = $nextTier
            ? min(100, max(0, (($effectiveValue - $prevThreshold) / max(1, $nextTier['threshold'] - $prevThreshold)) * 100))
            : 100;

        $rewards = self::REWARDS;

        // Thống kê tổng điểm đã tích / đã dùng
        $pointsEarned = (int) LoyaltyPointTransaction::where('user_id', $user->id)->where('points', '>', 0)->sum('points');
        $pointsSpent  = (int) abs(LoyaltyPointTransaction::where('user_id', $user->id)->where('points', '<', 0)->sum('points'));

        return view('loyalty.index', compact(
            'user', 'transactions', 'tiers', 'membershipTier', 'nextTier',
            'tierProgress', 'completedSpend', 'effectiveValue',
            'rewards', 'pointsEarned', 'pointsSpent'
        ));
    }

    public function redeem(Request $request)
    {
        $user = Auth::user();
        $pointsNeeded = (int) $request->input('points', 100);

        // Tìm reward khớp với số điểm
        $reward = collect(self::REWARDS)->firstWhere('points', $pointsNeeded);
        if (!$reward) {
            return back()->with('error', 'Gói đổi điểm không hợp lệ.');
        }

        $code = 'DIEM' . strtoupper(Str::random(8));

        try {
            DB::transaction(function () use ($user, $code, $reward, $pointsNeeded) {
                $lockedUser = $user->newQuery()->lockForUpdate()->findOrFail($user->id);
                if ($lockedUser->loyalty_points < $pointsNeeded) {
                    throw new \RuntimeException("Bạn cần tối thiểu {$pointsNeeded} điểm để đổi phần thưởng này.");
                }

                $lockedUser->decrement('loyalty_points', $pointsNeeded);
                Voucher::create([
                    'code'            => $code,
                    'scope'           => 'shop',
                    'user_id'         => $lockedUser->id,
                    'type'            => 'fixed',
                    'value'           => $reward['value'],
                    'min_order_value' => 0,
                    'usage_limit'     => 1,
                    'expires_at'      => today()->addDays(30),
                ]);
                LoyaltyPointTransaction::create([
                    'user_id'     => $lockedUser->id,
                    'points'      => -$pointsNeeded,
                    'description' => "Đổi {$pointsNeeded} điểm lấy {$reward['label']}",
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Đổi điểm thành công! Mã voucher: <strong>{$code}</strong> — có hiệu lực 30 ngày.");
    }
}
