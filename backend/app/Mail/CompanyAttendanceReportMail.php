<?php

namespace App\Mail;

use App\Exports\CompanyAttendanceSheetExport;

use App\Models\Back\CompanyAttendanceReport;
use App\Services\CompanyAttendanceReportService;
use App\Services\CompanyMigrationHelper;
use Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Str;



class CompanyAttendanceReportMail extends Mailable
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

        $domain = CompanyAttendanceReport::find($this->report_id)->company->center->domain_name ?? 'ptc-ksa.net';
        CompanyMigrationHelper::setMailgunConfigBasedOnDomain($domain);
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
            ->from('system@mg.noreplycenter.com', 'Training Center')
            ->subject('تقرير الحضور للمتدربات - ' . $report->company->name_ar . ' - ' . $report->date_from->format('Y-m-d') . ' - ' . $report->date_to->format('Y-m-d'))
            ->markdown('emails.company-attendance-report', [
                'report' => $report,
            ])
            ->withSwiftMessage(function ($message) {
                $message->getHeaders()
                    ->addTextHeader('X-Mailgun-Variables', '{"company_attendance_report_id": "' . $this->report_id . '"}');
            });

        // $report = CompanyAttendanceReport::find($this->report_id);

        // $this->attachReportFile($report);

        // // Use system@ email because when clients reply to that email, it should be received saved in the company.
        // // $center = CompanyAttendanceReport::find($this->report_id)->company->center;

        // return $this
        //     ->from('system@mg.noreplycenter.com', 'Training Center')
        //     ->subject('تقرير الحضور للمتدربات - '.$report->company->name_ar.' - '.$report->date_from->format('Y-m-d'). ' - '.$report->date_to->format('Y-m-d'))
        //     ->markdown('emails.company-attendance-report', [
        //         'report' => $report,
        //     ])
        //     ->withSwiftMessage(function ($message) {
        //         $message->getHeaders()
        //             ->addTextHeader('X-Mailgun-Variables', '{"company_attendance_report_id": "'.$this->report_id.'"}');
        //     });
    }

    public function attachReportFile($report)
{
    $filename = Str::slug($report->number) . '.xlsx';

    // Generate the Excel file and attach it to the email
    $this->attachData(
        Excel::raw(new CompanyAttendanceSheetExport($report), \Maatwebsite\Excel\Excel::XLSX), 
        $filename, 
        [
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]
    );

    return $this;
}

}
