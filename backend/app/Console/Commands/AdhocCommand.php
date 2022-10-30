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
     */
    public function handle()
    {
        DB::beginTransaction();
        $trainee = Trainee::withTrashed()->find('17b61580-709b-464b-b2b7-e5fea90516eb');
        $dates_marked_as_present = [
            Carbon::parse('2022-02-01'),
            Carbon::parse('2022-03-01')->endOfMonth(),
        ];
        $records = AttendanceReportRecord::where('trainee_id', $trainee->id)
            ->whereBetween('session_starts_at', $dates_marked_as_present)
            ->get();
        foreach ($records as $record) {
            $record->status = 3;
            $record->attended_at = $record->course_batch_session->starts_at->addMinute(rand(1,10));
            $record->created_at = $record->updated_at = $record->course_batch_session->starts_at;
            $record->save();
        }
        DB::commit();

        return 1;
    }
}
