<?php

namespace App\Mail;

use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReportRecordWarning;
use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public $warnings;
    public $email;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Back\Invoice $invoice
     */
    public function __construct(AttendanceReportRecordWarning $warnings, $email)
    {
        $this->$warnings = $warnings;
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
            ->subject('انذارات متدربة')
            ->view('emails.warning', [
                'warnings' => $this->warnings,
                'email' => $this->email,
            ]);
    }
}
