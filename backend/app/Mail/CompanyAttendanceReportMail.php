<?php

namespace App\Mail;

use App\Models\Back\CompanyAttendanceReport;
use App\Services\CompanyAttendanceReportService;
use App\Services\CompanyMigrationHelper;
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

        if (CompanyAttendanceReport::find($report_id)->company->is_ptc_net) {
            CompanyMigrationHelper::setMailgunConfigStatic();
        }
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
            ->subject('تقرير الحضور للمتدربات - '.$report->company->name_ar.' - '.$report->date_from->setTimezone('Asia/Riyadh')->format('Y-m-d'). ' - '.$report->date_to->setTimezone('Asia/Riyadh')->format('Y-m-d'))
            ->from('system@comms.ptc-ksa.net', 'شركة مركز احترافية التدريب.')
            ->markdown('emails.company-attendance-report', [
                'report' => $report,
            ]);
    }

    public function attachReportFile($report)
    {
        $filename = Str::slug($report->number).'.pdf';

        $this->attachData(CompanyAttendanceReportService::makePdf($this->report_id)->inline($filename), $filename);

        return $this;
    }
}
