<?php

namespace App\Notifications;

use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TraineeRestoredNotification extends Notification
{
    use Queueable;

    public $trainee_id;
    public $done_by;
    public $block_reason;

    /**
     * Create a new notification instance.
     *
     * @param $trainee_id
     * @param $done_by
     */
    public function __construct($trainee_id, $done_by, $block_reason)
    {
        $this->trainee_id = $trainee_id;
        $this->done_by = $done_by;
        $this->block_reason = $block_reason;
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
        $trainee = Trainee::withoutGlobalScopes()->find($this->trainee_id);

        return (new MailMessage)
            ->subject('⚠ Notification: Trainee restored - '.$trainee->name)
            ->bcc('sean.spilot@gmail.com')
            ->view('emails.trainee-restored', [
                'trainee' => $trainee,
                'done_by' => $this->done_by,
                'block_reason' => $this->block_reason,
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
