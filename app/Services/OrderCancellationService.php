<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderVoucherUsage;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class OrderCancellationService
{
    public function cancelUnpaidOnline(Order $order): bool
    {
        return DB::transaction(function () use ($order): bool {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->first();
            if (!$lockedOrder || $lockedOrder->status !== 'processing' || !$this->isOnline($lockedOrder)) {
                return false;
            }

            $this->restoreStock($lockedOrder);
            $this->releaseVouchers($lockedOrder);
            $lockedOrder->update(['status' => 'cancelled']);

            return true;
        });
    }

    public function cancel(Order $order, string $status = 'cancelled'): void
    {
        $this->restoreStock($order);
        $this->releaseVouchers($order);
        $order->update([
            'status' => $status,
            'refund_status' => $status === 'refund_pending' ? 'requested' : null,
        ]);
    }

    public function restoreStock(Order $order): void
    {
        foreach ($order->items()->lockForUpdate()->get() as $item) {
            if ($item->variation_id) {
                $variation = ProductVariation::whereKey($item->variation_id)->lockForUpdate()->first();
                if (!$variation) {
                    continue;
                }

                $beforeStock = (int) $variation->stock;
                $variation->increment('stock', $item->quantity);
                $variation->refresh();
                InventoryLog::record($variation, $beforeStock, (int) $variation->stock, 'Hoàn tồn do hủy đơn', $order);
                continue;
            }

            Product::whereKey($item->product_id)->lockForUpdate()->increment('quantity', $item->quantity);
        }
    }

    public function releaseVouchers(Order $order): void
    {
        $usages = OrderVoucherUsage::where('order_id', $order->id)
            ->whereNull('released_at')
            ->lockForUpdate()
            ->get();

        foreach ($usages as $usage) {
            Voucher::whereKey($usage->voucher_id)
                ->lockForUpdate()
                ->decrement('used_count');
            $usage->update(['released_at' => now()]);
        }
    }

    private function isOnline(Order $order): bool
    {
        return in_array(strtoupper((string) $order->payment_method), ['PAYOS', 'ONLINE'], true);
    }
}
