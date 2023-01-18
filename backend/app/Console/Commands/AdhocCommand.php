<?php

namespace App\Console\Commands;

use App\Models\Back\AttendanceReportRecord;
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
        DB::beginTransaction();
        $fromTrainee = Trainee::withTrashed()->find('2b3651cb-4563-4c54-8142-23b69c936770');

        $records = AttendanceReportRecord::where('trainee_id', $fromTrainee->id)
            ->whereBetween('session_starts_at', ['2022-10-01', '2022-10-31'])
            ->get();

        $this->info('Found: '.$records->count());

        foreach ($records as $record) {
            $newRecord = $record->replicate();
            $newRecord->trainee_id = 'b3618191-a8c0-4310-9379-a945397285d5';
            $newRecord->session_starts_at = $record->course_batch_session->starts_at;
            $newRecord->status = AttendanceReportRecord::STATUS_PRESENT;
            $newRecord->attended_at = $record->course_batch_session->starts_at->addMinute(random_int(1,10));
            $newRecord->save(['timestamps' => false]);
        }
        DB::commit();

        return 1;
    }
}
