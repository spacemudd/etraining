<?php

namespace App\Mail;

use App\Models\Back\TraineeWithdraw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TraineeWithdrawalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $withdrawal;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(TraineeWithdraw $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('words.new-withdrawal-request').' # '.$this->withdrawal->number)
            ->view('emails.trainee-withdrawal', [
            'withdrawal' => $this->withdrawal,
        ]);
    }
}
