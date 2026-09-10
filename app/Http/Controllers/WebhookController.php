<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleBankWebhook(Request $request)
    {
        $data = $request->input('data');
        
        // Trả về 200 ngay nếu là gói tin test của PayOS
        if (!$data) return response()->json(['error' => 0, 'message' => 'Ok'], 200);

        // Lấy đúng ID đơn hàng từ PayOS
        $orderId = $data['orderCode'];
        $amount = $data['amount'];
        
        $order = Order::find($orderId);
        if ($order && $amount >= $order->total) {
            $order->status = 'paid';
            $order->save();
            Log::info("PayOS Đã tự động cập nhật đơn #{$orderId}");
        }

        return response()->json(['error' => 0, 'message' => 'Thành công'], 200);
    }
}