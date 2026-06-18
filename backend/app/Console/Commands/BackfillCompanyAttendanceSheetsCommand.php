<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Models\Back\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillCompanyAttendanceSheetsCommand extends Command
{
    protected $signature = 'company-attendance-sheets:backfill
                            {company_id : The company UUID}
                            {--from= : First report month (Y-m), defaults to month after earliest invoice}
                            {--to= : Last report month (Y-m), defaults to previous calendar month}
                            {--dry-run : Preview without creating reports}';

    protected $description = 'Backfill missed company attendance reports for a single company (draft/review only, never sent)';

    public function handle(AutomateCompanyAttendanceSheetsCommand $automator): int
    {
        $company = Company::find($this->argument('company_id'));

        if (! $company) {
            $this->error('Company not found.');

            return self::FAILURE;
        }

        if ($company->trainees()->count() === 0) {
            $this->error('Company has no trainees.');

            return self::FAILURE;
        }

        if (! $company->email) {
            $this->error('Company has no email. Add an email before backfilling.');

            return self::FAILURE;
        }

        $reportMonths = $this->resolveReportMonths($company);

        if ($reportMonths->isEmpty()) {
            $this->warn('No report months to backfill.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Backfilling %d report month(s) for %s (%s)',
            $reportMonths->count(),
            $company->name_ar,
            $company->id
        ));

        $created = 0;
        $skipped = 0;

        foreach ($reportMonths as $reportMonth) {
            $reportFrom = $reportMonth->copy()->startOfMonth();
            $reportTo = $reportMonth->copy()->day(min(30, $reportMonth->daysInMonth))->endOfDay();

            $invoiceMonth = $reportMonth->copy()->subMonth();
            $invoicesFrom = $invoiceMonth->copy()->startOfMonth()->toDateString();
            $invoicesTo = $invoiceMonth->copy()->endOfMonth()->toDateString();

            if ($this->reportExistsForPeriod($company, $reportFrom, $reportTo)) {
                $this->line("Skip {$reportMonth->format('Y-m')}: report already exists.");
                $skipped++;
                continue;
            }

            $invoiceCount = Invoice::where('company_id', $company->id)
                ->whereBetween('to_date', [$invoicesFrom, $invoicesTo])
                ->count();

            if ($invoiceCount === 0) {
                $this->line("Skip {$reportMonth->format('Y-m')}: no invoices for {$invoiceMonth->format('Y-m')}.");
                $skipped++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("Would create {$reportMonth->format('Y-m')} report ({$reportFrom->toDateString()} → {$reportTo->toDateString()}) using {$invoiceCount} invoice(s) from {$invoiceMonth->format('Y-m')}.");
                $created++;
                continue;
            }

            $lastReport = $company->company_attendance_reports()->orderBy('date_from', 'desc')->first();

            if ($lastReport) {
                $this->info("Creating {$reportMonth->format('Y-m')} from last report ({$invoiceCount} invoices from {$invoiceMonth->format('Y-m')}).");
                $automator->makeNewReportFromLastReportBasedOnInvoices(
                    $company,
                    $lastReport,
                    $reportFrom,
                    $reportTo,
                    $invoicesFrom,
                    $invoicesTo
                );
            } else {
                $this->info("Creating first report {$reportMonth->format('Y-m')} ({$invoiceCount} invoices from {$invoiceMonth->format('Y-m')}).");
                $automator->makeNewReportBasedOnInvoices(
                    $company,
                    $reportFrom,
                    $reportTo,
                    $invoicesFrom,
                    $invoicesTo
                );
            }

            $created++;
        }

        $this->newLine();
        $this->info($this->option('dry-run')
            ? "Dry run complete: {$created} would be created, {$skipped} skipped."
            : "Done: {$created} created, {$skipped} skipped. All reports left in review/draft (not approved or sent).");

        return self::SUCCESS;
    }

    private function resolveReportMonths(Company $company)
    {
        $earliestInvoiceMonth = Invoice::where('company_id', $company->id)
            ->orderBy('to_date')
            ->value('to_date');

        if (! $earliestInvoiceMonth) {
            return collect();
        }

        $from = $this->option('from')
            ? Carbon::parse($this->option('from').'-01')->startOfMonth()
            : Carbon::parse($earliestInvoiceMonth)->startOfMonth()->addMonth();

        $to = $this->option('to')
            ? Carbon::parse($this->option('to').'-01')->startOfMonth()
            : now()->subMonth()->startOfMonth();

        if ($from->gt($to)) {
            return collect();
        }

        $months = collect();
        $cursor = $from->copy();

        while ($cursor->lte($to)) {
            $months->push($cursor->copy());
            $cursor->addMonth();
        }

        return $months;
    }

    private function reportExistsForPeriod(Company $company, Carbon $reportFrom, Carbon $reportTo): bool
    {
        return $company->company_attendance_reports()
            ->whereDate('date_from', $reportFrom->toDateString())
            ->whereDate('date_to', $reportTo->toDateString())
            ->exists();
    }
}
