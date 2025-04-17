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
            ->subject('Your Report is Ready')
            ->line('Your attendance report has been generated.')
            ->action('Download Report', $this->filePath)
            ->line('Thank you for using our system!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your report is ready to download.',
            'file_url' => $this->filePath,
        ];
    }
}
