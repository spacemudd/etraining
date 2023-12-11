<?php

namespace App\Mail;

use App\Models\Back\Resignation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnreceivedResignationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resignation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function  __construct(Resignation $resignation)
    {
        $this->resignation = $resignation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('❗#'.$this->resignation->number.' - '.'٦ ايام لا يوجد رد على الاستقالة')
            ->view('emails.resignation-no-response-email', [
                'resignation' => $this->resignation,
            ]);
    }
}
