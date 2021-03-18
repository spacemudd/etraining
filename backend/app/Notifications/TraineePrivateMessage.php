<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\ClickSend\ClickSendChannel;

class TraineePrivateMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public $email_title;

    public $email_body;

    public $sms_body;

    /**
     * Create a new notification instance.
     *
     * @param string $email_title
     * @param string $email_body
     * @param string $sms_body
     */
    public function  __construct(string $email_title, string $email_body, string $sms_body)
    {
        $this->email_title = $email_title;
        $this->email_body = $email_body;
        $this->sms_body = $sms_body;
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

        if (Str::start($notifiable->phone, '9665')) {
            $notify_via[] = ClickSendChannel::class;
        }

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
        return (new MailMessageMultiline())
            ->subject($this->email_title)
            ->line($this->email_body)
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
        return $this->sms_body;
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
