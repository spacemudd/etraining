<?php

namespace App\Notifications;

use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TraineeRestoredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $trainee_name;
    public $trainee_email;
    public $trainee_phone;
    public $done_by;
    public $block_reason;

    /**
     * Create a new notification instance.
     *
     * @param $trainee_name
     * @param $trainee_email
     * @param $trainee_phone
     * @param $done_by
     * @param $block_reason
     */
    public function __construct($trainee_name, $trainee_email, $trainee_phone, $done_by, $block_reason)
    {
        $this->trainee_name = $trainee_name;
        $this->trainee_phone = $trainee_phone;
        $this->trainee_email = $trainee_email;
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
        return (new MailMessage)
            ->subject('⚠ Notification: Trainee restored - '.$this->trainee_name)
            ->view('emails.trainee-restored', [
                'trainee_name' => $this->trainee_name,
                'trainee_phone' => $this->trainee_phone,
                'trainee_email' => $this->trainee_email,
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
