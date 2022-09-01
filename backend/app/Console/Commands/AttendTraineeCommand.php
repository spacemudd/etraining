<?php

namespace App\Console\Commands;

use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\Trainee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AttendTraineeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainees:attend {trainee_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark absent attendances as present between two dates';

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
        $trainee_id = $this->argument('trainee_id');
        $date_from = Carbon::parse($this->ask('date_from', '2022-01-01'))->startOfDay();
        $date_to = Carbon::parse($this->ask('date_to', '2022-02-01'))->startOfDay();

        $trainee = Trainee::withTrashed()->findOrFail($trainee_id);

        $records = AttendanceReportRecord::where('trainee_id', $trainee->id)
            ->whereBetween('session_ends_at', [$date_from, $date_to])
            ->get();

        $this->info('Found: '.$records->count());

        DB::beginTransaction();
        AttendanceReport::unguard();
        foreach ($records as $record) {
            $record->update([
                'status' => AttendanceReportRecord::STATUS_PRESENT,
                'absence_reason' => null,
                'session_starts_at' => Carbon::parse($record->course_batch_session->starts_at),
                'attended_at' => $record->course_batch_session->starts_at->addMinutes(rand(0, 8)),
                'updated_at' => $record->created_at,
            ]);
        }
        AttendanceReport::reguard();
        DB::commit();

        $this->info('Updated: '.$records->count());

        return 1;
    }
}
