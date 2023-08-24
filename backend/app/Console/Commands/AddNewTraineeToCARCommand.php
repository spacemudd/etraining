<?php

namespace App\Console\Commands;

use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\CompanyAttendanceReportsTrainee;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddNewTraineeToCARCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainee:car';

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
        DB::beginTransaction();
        $data = ['company_attendance_report_id' => 5727, 'trainee_id' => 'f2e6a762-df1a-4514-a8a5-b21f604dada9'];
        $records = CompanyAttendanceReportsTrainee::where($data)->get();

        foreach ($records as $record) {
            $newRecord = $record->replicate();
            $newRecord->save();
        }
        DB::commit();

        return 1;

    }
}
