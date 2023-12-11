<?php

namespace App\Mail;

use App\Models\Back\Resignation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResignationsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resignation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Resignation $resignation)
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
        $this->attachResignationFile();

        return $this
            ->subject('(#'.$this->resignation->number.') انسحاب متدربة - '.$this->resignation->company->name_ar)
            ->markdown('emails.resignations');
    }

    public function attachResignationFile()
    {
        if (env('APP_ENV') === 'local') return false;
        $file = file_get_contents($this->resignation->media()->first()->getTemporaryUrl(Carbon::now()->addMinutes(5)));
        $this->attachData($file, $this->resignation->media()->first()->file_name);
    }
}
