<?php

namespace App\Mail;

use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Models\NewEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RejectNewEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $email)
    {
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(' تم رفض الطلب رقم - '.$this->email->number)
            ->view('emails.reject-new-email', [
                'new_emails' => $this->email,
            ]);
    }
}
