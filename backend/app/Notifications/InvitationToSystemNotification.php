<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use NotificationChannels\ClickSend\ClickSendChannel;

class InvitationToSystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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

        if ($notifiable->phone) {
            // $notify_via[] = ClickSendChannel::class;
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
        return (new MailMessage)
            ->subject(trans('words.invitation-to-system'))
            ->line(trans('words.we-would-like-to-inform-you-that-you-have-been-invited-to-create-an-account'))
            // ->action(trans('words.access-the-platform'), URL::temporarySignedRoute('invite', now()->addDays(7), $notifiable->id))
            ->action(trans('words.access-the-platform'), route('invite', $notifiable->id))
            ->salutation(trans('words.with-regards'));
    }

    /**
     * SMS message.
     *
     * @param $notifiable
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function getMessage($notifiable)
    {
        // return trans('words.invitation-to-system').' '.URL::temporarySignedRoute('invite', now()->addDays(7), $notifiable->id);
        return trans('words.invitation-to-system').' '.route('invite', $notifiable->id);
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
