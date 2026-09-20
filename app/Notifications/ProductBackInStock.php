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
        return ['mail'];
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
}
