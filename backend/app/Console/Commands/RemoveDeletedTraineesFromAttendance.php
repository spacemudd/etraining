<?php

namespace App\Console\Commands;

use App\Models\Back\AttendanceReportRecord;
use Illuminate\Console\Command;

class RemoveDeletedTraineesFromAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:remove-attendances';

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
        \DB::beginTransaction();
        $bar = $this->output->createProgressBar(AttendanceReportRecord::count());
        $bar->start();
        AttendanceReportRecord::with(['trainee' => function($q) {
            $q->withTrashed();
        }])->chunk(500, function($records) use (&$bar) {
            foreach ($records as $record) {
                $bar->advance(1);

                if (!$record->trainee->deleted_at) continue;

                if ($record->trainee->deleted_at->isBefore($record->course_batch_session->starts_at)) {
                    $record->forceDelete();
                }
            }
        });
        \DB::commit();
        return 1;
    }
}
