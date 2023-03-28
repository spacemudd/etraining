<?php

namespace App\Console\Commands;

use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AdhocCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:adhoc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For random adhoc execution of commands';

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
     * @throws \Throwable
     */
    public function handle()
    {
        // $msg = \Msegat::sendMessage('966565176235', 'Hello there test from Msegat');

        foreach (Company::get() as $company) {
            $report = $company->company_attendance_reports()->first();
            if (optional($report)->to_emails) {
                $company->email = explode(', ', $report->to_emails)[0];
                $company->save();
            }
        }


        return 1;
    }
}
