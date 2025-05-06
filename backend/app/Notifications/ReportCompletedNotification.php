<?php

namespace App\Notifications;

use App\Models\JobTracker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportCompletedNotification extends Notification
{
    use Queueable;

    public $tracker;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobTracker $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('تقريرك جاهز للتنزيل')
                    ->greeting('مرحباً!')
                    ->line('تم الانتهاء من إعداد التقرير الذي طلبته.')
                    ->action('تحميل التقرير', route('job-trackers.download', $this->tracker->id))
                    ->line('شكراً لاستخدامك نظامنا.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
