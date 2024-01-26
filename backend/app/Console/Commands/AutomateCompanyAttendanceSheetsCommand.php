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
        $this->createReportsBasedOnTraineedInvoiced();
        return 1;

        // ------------------------------------------------------------------

        $this->handleReportsBasedOnInvoices();
        return 1;

        //-------------------------------------------------------------------

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
                ->whereBetween('date_to', [Carbon::parse('2023-12-01')->startOfDay(), Carbon::parse('2023-12-31')->endOfDay()])
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
                    $emails[] = ['type' => 'to', 'email' => $company->salesperson_email];
                }
                $report->emails()->createMany($emails);

                app()->make(CompanyAttendanceReportService::class)->approve($report->id);
            }
        }

        return 1;
    }

    public function handleReportsBasedOnInvoices()
    {
        $count = Company::with('invoices')
            ->whereHas('invoices', function ($query) {
                $query->whereBetween('to_date', [Carbon::parse('2023-11-01')->startOfDay(), Carbon::parse('2023-11-30')->endOfDay()]);
            })->count();
        $this->info('Found companies with invoices: '.$count);

        Company::with('invoices')
            ->whereHas('invoices', function ($query) {
                $query->whereBetween('to_date', [Carbon::parse('2023-11-01')->startOfDay(), Carbon::parse('2023-11-30')->endOfDay()]);
            })->chunk(20, function($companies) {
                foreach ($companies as $company) {

                    // Checks
                    if ($company->trainees()->count() === 0) {
                        // $this->info('No trainees. Skipping: '.$company->name_ar);
                        continue;
                    }
                    $currentMonthReport = $company->company_attendance_reports()
                        ->whereBetween('date_to', [Carbon::parse('2023-12-01')->startOfDay(), Carbon::parse('2023-12-31')->endOfDay()])
                        ->first();
                    if ($currentMonthReport) {
                        // $this->info('Already created. Skipping: '.$company->name_ar);
                        continue;
                    }

                    $lastReport = $company->company_attendance_reports()
                        ->orderBy('date_from', 'desc')
                        ->first();

                    if ($lastReport) {
                        //$this->makeNewReportFromLastReport($company, $lastReport);
                        $this->info('Making report for company: '.$company->name_ar);
                    } else {
                        if (! $company->email) {
                            $this->info('No email for company. Skipping: '.$company->name_ar);
                            continue;
                        }
                        $this->info('Making report for company: '.$company->name_ar);
                        //$this->makeNewReport($company);
                    }
                }
            });
    }

    public function makeNewReportFromLastReport($company, $lastReport)
    {
        $this->info('New report from last report: '.$company->name_ar . ',' . $company->trainees()->count());
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
    }

    public function makeNewReport($company)
    {
        $this->info('No last report. Creating new report - '.$company->name_ar . ',' . $company->trainees()->count());
        $report = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
        $report->date_from = Carbon::parse('2023-12-01')->setTimezone('Asia/Riyadh')->startOfDay();
        $report->date_to = Carbon::parse('2023-12-31')->setTimezone('Asia/Riyadh')->endOfDay();
        $report->save();

        $emails = [
            ['type' => 'to', 'email' => $company->email],
            ['type' => 'cc', 'email' => 'sara@ptc-ksa.net'],
            ['type' => 'cc', 'email' => 'm_shehatah@ptc-ksa.net'],
            ['type' => 'cc', 'email' => 'ceo@ptc-ksa.net'],
            ['type' => 'cc', 'email' => 'mashael.a@ptc-ksa.net'],
        ];
        if ($company->salesperson_email) {
            $emails[] = ['type' => 'to', 'email' => $company->salesperson_email];
        }
        $report->emails()->createMany($emails);

        app()->make(CompanyAttendanceReportService::class)->approve($report->id);
    }

    public function createReportsBasedOnTraineedInvoiced()
    {
        $count = Company::with('invoices')
            ->whereHas('invoices', function ($query) {
                $query->whereBetween('to_date', [Carbon::parse('2023-12-01')->startOfDay(), Carbon::parse('2023-12-31')->endOfDay()]);
            })->count();
        $this->info('Found companies with invoices: '.$count);

        Company::with('invoices')
            ->whereHas('invoices', function ($query) {
                $query->whereBetween('to_date', [Carbon::parse('2023-12-01')->startOfDay(), Carbon::parse('2023-12-31')->endOfDay()]);
            })->chunk(20, function($companies) {
                foreach ($companies as $company) {

                    // Checks
                    if ($company->trainees()->count() === 0) {
                        $this->info('No trainees. Skipping: '.$company->name_ar);
                        continue;
                    }
                    $currentMonthReport = $company->company_attendance_reports()
                        ->whereBetween('date_to', [Carbon::parse('2024-01-01')->startOfDay(), Carbon::parse('2024-01-31')->endOfDay()])
                        ->first();
                    if ($currentMonthReport) {
                        $this->info('Already created. Skipping: '.$company->name_ar);
                        continue;
                    }

                    $lastReport = $company->company_attendance_reports()
                        ->orderBy('date_from', 'desc')
                        ->first();

                    if ($lastReport) {
                        $this->makeNewReportFromLastReportBasedOnInvoices($company, $lastReport, '2024-01-01', '2024-01-31', '2023-12-01', '2023-12-31');
                    } else {
                        if (! $company->email) {
                            $this->info('No email for company. Skipping: '.$company->name_ar);
                            continue;
                        }
                        $this->makeNewReportBasedOnInvoices($company, '2024-01-01', '2024-01-31', '2023-12-01', '2023-12-31');
                    }
                }
            });
    }

    public function makeNewReportFromLastReportBasedOnInvoices($company, $lastReport, $dateFrom, $dateTo, $invoicesDateFrom, $invoicesDateTo)
    {
        $this->info('New report from last report: '.$company->name_ar . ',' . $company->trainees()->count());
        $clone = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
        $clone->date_from = Carbon::parse($dateFrom)->setTimezone('Asia/Riyadh')->startOfDay();
        $clone->date_to = Carbon::parse($dateTo)->setTimezone('Asia/Riyadh')->endOfDay();
        $clone->save();
        $clone->emails()->createMany($lastReport->emails()->get()->map(function ($email) {
            return [
                'type' => $email->type,
                'email' => Str::replace(' ', '', $email->email),
            ];
        })->toArray());

        $trainee_ids = Invoice::where('company_id', $company->id)
            ->whereBetween('to_date', [$invoicesDateFrom, $invoicesDateTo])
            ->pluck('trainee_id');

        $clone->trainees()->sync($trainee_ids);

        app()->make(CompanyAttendanceReportService::class)->approve($clone->id);
    }

    public function makeNewReportBasedOnInvoices($company, $dateFrom, $dateTo, $invoicesDateFrom, $invoicesDateTo)
    {
        $this->info('No last report. Creating new report - '.$company->name_ar . ',' . $company->trainees()->count());
        $report = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
        $report->date_from = Carbon::parse($dateFrom)->setTimezone('Asia/Riyadh')->startOfDay();
        $report->date_to = Carbon::parse($dateTo)->setTimezone('Asia/Riyadh')->endOfDay();
        $report->save();

        $emails = [
            ['type' => 'to', 'email' => $company->email],
            ['type' => 'cc', 'email' => 'sara@ptc-ksa.net'],
            ['type' => 'cc', 'email' => 'm_shehatah@ptc-ksa.net'],
            ['type' => 'cc', 'email' => 'ceo@ptc-ksa.net'],
            ['type' => 'cc', 'email' => 'mashael.a@ptc-ksa.net'],
        ];
        if ($company->salesperson_email) {
            $emails[] = ['type' => 'to', 'email' => $company->salesperson_email];
        }
        $report->emails()->createMany($emails);

        $trainee_ids = Invoice::where('company_id', $company->id)
            ->whereBetween('to_date', [$invoicesDateFrom, $invoicesDateTo])
            ->pluck('trainee_id');

        $report->trainees()->sync($trainee_ids);

        app()->make(CompanyAttendanceReportService::class)->approve($report->id);
    }
}
