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
        $from_date = Carbon::parse('2024-08-01')->startOfDay();
        $to_date = Carbon::parse('2024-08-31')->endOfDay();
        $this->createReportsBasedOnTraineedInvoiced($from_date, $to_date);
        return 1;
    }

    public function createReportsBasedOnTraineedInvoiced(Carbon $from_date, Carbon $to_date)
    {
        $count = Company::with('invoices')
            ->where('id', '09592d71-8b2e-4272-bdce-af904069c663')
            ->whereHas('invoices', function ($query) use ($from_date) {
                $query->whereBetween('to_date', ['2024-07-01', '2024-07-31']);
            })->count();
        $this->info('Found companies with invoices: '.$count);

        Company::with('invoices')
            ->where('id', '09592d71-8b2e-4272-bdce-af904069c663')
            ->whereHas('invoices', function ($query) use ($from_date) {
            $query->whereBetween('to_date', ['2024-07-01', '2024-07-31']);})->count();

        // Companies that don't have invoices in the past month, to skip.
         $companies_with_invoices = Company::with('invoices')
             ->where('id', '09592d71-8b2e-4272-bdce-af904069c663')
            ->whereHas('invoices', function ($query) use ($from_date) {
                $query->whereBetween('to_date',  ['2024-07-01', '2024-07-31']);
            })->pluck('id');
         $companies_without_invoices = Company::whereNotIn('id', $companies_with_invoices)->pluck('name_ar');
         foreach ($companies_without_invoices as $name_ar) {
             $this->info('No invoices for company: '.$name_ar);
         }

        Company::with('invoices')
            ->where('id', '09592d71-8b2e-4272-bdce-af904069c663')
            ->whereHas('invoices', function ($query) use ($from_date) {
                $query->whereBetween('to_date', ['2024-07-01', '2024-07-31']);
            })
            ->chunk(20, function($companies) use ($from_date, $to_date) {
                foreach ($companies as $company) {

                    //$companies_to_execlude = [
                    //    'dde0a82b-6197-4b60-a413-c5d208aa82f9',
                    //    '0386374a-3b80-4f0e-a903-5cf59e05b5ec',
                    //    'df1e6827-cbd7-448b-8d99-885b2a8ed539',
                    //    '171eaa0c-7ced-4acb-95d6-65c26eb73132',
                    //    'bb05eb3b-940c-402a-8be3-d40f2ff32af1',
                    //    'a6e393e6-5a23-45ff-902d-b572161fb932',
                    //    '2858f70d-0be2-4495-b78b-d451a44c3adb',
                    //    'cd44950e-1c80-47c4-853b-2c89bb2c6bc0',
                    //    '66ce8388-69da-433e-8422-3cd4ad8c4df4',
                    //    '436c9f1c-feb5-4732-bfb8-a4cee365beb2',
                    //    '211b16b2-7c2d-4413-9dda-0a98e5d917bc',
                    //    'c0169f69-9a0a-4b39-a2c4-1daeb4b18f32',
                    //    'de559e3d-0fd8-439a-9ddb-73e1637b00da',
                    //    'c8db9014-8e0e-4af1-a1b2-b7a638a635f9',
                    //    'd71ff6a1-d87c-4d4f-b714-ff5063f4729c',
                    //    '7b7a37a6-1029-48e5-9f85-4f6d0776d3fe',
                    //    '1f7466ba-6ad3-440c-839f-b9822fa0c4a4',
                    //    '09592d71-8b2e-4272-bdce-af904069c663',
                    //    'c8db9014-8e0e-4af1-a1b2-b7a638a635f9',
                    //    '04ce3c10-a599-439e-9d18-f67c36da02e8',
                    //    '59dff45f-13ca-46c0-8a50-4959f64a6eef',
                    //    'f8c4c81d-e869-408f-bc4c-37bb57091f60',
                    //    '30edafa9-aa26-4cca-89a5-8fd1a217556a',
                    //    'c8db9014-8e0e-4af1-a1b2-b7a638a635f9',
                    //    'c31feb4a-c2e0-4936-ae86-09e13b591699',
                    //    '36245350-5e98-484d-8c5d-853550beebdf',
                    //    'd4dad767-b1c9-445d-bf1b-0597c3302a28',
                    //    '3601d5b5-ebca-4c26-94f4-71b73395b2fb',
                    //];

                    //if (in_array($company->id, $companies_to_execlude, true)) {
                    //    $this->info('Excluded company: '.$company->name_ar);
                    //    continue;
                    //}

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
                        $this->makeNewReportFromLastReportBasedOnInvoices($company, $lastReport, '2024-08-01', '2024-08-31', '2024-07-01', '2024-07-31');
                    } else {
                        if (! $company->email) {
                            $this->info('No email for company. Skipping: '.$company->name_ar);
                            continue;
                        }
                        $this->info('No last report. Creating new report - '.$company->name_ar . ',' . $company->trainees()->count());
                        $this->makeNewReportBasedOnInvoices($company, '2024-08-01', '2024-08-31', '2024-07-01', '2024-07-31');
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
        $clone->emails()->createMany($emails);

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

        //app()->make(CompanyAttendanceReportService::class)->approve($clone->id);
    }

    public function makeNewReportBasedOnInvoices($company, $dateFrom, $dateTo, $invoicesDateFrom, $invoicesDateTo)
    {
        $report = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
        $report->date_from = Carbon::parse($dateFrom)->setTimezone('Asia/Riyadh')->startOfDay();
        $report->date_to = Carbon::parse($dateTo)->setTimezone('Asia/Riyadh')->endOfDay();
        $report->save();

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

        //app()->make(CompanyAttendanceReportService::class)->approve($report->id);
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
