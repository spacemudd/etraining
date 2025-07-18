<?php

namespace App\Notifications;

use App\Models\Back\CourseBatchSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TraineeLateToClassNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $session;

    public $tries = 0;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Back\CourseBatchSession $session
     */
    public function __construct(CourseBatchSession $session)
    {
        $this->session = $session;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $notify_via = [];

        if ($notifiable->email) {
            $notify_via[] = 'mail';
        }

//        if ($notifiable->routeNotificationForClickSend()) {
//            //$notify_via[] = ClickSendChannel::class;
//        }

        return $notify_via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $dayName = $this->session->starts_at->locale('ar')->getTranslatedDayName();
        $dayDate = $this->session->starts_at->now()->toDateString();
        return (new MailMessage)
            ->subject('انذار تأخر البرنامج التدريبي')
            ->line('عزيزتي المتدربة \ '.$notifiable->name)
            ->line(
                'نظرا لتأخركم اليوم '.$dayName.' الموافق لـ '.$dayDate.' للبرنامج التدريبي وبناء عليه تم انذاركم.',
            )
            ->line('نأمل منكم الالتزام بحضور البرنامج التدريبي والالتزام بالاوقات المحددة.')
            ->salutation('⠀')
            ->greeting('⠀');
    }

    /**
     * SMS message.
     *
     * @param $notifiable
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function getMessage($notifiable)
    {
        return 'تم انذاركم لتأخركم عن البرنامج التدريبي،
        نأمل الالتزام بمواعيد الدورات التدريبية وشكراً';
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
