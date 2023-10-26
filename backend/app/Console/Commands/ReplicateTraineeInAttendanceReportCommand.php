<?php

namespace App\Console\Commands;

use App\Models\Back\CompanyAttendanceReportsTrainee;
use DB;
use Illuminate\Console\Command;

class ReplicateTraineeInAttendanceReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replicate:trainee';

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
        $data = ['company_attendance_report_id' => '7086', 'trainee_id' => 'f2e6a762-df1a-4514-a8a5-b21f604dada9'];
//        $data = ['company_attendance_report_id' => '1', 'trainee_id' => '08744259-f3ce-46fb-a159-d09ff79ffdac'];

        DB::beginTransaction();
        $records = CompanyAttendanceReportsTrainee::where($data)->get();
        foreach ($records as $record) {
            $x = $record->replicate();
            $x ->save();
        }
        DB::commit();
        return 0;
    }
}
