<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\StaffCustomerActivity;
use Illuminate\Support\Facades\Log;
use Throwable;

class StaffNotificationService
{
    private const RECIPIENT_ROLES = ['admin', 'manager', 'warehouse_staff'];

    public function notify(string $title, string $message, ?string $url = null): void
    {
        User::query()
            ->whereIn('role', self::RECIPIENT_ROLES)
            ->get()
            ->each(function (User $user) use ($title, $message, $url): void {
                try {
                    $user->notify(new StaffCustomerActivity($title, $message, $url));
                } catch (Throwable $exception) {
                    Log::error('Staff notification delivery failed.', [
                        'user_id' => $user->id,
                        'title' => $title,
                        'exception' => $exception,
                    ]);
                }
            });
    }
}
