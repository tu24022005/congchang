<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('orders.track');
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string|max:50',
            'contact' => 'required|string|max:100',
        ], [
            'order_id.required' => 'Vui lòng nhập mã đơn hàng.',
            'contact.required' => 'Vui lòng nhập số điện thoại hoặc email đặt hàng.',
        ]);

        $orderId = trim(ltrim($validated['order_id'], '#'));
        $contact = trim(strtolower($validated['contact']));

        $order = Order::with(['items.product', 'items.variation'])
            ->where(function ($q) use ($orderId) {
                $q->where('id', $orderId)
                  ->orWhere('tracking_number', $orderId);
            })
            ->where(function ($q) use ($contact) {
                $q->where('phone', $contact)
                  ->orWhere('receiver_phone', $contact)
                  ->orWhereHas('user', function ($uq) use ($contact) {
                      $uq->where('email', $contact)->orWhere('phone', $contact);
                  });
            })
            ->first();

        if (!$order) {
            return back()->withInput()->with('error', 'Không tìm thấy thông tin đơn hàng phù hợp với mã đơn và thông tin liên hệ đã cung cấp. Vui lòng kiểm tra lại.');
        }

        return view('orders.track', compact('order'));
    }
}
