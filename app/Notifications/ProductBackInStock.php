<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductBackInStock extends Notification
{
    public function __construct(public Product $product)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->product->name . ' đã có hàng trở lại')
            ->greeting('Xin chào ' . ($notifiable->name ?? 'quý khách') . ',')
            ->line('Sản phẩm bạn quan tâm hiện đã có hàng trở lại.')
            ->line($this->product->name)
            ->action('Xem sản phẩm', route('products.show', ['product' => $this->product->slug]))
            ->line('Hãy ghé xem sớm để không bỏ lỡ sản phẩm.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'category' => 'stock',
            'title' => $this->product->name . ' đã có hàng',
            'message' => 'Sản phẩm bạn đăng ký báo có hàng hiện đã có thể đặt mua.',
            'url' => route('products.show', ['product' => $this->product->slug]),
        ];
    }
}
