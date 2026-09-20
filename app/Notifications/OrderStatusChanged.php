<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    public function __construct(
        public Order $order,
        public string $previousStatus
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $labels = [
            'processing' => 'Đang xử lý',
            'confirmed' => 'Đã xác nhận',
            'paid' => 'Đã thanh toán',
            'packing' => 'Đang đóng gói',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'refund_pending' => 'Đang chờ hoàn tiền',
            'refunded' => 'Đã hoàn tiền',
        ];

        return (new MailMessage)
            ->subject('Cập nhật đơn hàng #' . $this->order->id)
            ->greeting('Xin chào ' . ($notifiable->name ?? 'quý khách') . ',')
            ->line('Đơn hàng #' . $this->order->id . ' vừa được cập nhật trạng thái.')
            ->line('Trạng thái mới: ' . ($labels[$this->order->status] ?? $this->order->status))
            ->line('Tổng tiền: ' . number_format((float) $this->order->total, 0, ',', '.') . ' đ')
            ->action('Xem đơn hàng', route('orders.show', $this->order))
            ->line('Cảm ơn bạn đã mua hàng.');
    }
}
