<?php

namespace App\Services;

use App\Models\LoyaltyPointTransaction;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class LoyaltyPointService
{
    public function awardForCompletedOrder(Order $order): int
    {
        if (!$order->user_id || $order->status !== 'completed') {
            return 0;
        }

        $points = (int) floor((float) $order->total / 10000);
        if ($points < 1) {
            return 0;
        }

        return DB::transaction(function () use ($order, $points) {
            $exists = LoyaltyPointTransaction::where('order_id', $order->id)->exists();
            if ($exists) {
                return 0;
            }

            $user = $order->user()->lockForUpdate()->first();
            $user->increment('loyalty_points', $points);
            LoyaltyPointTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'points' => $points,
                'description' => 'Tích điểm từ đơn hàng #' . $order->id,
            ]);

            return $points;
        });
    }
}
