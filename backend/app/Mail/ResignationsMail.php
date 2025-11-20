<?php

namespace App\Mail;

use App\Models\Back\Resignation;
use App\Services\CompanyMigrationHelper;
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

        $center = $resignation->company->center;
        CompanyMigrationHelper::setMailgunConfigBasedOnDomain($center->domain_name);
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
            ->subject('(#'.$this->resignation->number.') إيقاف برنامج - '.$this->resignation->company->name_ar)
            ->markdown('emails.resignations');
    }

    public function attachResignationFile()
    {
        $file = file_get_contents($this->resignation->media()->first()->getTemporaryUrl(Carbon::now()->addMinutes(5)));
        $this->attachData($file, $this->resignation->media()->first()->file_name);
    }
}
