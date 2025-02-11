<?php

namespace App\Mail;

use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractSigned extends Mailable
{
    use Queueable, SerializesModels;
    public $trainee;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Trainee $trainee)
    {
        $this->trainee = $trainee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Contract Has Been Signed')
        ->view('emails.contract_signed')
        ->with([
            'name' => $this->trainee->name,
            'contract_id' => $this->trainee->zoho_contract_id,
        ]);
    }
}
