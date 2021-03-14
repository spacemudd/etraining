<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\ClickSend\ClickSendChannel;

class CustomTraineeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $emailTitle;
    public $emailBody;

    public $smsBody;

    /**
     * Create a new notification instance.
     *
     * @param $emailTitle
     * @param $emailBody
     * @param $smsBody
     */
    public function __construct($emailTitle, $emailBody, $smsBody)
    {
        $this->emailTitle = $emailTitle;
        $this->emailBody = $emailBody;
        $this->smsBody = $smsBody;
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

        if ($notifiable->email && $this->emailBody) {
            $notify_via[] = 'mail';
        }

        if ($notifiable->phone && $this->smsBody) {
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
            ->subject($this->emailTitle)
            ->line($this->emailBody)
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
        return $this->smsBody;
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
