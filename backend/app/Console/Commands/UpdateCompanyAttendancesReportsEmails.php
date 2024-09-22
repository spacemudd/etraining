<?php

namespace App\Console\Commands;

use App\Models\Back\CompanyAttendanceReport;
use Illuminate\Console\Command;

class UpdateCompanyAttendancesReportsEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update-company-attendances-reports-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Moves saving emails from inside the table to different tables';

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
        CompanyAttendanceReport::chunk(100, function($reports) {
            foreach ($reports as $report) {
                $this->info('Update for company ID: '.$report->company_id);
                $to_emails = explode(',', $report->to_emails);
                $cc_emails = explode(',', $report->cc_emails);
                foreach ($to_emails as $to_email) {
                    $report->emails()->create(['type' => 'to', 'email' => $to_email]);
                }
                foreach ($cc_emails as $cc_email) {
                    $report->emails()->create(['type' => 'cc', 'email' => $cc_email]);
                }
            }
        });
        return 1;
    }
}
