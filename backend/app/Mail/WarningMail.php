<?php

namespace App\Mail;

use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public $warning;
    public $email;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Back\Invoice $invoice
     */
    public function __construct(Trainee $warning, $email)
    {
        $this->$warning = $warning;
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
                'trainee' => $this->warning,
                'email' => $this->email,
            ]);
    }
}
