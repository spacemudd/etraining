<?php

namespace App\Mail;

use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeletedTraineeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainee;
    public $email;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Back\Invoice $invoice
     */
    public function __construct(Trainee $trainee, $email)
    {
        $this->trainee = $trainee;
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
            ->subject('حذف متدربة')
            ->view('emails.deleted-trainee', [
                'trainee' => $this->trainee,
                'email' => $this->email,
            ]);
    }
}
