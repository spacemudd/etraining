<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Services\CompanyAttendanceReportService;
use Illuminate\Console\Command;

class AutomateCompanyAttendanceSheetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company-attendance-sheets:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = Company::whereNotNull('is_ptc_net')
            ->get();

        foreach ($companies as $company) {
            if ($company->trainees()->count() === 0) {
                continue;
            }

            $this->info('Processing company: '.$company->name_ar.' - '.$company->id);

            // Has march report?
            $marchReport = $company->company_attendance_reports()
                ->whereBetween('date_from', ['2023-03-01', '2023-03-31'])
                ->first();

            if ($marchReport) {
                // $this->info('March report already exists, skipping');
                continue;
            }

            $lastReport = $company->company_attendance_reports()
                ->orderBy('date_from', 'desc')
                ->first();

            if ($lastReport && $lastReport->to_emails) {
                $clone = app()->make(CompanyAttendanceReportService::class)->clone($lastReport->id);
                $clone->date_from = '2023-03-01';
                $clone->date_to = '2023-03-31';
                $clone->cc_emails = 'sara@ptc-ksa.net, m_shehatah@ptc-ksa.net, ceo@ptc-ksa.net, mashal.a@ptc-ksa.net';
                $clone->save();
                app()->make(CompanyAttendanceReportService::class)->approve($clone->id);
            } else {
                if (!$company->email) {
                    $this->info('No email for company. Skipping: '.$company->name_ar);
                    continue;
                }
                $this->info('No last report. Creating new report - '.$company->name_ar);
                $report = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
                $report->date_from = '2023-03-01';
                $report->date_to = '2023-03-31';
                $report->cc_emails = 'sara@ptc-ksa.net, m_shehatah@ptc-ksa.net, ceo@ptc-ksa.net, mashal.a@ptc-ksa.net';
                $report->save();
                app()->make(CompanyAttendanceReportService::class)->approve($report->id);
            }
        }

        return 1;
    }
}
