<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class MsegatChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (config('msegat.MSEGAT_USERNAME')) {
            if (method_exists($notifiable, 'routeNotificationForMsegat')) {
                $phone = $notifiable->routeNotificationForMsegat($notifiable);
                \Msegat::sendMessage($phone, $notification->getMessage());
            }
        }
    }
}
