<?php

namespace App\Notifications;

use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManageMissedClassNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $trainee;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Trainee $trainee)
    {
        $this->trainee = $trainee;
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
            ->subject('⚠ غياب لأكثر من 3 مرات: '.$this->trainee->name. ' - '.$this->trainee->identity_number)
            ->bcc('shafiqalshaar@gmail.com')
            ->view('emails.manage-missed-classes-trainee', [
                'trainee' => $this->trainee,
            ]);
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
