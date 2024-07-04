<?php

namespace App\Mail;

use App\Models\Back\TraineeCertificate;
use App\Services\CertificatesService;
use App\Services\CompanyMigrationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TraineeCertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $certificate_id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($certificate_id)
    {
        $this->certificate_id = $certificate_id;

        //$certificate = TraineeCertificate::find($this->certificate_id);
        //$center = $certificate->trainee->company->center;
        //CompanyMigrationHelper::setMailgunConfigBasedOnDomain('ptc-ksa.net');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $certificate = TraineeCertificate::find($this->certificate_id);

        $this->attachReportFile($certificate);

        return $this
            ->subject('شهادة تدريبية - '.$certificate->course->name_ar.' - '.$certificate->trainee->name)
            ->markdown('emails.certificate', [
                'certificate' => $certificate,
            ]);
    }

    public function attachReportFile($certificate)
    {
        $filename = $certificate->id.'-cert.pdf';
        $this->attachData(CertificatesService::new($certificate->id)->pdf()->inline($filename), $filename);
        return $this;
    }
}
