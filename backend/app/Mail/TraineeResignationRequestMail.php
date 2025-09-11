<?php

namespace App\Mail;

use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TraineeResignationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainee;
    public $contactPhone;

    /**
     * Create a new message instance.
     *
     * @param Trainee $trainee
     * @param string|null $contactPhone
     */
    public function __construct(Trainee $trainee, $contactPhone = null)
    {
        $this->trainee = $trainee;
        $this->contactPhone = $contactPhone;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('طلب استقالة متدرب - ' . $this->trainee->name)
            ->markdown('emails.trainee-resignation-request');
    }
}
