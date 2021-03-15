<?php

namespace App\Console\Commands;

use App\Models\Back\CourseBatchSession;
use App\Models\Back\TraineeAbsent;
use App\Services\AttendanceService;
use Illuminate\Console\Command;

class ProcessCourseAbsentWarningsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:course-absent-warnings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save course absent warnings';

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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        $sessions = CourseBatchSession::where('processed_absentees', false)->get();

        foreach ($sessions as $session) {
            \DB::beginTransaction();
            if (now()->isAfter($session->ends_at)) {
                $didntAttend = app()
                    ->make(AttendanceService::class)
                    ->getMissingTraineesForCourseBatchSession($session);

                foreach ($didntAttend as $trainee) {
                    $absent = new TraineeAbsent();
                    $absent->team_id = $trainee->team_id;
                    $absent->trainee_id = $trainee->id;
                    $absent->course_id = $session->course_id;
                    $absent->course_batch_id = $session->course_batch_id;
                    $absent->course_batch_session_id = $session->id;
                    $trainee->absentes()->save($absent);
                }
            }

            $session->processed_absentees = true;
            $session->save();
            \DB::commit();
        }

        return 1;
    }
}
