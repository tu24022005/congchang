<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderCancellationService;
use Illuminate\Console\Command;

class CancelExpiredOnlineOrders extends Command
{
    protected $signature = 'orders:cancel-expired-online {--minutes=20 : Minutes before an unpaid online order expires}';
    protected $description = 'Cancel unpaid online orders, restore stock, and release vouchers';

    public function handle(OrderCancellationService $cancellationService): int
    {
        $minutes = max(15, min(30, (int) $this->option('minutes')));
        $cutoff = now()->subMinutes($minutes);
        $cancelled = 0;

        Order::where('status', 'processing')
            ->whereIn('payment_method', ['PAYOS', 'online'])
            ->where('created_at', '<=', $cutoff)
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($orders) use ($cancellationService, &$cancelled): void {
                foreach ($orders as $order) {
                    if ($cancellationService->cancelUnpaidOnline($order)) {
                        $cancelled++;
                    }
                }
            });

        $this->info("Cancelled {$cancelled} expired online order(s).");
        return self::SUCCESS;
    }
}
