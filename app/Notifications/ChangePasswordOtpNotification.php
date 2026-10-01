<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChangePasswordOtpNotification extends Notification
{
    public function __construct(
        public string $otp,
        public int $expiresMinutes = 10
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Aloha Beauty] ' . $this->otp . ' là mã OTP đổi mật khẩu của bạn')
            ->greeting('Xin chào ' . ($notifiable->name ?? 'quý khách') . ',')
            ->line('Bạn vừa yêu cầu đổi mật khẩu tài khoản trên hệ thống Aloha Beauty / BeatyCare.')
            ->line('Mã xác thực OTP của bạn là: **' . $this->otp . '**')
            ->line('Mã này có hiệu lực trong vòng ' . $this->expiresMinutes . ' phút.')
            ->line('Tuyệt đối không chia sẻ mã này cho bất kỳ ai, kể cả nhân viên hỗ trợ.')
            ->line('Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email và kiểm tra lại bảo mật tài khoản ngay lập tức.')
            ->salutation('Trân trọng, Đội ngũ Aloha Beauty 🌸');
    }
}
