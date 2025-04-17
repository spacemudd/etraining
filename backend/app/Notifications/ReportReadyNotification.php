<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        $url = url('/storage/' . $this->filePath); // adjust if you use S3 or other storage
        return (new MailMessage)
            ->subject('Your Report is Ready')
            ->line('Your attendance report has been generated.')
            ->action('Download Report', $url)
            ->line('Thank you for using our system!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your report is ready to download.',
            'file_url' => url('/storage/' . $this->filePath),
        ];
    }
}
