<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\Invoice;
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
    protected $description = 'Generate company attendance reports based on invoices.';

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
        $from_date = Carbon::parse('2024-09-01')->startOfDay();
        $to_date = Carbon::parse('2024-09-30')->endOfDay();
        $this->createReportsBasedOnTraineedInvoiced($from_date, $to_date);
        return 1;
    }

    public function createReportsBasedOnTraineedInvoiced(Carbon $from_date, Carbon $to_date)
    {
        $select_invoices_from = ['2024-08-01', '2024-08-31'];
        $companies_with_invoices = Company::with('invoices')
            ->whereHas('invoices', function ($query) use ($select_invoices_from) {
                $query->whereBetween('to_date', $select_invoices_from);
            })
            ->pluck('id');

        $companies_without_invoices = Company::whereNotIn('id', $companies_with_invoices)->pluck('name_ar');
        foreach ($companies_without_invoices as $name_ar) {
            $this->info('No invoices for company: ' . $name_ar);
        }

        Company::with('invoices')
            ->whereHas('invoices', function ($query) use ($select_invoices_from) {
                $query->whereBetween('to_date', $select_invoices_from);
            })
            ->chunk(20, function ($companies) use ($from_date, $to_date, $select_invoices_from) {
                foreach ($companies as $company) {
                    if ($company->trainees()->count() === 0) {
                        $this->info('No trainees. Skipping: ' . $company->name_ar);
                        continue;
                    }

                    $lastReport = $company->company_attendance_reports()->orderBy('date_from', 'desc')->first();
                    if ($lastReport) {
                        $this->info('New report from last report: ' . $company->name_ar . ', ' . $company->trainees()->count());
                        $this->makeNewReportFromLastReportBasedOnInvoices($company, $lastReport, $from_date, $to_date, $select_invoices_from[0], $select_invoices_from[1]);
                    } else {
                        if (!$company->email) {
                            $this->info('No email for company. Skipping: ' . $company->name_ar);
                            continue;
                        }
                        $this->info('No last report. Creating new report - ' . $company->name_ar . ', ' . $company->trainees()->count());
                        $this->makeNewReportBasedOnInvoices($company, $from_date, $to_date, $select_invoices_from[0], $select_invoices_from[1]);
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

        $this->attachEmails($clone, $company);
        $trainee_ids = Invoice::where('company_id', $company->id)
            ->whereBetween('to_date', [$invoicesDateFrom, $invoicesDateTo])
            ->pluck('trainee_id');

        $clone->trainees()->sync($trainee_ids);
        $this->detachResignedTrainees($clone);
    }

    public function makeNewReportBasedOnInvoices($company, $dateFrom, $dateTo, $invoicesDateFrom, $invoicesDateTo)
    {
        $report = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
        $report->date_from = Carbon::parse($dateFrom)->setTimezone('Asia/Riyadh')->startOfDay();
        $report->date_to = Carbon::parse($dateTo)->setTimezone('Asia/Riyadh')->endOfDay();
        $report->save();

        $this->attachEmails($report, $company);
        $trainee_ids = Invoice::where('company_id', $company->id)
            ->whereBetween('to_date', [$invoicesDateFrom, $invoicesDateTo])
            ->pluck('trainee_id');

        $report->trainees()->sync($trainee_ids);
        $this->detachResignedTrainees($report);
    }

    private function attachEmails($report, $company)
    {
        $emails = [
            ['type' => 'bcc', 'email' => 'sara@ptc-ksa.net'],
            ['type' => 'bcc', 'email' => 'm_shehatah@ptc-ksa.net'],
            ['type' => 'bcc', 'email' => 'ceo@ptc-ksa.net'],
            ['type' => 'bcc', 'email' => 'mashael.a@ptc-ksa.net'],
        ];

        if ($company->salesperson_email) {
            $repEmails = explode(', ', $company->salesperson_email);
            foreach ($repEmails as $repEmail) {
                $emails[] = ['type' => 'bcc', 'email' => $repEmail];
            }
        }
        $report->emails()->createMany($emails);

        $company_emails = explode(', ', $company->email);
        foreach ($company_emails as $email) {
            if ($report->emails()->where('email', $email)->count() === 0) {
                $report->emails()->create(['type' => 'to', 'email' => $email]);
            }
        }

        $this->updateReportEmailsPerToCenter($report);
    }

    private function detachResignedTrainees($report)
    {
        foreach ($report->trainees as $trainee) {
            if ($trainee->resignations()->whereBetween('created_at', [$report->date_from, $report->date_to])->count()) {
                $report->trainees()->detach($trainee->id);
                $this->info('Trainee has resignations. Removed: ' . $trainee->name);
            }
        }
    }

    public function updateReportEmailsPerToCenter(CompanyAttendanceReport $report)
    {
        if (Str::contains($report->company->center->domain_name, 'jisr') || Str::contains($report->company->center->domain_name, 'jasarah')) {
            $bccEmails = $report->emails()->where('type', 'bcc')->where('email', 'LIKE', '%ptc-ksa%')->get();
            foreach ($bccEmails as $bccEmail) {
                $bccEmail->update(['email' => Str::before($bccEmail->email, '@') . '@jisr-ksa.com']);
            }
        }

        return $report;
    }
}
