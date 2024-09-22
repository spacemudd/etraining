<?php

namespace App\Mail;

use App\Models\Back\CompanyAttendanceReportsEmail;
use App\Services\CompanyMigrationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyAttendanceFailureMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company_attendance_reports_email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CompanyAttendanceReportsEmail $email)
    {
        $this->company_attendance_reports_email = $email;

        $domain = $email->report->company->center->domain_name ?? 'ptc-ksa.net';
        CompanyMigrationHelper::setMailgunConfigBasedOnDomain($domain);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('❗#'.$this->company_attendance_reports_email->report->number.' - خطأ في الارسال عبر الإيميل - حضور وانصراف')
            ->view('emails.company-attendance-report-email-error', [
                'company_attendance_reports_email' => $this->company_attendance_reports_email,
            ]);
    }
}
