<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        $companies = Company::whereNotNull('is_ptc_net')
            ->get();

        foreach ($companies as $company) {
            if ($company->trainees()->count() === 0) {
                continue;
            }

            if (Invoice::where('company_id', $company->id)->whereBetween('to_date', ['2023-11-01', '2023-11-30'])->count() === 0) {
                $this->info('No invoices in 2023-11. Skipping: '.$company->name_ar);
                continue;
            }

            // TODO: Has report for current month? Update for the current month
            $currentMonthReport = $company->company_attendance_reports()
                ->whereBetween('date_to', ['2023-12-01', '2023-12-31'])
                ->first();

            if ($currentMonthReport) {
                // $this->info('Current report already exists for the same period, skipping');
                continue;
            }

            $lastReport = $company->company_attendance_reports()
                ->orderBy('date_from', 'desc')
                ->first();

            if ($lastReport && $lastReport->emails()->count()) {
                // Is the number of trainees equal to the number of trainees in the company?
                if ($lastReport->trainees()->count() !== $company->trainees()->count()) {
                    $traineesNotFound = $lastReport->trainees()->pluck('trainees.id')->diff($company->trainees()->pluck('trainees.id'));
                    if ($traineesNotFound->count() > 0) {
                        foreach ($traineesNotFound as $notFound) {
                            $info = [
                                'Count is not equal to last report',
                                $company->name_ar,
                                ($company->trainees()->count() - $lastReport->trainees()->count()),
                                Trainee::withTrashed()->find($notFound)->name,
                            ];
                            $this->info(implode(' , ', $info));
                        }
                    }
                    $this->info('Number of trainees in the last report is not equal to the number of trainees in the company. Skipping: '.$company->name_ar);
                    continue;
                }

                // Are the trainees matching the IDs of the all the trainees in the company?
                foreach ($lastReport->trainees as $trainee) {
                    if (! $company->trainees()->where('id', $trainee->id)->first()) {
                        $this->info('Trainee not found in the company. Skipping: '.$company->name_ar);
                        continue 2;
                    }
                }

                $clone = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
                $clone->date_from = Carbon::parse('2023-12-01')->setTimezone('Asia/Riyadh')->startOfDay();
                $clone->date_to = Carbon::parse('2023-12-31')->setTimezone('Asia/Riyadh')->endOfDay();
                $clone->save();
                $clone->emails()->createMany($lastReport->emails()->get()->map(function ($email) {
                    return [
                        'type' => $email->type,
                        'email' => Str::replace(' ', '', $email->email),
                    ];
                })->toArray());
                app()->make(CompanyAttendanceReportService::class)->approve($clone->id);
                $this->info('Sent company: '.$company->name_ar);
            } else {
                if (!$company->email) {
                    $this->info('No email for company. Skipping: '.$company->name_ar);
                    continue;
                }
                $this->info('No last report. Creating new report - '.$company->name_ar);
                $report = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
                $report->date_from = Carbon::parse('2023-12-01')->setTimezone('Asia/Riyadh')->startOfDay();
                $report->date_to = Carbon::parse('2023-12-31')->setTimezone('Asia/Riyadh')->endOfDay();
                $report->save();

                $emails = [
                    ['type' => 'to', 'email' => $company->email],
                    ['type' => 'cc',' email' => 'sara@ptc-ksa.net'],
                    ['type' => 'cc', 'email' => 'm_shehatah@ptc-ksa.net'],
                    ['type' => 'cc', 'email' => 'ceo@ptc-ksa.net'],
                    ['type' => 'cc', 'email' => 'mashael.a@ptc-ksa.net'],
                ];
                if ($company->salesperson_email) {
                    $emails[] = ['type' => 'cc', 'email' => $company->salesperson_email];
                }
                $report->emails()->createMany($emails);

                app()->make(CompanyAttendanceReportService::class)->approve($report->id);
            }
        }

        return 1;
    }
}
