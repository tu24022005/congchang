<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAlertSubscription;
use App\Notifications\ProductBackInStock;
use Illuminate\Support\Facades\Log;

class StockAlertService
{
    public function notifyIfRestocked(Product $product, int $previousQuantity, int $currentQuantity): void
    {
        if ($previousQuantity > 0 || $currentQuantity <= 0) {
            return;
        }

        $subscriptions = StockAlertSubscription::with('user')
            ->where('product_id', $product->id)
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                $subscription->user?->notify(new ProductBackInStock($product));
                $subscription->delete();
            } catch (\Throwable $exception) {
                Log::error('Back-in-stock email notification failed.', [
                    'product_id' => $product->id,
                    'user_id' => $subscription->user_id,
                    'message' => $exception->getMessage(),
                    'exception' => $exception,
                ]);
            }
        }
    }
}
