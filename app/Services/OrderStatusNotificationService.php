<?php

namespace App\Services;

use App\Models\Order;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\Log;

class OrderStatusNotificationService
{
    public function notify(Order $order, string $previousStatus): void
    {
        if ($previousStatus === $order->status || !$order->user) {
            return;
        }

        try {
            $order->user->notify(new OrderStatusChanged($order, $previousStatus));
        } catch (\Throwable $exception) {
            Log::error('Order status email notification failed.', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'message' => $exception->getMessage(),
                'exception' => $exception,
            ]);
        }
    }
}
