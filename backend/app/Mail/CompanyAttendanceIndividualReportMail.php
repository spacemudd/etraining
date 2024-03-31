<?php

namespace App\Mail;

use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\Trainee;
use App\Services\CompanyAttendanceReportService;
use App\Services\CompanyMigrationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Str;

class CompanyAttendanceIndividualReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $report_id;

    public $trainee_id;

    public $with_attendance_times;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($report_id, $trainee_id, bool $with_attendance_times)
    {
        $this->report_id = $report_id;

        $this->trainee_id = $trainee_id;

        $this->with_attendance_times = $with_attendance_times;

        $center = CompanyAttendanceReport::find($report_id)->company->center;
        CompanyMigrationHelper::setMailgunConfigBasedOnDomain(optional($center)->domain_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $report = CompanyAttendanceReport::find($this->report_id);

        $trainee = Trainee::withTrashed()->findOrFail($this->trainee_id);

        $this->attachReportFile($report, $trainee);

        return $this
            ->subject('تقرير الحضور للمتدربة - '.$trainee->identity_number.' - '.$report->date_from->format('Y-m-d'). ' - '.$report->date_to->format('Y-m-d'))
            ->markdown('emails.company-attendance-individual-report', [
                'report' => $report,
                'trainee' => $trainee,
            ]);
    }

    public function attachReportFile($report, Trainee $trainee)
    {
        $filename = Str::slug($report->number).'-'.$trainee->identity_number.'.pdf';

        $pdf = CompanyAttendanceReportService::makeIndividualPdf($this->report_id, $this->trainee_id, $this->with_attendance_times);

        $this->attachData($pdf->inline($filename), $filename);

        return $this;
    }
}
