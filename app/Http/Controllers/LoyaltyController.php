<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyPointTransaction;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoyaltyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transactions = LoyaltyPointTransaction::where('user_id', $user->id)->latest()->paginate(10);

        return view('loyalty.index', compact('user', 'transactions'));
    }

    public function redeem(Request $request)
    {
        $user = Auth::user();
        $code = 'DIEM' . strtoupper(\Illuminate\Support\Str::random(8));

        try {
            DB::transaction(function () use ($user, $code) {
                $lockedUser = $user->newQuery()->lockForUpdate()->findOrFail($user->id);
                if ($lockedUser->loyalty_points < 100) {
                    throw new \RuntimeException('Bạn cần tối thiểu 100 điểm để đổi voucher.');
                }

                $lockedUser->decrement('loyalty_points', 100);
                Voucher::create([
                    'code' => $code,
                    'scope' => 'shop',
                    'user_id' => $lockedUser->id,
                    'type' => 'fixed',
                    'value' => 10000,
                    'min_order_value' => 0,
                    'usage_limit' => 1,
                    'expires_at' => today()->addDays(30),
                ]);
                LoyaltyPointTransaction::create([
                    'user_id' => $lockedUser->id,
                    'points' => -100,
                    'description' => 'Đổi 100 điểm lấy voucher giảm 10.000đ',
                ]);
            });
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Đổi điểm thành công. Mã voucher của bạn là ' . $code);
    }
}
