<?php

namespace App\Console\Commands;

use App\Models\Back\CompanyAttendanceReportsTrainee;
use DB;
use Illuminate\Console\Command;

class ReplicateTraineeInInCompanyAttendanceReportCommand extends Command
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
        $data = ['company_attendance_report_id' => '8039', 'trainee_id' => 'f8cc1153-a2d3-4ee4-985f-6db3fc155a12'];
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
