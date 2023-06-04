<?php

namespace App\Mail;

use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Models\NewEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $new_email;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\NewEmail $new_email
     */
    public function __construct(NewEmail $new_email)
    {
        $this->new_email = $new_email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('طلب انشاء بريد الكتروني جديد')
            ->view('emails.new-email', [
                'new_emails' => $this->new_email,
            ]);
    }
}
