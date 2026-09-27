<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAlertSubscription;
use App\Notifications\ProductBackInStock;
use Illuminate\Support\Facades\Log;
use App\Services\StaffNotificationService;

class StockAlertService
{
    public function notifyIfRestocked(Product $product, int $previousQuantity, int $currentQuantity): void
    {
        if ($previousQuantity > 0 || $currentQuantity <= 0) {
            return;
        }

        app(StaffNotificationService::class)->notify(
            'Sản phẩm đã có hàng trở lại',
            $product->name . ' vừa được cập nhật có hàng. Có thể kiểm tra và tiếp nhận các yêu cầu báo có hàng.',
            route('admin.products.show', $product),
            'stock'
        );

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
