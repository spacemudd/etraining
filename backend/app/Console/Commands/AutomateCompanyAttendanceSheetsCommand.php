<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
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
     */
    public function handle()
    {
        $from_date = Carbon::parse('2024-06-01')->startOfDay();
        $to_date = Carbon::parse('2024-06-31')->endOfDay();
        $this->createReportsBasedOnTraineedInvoiced($from_date, $to_date);
        return 1;
    }

    public function createReportsBasedOnTraineedInvoiced(Carbon $from_date, Carbon $to_date)
    {
        $count = Company::with('invoices')
            ->whereHas('invoices', function ($query) use ($from_date) {
                $query->whereBetween('to_date', ['2024-05-01', '2024-05-31']);
            })->count();
        $this->info('Found companies with invoices: '.$count);

        Company::with('invoices')->whereHas('invoices', function ($query) use ($from_date) {
            $query->whereBetween('to_date', ['2024-05-01', '2024-05-31']);})->count();

        // Companies that don't have invoices in the past month, to skip.
         $companies_with_invoices = Company::with('invoices')
            ->whereHas('invoices', function ($query) use ($from_date) {
                $query->whereBetween('to_date',  ['2024-05-01', '2024-05-31']);
            })->pluck('id');
         $companies_without_invoices = Company::whereNotIn('id', $companies_with_invoices)->pluck('name_ar');
         foreach ($companies_without_invoices as $name_ar) {
             $this->info('No invoices for company: '.$name_ar);
         }

        Company::with('invoices')
            //->whereHas('invoices', function ($query) use ($from_date) {
            //    $query->whereBetween('to_date', ['2024-05-01', '2024-05-31']);
            //})
            ->chunk(20, function($companies) use ($from_date, $to_date) {
                foreach ($companies as $company) {

                    $companies_to_execlude = [
                        'd31fcf66-1010-4d5f-83ff-10e92ca0f902',
                        '3134cbb3-a1e5-41da-8f46-7eb8da78aad7',
                        '9d6ad117-d2e5-4148-a8de-fc92fefffea7',
                    ];

                    if (in_array($company->id, $companies_to_execlude, true)) {
                        $this->info('Excluded company: '.$company->name_ar);
                        continue;
                    }

                    // Checks
                    if ($company->trainees()->count() === 0) {
                        $this->info('No trainees. Skipping: '.$company->name_ar);
                        continue;
                    }
                    $currentMonthReport = $company->company_attendance_reports()
                        ->whereBetween('date_to', [$from_date, $to_date])
                        ->first();
                    if ($currentMonthReport) {
                        $this->info('Already created. Skipping: '.$company->name_ar);
                        continue;
                    }

                    $lastReport = $company->company_attendance_reports()
                        ->orderBy('date_from', 'desc')
                        ->first();

                    if ($lastReport) {
                        $this->info('New report from last report: '.$company->name_ar . ',' . $company->trainees()->count());
                        $this->makeNewReportFromLastReportBasedOnInvoices($company, $lastReport, '2024-06-01', '2024-06-30', '2024-05-01', '2024-05-31');
                    } else {
                        if (! $company->email) {
                            $this->info('No email for company. Skipping: '.$company->name_ar);
                            continue;
                        }
                        $this->info('No last report. Creating new report - '.$company->name_ar . ',' . $company->trainees()->count());
                        $this->makeNewReportBasedOnInvoices($company, '2024-06-01', '2024-06-30', '2024-05-01', '2024-05-30');
                    }
                }
            });
    }

    public function makeNewReportFromLastReportBasedOnInvoices($company, $lastReport, $dateFrom, $dateTo, $invoicesDateFrom, $invoicesDateTo)
    {
        $clone = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
        $clone->date_from = Carbon::parse($dateFrom)->setTimezone('Asia/Riyadh')->startOfDay();
        $clone->date_to = Carbon::parse($dateTo)->setTimezone('Asia/Riyadh')->endOfDay();
        $clone->save();
        $clone->emails()->createMany($lastReport->emails()->get()->map(function ($email) {
            return [
                'type' => Str::contains($email->email, 'ptc-ksa') ? 'bcc' : $email->type,
                'email' => Str::replace(' ', '', $email->email),
            ];
        })->toArray());

        $emails = explode(', ', $company->email);
        foreach ($emails as $email) {
            if ($clone->emails()->where('email', $email)->count()) {
                continue;
            }
            $clone->emails()->create([
                'type' => 'to',
                'email' => $email,
            ]);
        }
        // Add sales rep email
        if ($company->salesperson_email && !$clone->emails()->where('email', $company->salesperson_email)->count()) {
            $clone->emails()->create([
                'type' => 'bcc',
                'email' => $company->salesperson_email,
            ]);
        }

        $this->updateReportEmailsPerToCenter($clone);

        $trainee_ids = Invoice::where('company_id', $company->id)
            ->whereBetween('to_date', [$invoicesDateFrom, $invoicesDateTo])
            ->pluck('trainee_id');

        $clone->trainees()->sync($trainee_ids);

        // Check if a trainee in the current report has any resignations.
        foreach ($clone->trainees as $trainee) {
            if ($trainee->resignations()->whereBetween('created_at', [$clone->date_from, $clone->date_to])->count()) {
                $clone->trainees()->detach($trainee->id);
                $this->info('Trainee has resignations. Removed: '.$trainee->name. ' CompanyName: '.$company->name_ar);
            }
        }

        app()->make(CompanyAttendanceReportService::class)->approve($clone->id);
    }

    public function makeNewReportBasedOnInvoices($company, $dateFrom, $dateTo, $invoicesDateFrom, $invoicesDateTo)
    {
        $report = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
        $report->date_from = Carbon::parse($dateFrom)->setTimezone('Asia/Riyadh')->startOfDay();
        $report->date_to = Carbon::parse($dateTo)->setTimezone('Asia/Riyadh')->endOfDay();
        $report->save();

        $emails = [
            ['type' => 'to', 'email' => $company->email],
            ['type' => 'bcc', 'email' => 'sara@ptc-ksa.net'],
            ['type' => 'bcc', 'email' => 'm_shehatah@ptc-ksa.net'],
            ['type' => 'bcc', 'email' => 'ceo@ptc-ksa.net'],
            ['type' => 'bcc', 'email' => 'mashael.a@ptc-ksa.net'],
        ];
        if ($company->salesperson_email) {
            $emails[] = ['type' => 'to', 'email' => $company->salesperson_email];
        }
        $report->emails()->createMany($emails);

        $emails = explode(', ', $company->email);
        foreach ($emails as $email) {
            if ($report->emails()->where('email', $email)->count()) {
                continue;
            }
            $report->emails()->create([
                'type' => 'to',
                'email' => $email,
            ]);
        }
        // Add sales rep email
        if ($company->salesperson_email && !$report->emails()->where('email', $company->salesperson_email)->count()) {
            $report->emails()->create([
                'type' => 'cc',
                'email' => $company->salesperson_email,
            ]);
        }

        $this->updateReportEmailsPerToCenter($report);

        $trainee_ids = Invoice::where('company_id', $company->id)
            ->whereBetween('to_date', [$invoicesDateFrom, $invoicesDateTo])
            ->pluck('trainee_id');

        $report->trainees()->sync($trainee_ids);

        // Check if a trainee in the current report has any resignations.
        foreach ($report->trainees as $trainee) {
            if ($trainee->resignations()->whereBetween('created_at', [$report->date_from, $report->date_to])->count()) {
                $report->trainees()->detach($trainee->id);
                $this->info('Trainee has resignations. Removed: '.$trainee->name);
            }
        }

        app()->make(CompanyAttendanceReportService::class)->approve($report->id);
    }

    public function updateReportEmailsPerToCenter(CompanyAttendanceReport $report)
    {
        if (Str::contains($report->company->center->domain_name, 'jisr') || Str::contains($report->company->center->domain_name, 'jasarah')) {
            $bccEmails = $report->emails()->where('type', 'bcc')->where('email', 'LIKE', '%ptc-ksa%')->get();
            foreach ($bccEmails as $bccEmail) {
                $bccEmail->update(['email' => Str::before($bccEmail->email, '@') . '@jisr-ksa.com']);
            }
        }

        // To reduce confusion... all emails go to Jisr for now.
        //if (Str::contains($report->company->center->domain_name, 'jasarah')) {
        //    $bccEmails = $report->emails()->where('type', 'bcc')->where('email', 'LIKE', '%ptc-ksa%')->get();
        //    foreach ($bccEmails as $bccEmail) {
        //        $bccEmail->update(['email' => Str::before($bccEmail->email, '@') . '@jasarah-ksa.com']);
        //    }
        //}

        return $report;
    }
}
