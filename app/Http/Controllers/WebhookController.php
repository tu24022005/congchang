<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use PayOS\PayOS;

class WebhookController extends Controller
{
    public function handleBankWebhook(Request $request)
    {
        $payload = $request->all();
        
        // Trả về 200 ngay nếu là gói tin test của PayOS
        if (!$payload || !$request->input('data')) {
            return response()->json(['error' => 0, 'message' => 'Ok'], 200);
        }

        try {
            $payOS = new PayOS(
                env('PAYOS_CLIENT_ID'),
                env('PAYOS_API_KEY'),
                env('PAYOS_CHECKSUM_KEY')
            );
            $data = $payOS->webhooks->verify($payload, ['asArray' => true]);
        } catch (\Throwable $exception) {
            Log::warning('PayOS webhook signature verification failed.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['error' => 1, 'message' => 'Invalid webhook signature'], 400);
        }

        // Chỉ cập nhật đơn khi webhook đã được PayOS xác thực chữ ký.
        $orderId = (int) $data['orderCode'];
        $amount = (int) $data['amount'];
        
        $order = Order::find($orderId);
        if ($order && $order->payment_method === 'PAYOS' && $amount >= (int) $order->total) {
            $order->status = 'paid';
            $order->save();
            Log::info("PayOS Đã tự động cập nhật đơn #{$orderId}");
        }

        return response()->json(['error' => 0, 'message' => 'Thành công'], 200);
    }
}