<?php

namespace App\Mail;

use App\Models\Back\CompanyAttendanceReport;
use App\Services\CompanyAttendanceReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Str;

class CompanyAttendanceReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $report_id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($report_id)
    {
        $this->report_id = $report_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $report = CompanyAttendanceReport::find($this->report_id);

        $this->attachReportFile($report);

        return $this
            ->subject('تقرير الحضور للمتدربات - '.$report->date_from->format('Y-m-d'). ' - '.$report->date_to->format('Y-m-d'))
            ->markdown('emails.company-attendance-report');
    }

    public function attachReportFile($report)
    {
        $filename = 'ptc-'.Str::slug($report->number).'.pdf';

        $this->attachData(CompanyAttendanceReportService::makePdf($this->report_id)->inline($filename), $filename);

        return $this;
    }
}