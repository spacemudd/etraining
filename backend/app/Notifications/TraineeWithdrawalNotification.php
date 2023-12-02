<?php

namespace App\Notifications;

use App\Mail\TraineeWithdrawalMail;
use App\Models\Back\TraineeWithdraw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TraineeWithdrawalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $withdrawal;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TraineeWithdraw $withdraw)
    {
        $this->withdrawal = $withdraw;
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
     * @return \App\Mail\TraineeWithdrawalMail
     */
    public function toMail($notifiable)
    {
        return (new TraineeWithdrawalMail($this->withdrawal))
                ->subject(__('words.new-withdrawal-request').' # '.$this->withdrawal->number)
                ->to($notifiable->email);
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
