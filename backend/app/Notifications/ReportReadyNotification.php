<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ReportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function via($notifiable)
    {
        return ['mail']; // Email + database notifications
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تقرير الحضور جاهز')
            ->line('تم إنشاء تقرير الحضور الخاص بك بنجاح.')
            ->action('تحميل التقرير', $this->filePath)
            ->line('شكرًا لاستخدامك لنظامنا.')
            ->salutation('تحياتنا');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your report is ready to download.',
            'file_url' => $this->filePath,
        ];
    }
}
