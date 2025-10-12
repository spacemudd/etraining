<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeLeave;

class MaternityLeaveMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainee;
    public $leave;
    public $emailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Trainee $trainee, TraineeLeave $leave, array $emailData = [])
    {
        $this->trainee = $trainee;
        $this->leave = $leave;
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject('إشعار إجازة وضع - ' . $this->trainee->name)
                    ->view('emails.maternity-leave');

        return $mail;
    }
}
