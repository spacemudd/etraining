<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintMail extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Complaint $complaint
     */
    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
        $center = $complaint->user->trainee->company->center;
        CompanyMigrationHelper::setMailgunConfigBasedOnDomain($center->domain);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('[مهم] شكوى جديدة 🔴')
            ->view('emails.complaint', [
            'complaint' => $this->complaint,
        ]);
    }
}
