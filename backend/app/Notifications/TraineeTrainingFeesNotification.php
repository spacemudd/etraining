<?php

namespace App\Notifications;

use App\Models\Back\SaleInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TraineeTrainingFeesNotification extends Notification
{
    use Queueable;

    public $invoice;

    public $salesTeamEmail;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Back\SaleInvoice $invoice
     * @param $salesTeamEmail
     */
    public function __construct(SaleInvoice $invoice, $salesTeamEmail)
    {
        $this->invoice = $invoice;
        $this->salesTeamEmail = $salesTeamEmail;
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
            ->from($this->salesTeamEmail)
            ->subject(trans('words.training-fees-is-issued'))
            ->line(trans('words.please-transfer-the-amount').' - '.$this->invoice->grand_total_display)
            ->action(trans('words.access-the-platform'), url('/'))
            ->salutation(trans('words.with-regards'));
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
