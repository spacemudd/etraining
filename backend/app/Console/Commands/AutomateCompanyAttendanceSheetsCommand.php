<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Services\CompanyAttendanceReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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

            //$this->info('Processing company: '.$company->name_ar);

            // Has report for current month?
            $currentMonthReport = $company->company_attendance_reports()
                ->whereBetween('date_from', ['2023-07-01', '2023-07-31'])
                ->first();

            if ($currentMonthReport) {
                // $this->info('Current report already exists for the same period, skipping');
                continue;
            }

            $lastReport = $company->company_attendance_reports()
                ->orderBy('date_from', 'desc')
                ->first();

            if ($lastReport && $lastReport->to_emails) {
                // هل العدد الكلي للمتدربين مطابق لعدد المتدربين في الكشف السابق؟
                // Is the number of trainees equal to the number of trainees in the company?
                if ($lastReport->trainees()->count() !== $company->trainees()->count()) {
                    $this->info('No. of trainees no equal QQQQQQ '.$company->name_ar);
                    //$this->info('Number of trainees in the last report is not equal to the number of trainees in the company. Skipping: '.$company->name_ar);
                    continue;
                }


                // هل ارقام الهويات مطابقة لارقام الهويات لجميع المتدربين في الشركة؟
                // Are the trainees matching the IDs of the all the trainees in the company?
                foreach ($lastReport->trainees as $trainee) {
                    if (! $company->trainees()->where('id', $trainee->id)->first()) {
                        //$this->info('Trainee not found in the company. Skipping: '.$company->name_ar);
                        $this->info('ID no. of trainees changed QQQQQQ '.$company->name_ar);
                        continue 2;
                    }
                }

                //$clone = app()->make(CompanyAttendanceReportService::class)->clone($lastReport->id);

                //$clone = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
                //$clone->date_from = Carbon::parse('2023-07-01')->setTimezone('Asia/Riyadh')->startOfDay();
                //$clone->date_to = Carbon::parse('2023-07-31')->setTimezone('Asia/Riyadh')->endOfDay();
                //$clone->cc_emails = Str::replace('ptc-ksa.com', 'ptc-ksa.net', $lastReport->cc_emails);
                //if ($company->salesperson_email && !Str::contains($clone->cc_emails, $company->salesperson_email)) {
                //    $clone->cc_emails .= ', '.$company->salesperson_email;
                //}
                //$clone->save();
                //app()->make(CompanyAttendanceReportService::class)->approve($clone->id);
            } else {
                if (!$company->email) {
                    $this->info('No email for company. Skipping: '.$company->name_ar);
                    continue;
                }
                //$this->info('No last report. Creating new report - '.$company->name_ar);
                //$report = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
                //$report->date_from = Carbon::parse('2023-07-01')->setTimezone('Asia/Riyadh')->startOfDay();
                //$report->date_to = Carbon::parse('2023-07-31')->setTimezone('Asia/Riyadh')->endOfDay();
                //$report->cc_emails = 'sara@ptc-ksa.net, m_shehatah@ptc-ksa.net, ceo@ptc-ksa.net, mashael.a@ptc-ksa.net';
                //if ($company->salesperson_email && !Str::contains($report->cc_emails, $company->salesperson_email)) {
                //    $report->cc_emails .= ', '.$company->salesperson_email;
                //}
                //$report->save();
                //app()->make(CompanyAttendanceReportService::class)->approve($report->id);
            }
        }

        return 1;
    }
}
