<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Back\CourseBatch;
use App\Models\Back\Course;
use App\Models\Back\TraineeGroup;
use App\Models\User;
use Illuminate\Console\Command;
use App\Notifications\TraineeAlertUpcomingSessionNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class TraineeAlertUpcomingSessionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etrianing:coursereminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify Users through email about the course start!';

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
        $targetDate = date('Y-m-d', strtotime("+1 day"));

        $courseBatches = CourseBatch::whereDate('starts_at', new Carbon($targetDate))->get();




        foreach($courseBatches as $batch) {

            $course = Course::where('id', $batch->course_id)->get();

            $traineeGroup = TraineeGroup::where('id', $batch->trainee_group_id)->with('trainees')->first();

            $users = User::whereIn('id', $traineeGroup->trainees->pluck('user_id'));

            $targetDate = $batch->starts_at;

            $users->chunk(20, function($users) use ($traineeGroup, $course, $targetDate) {
                Notification::send($users, new TraineeAlertUpcomingSessionNotification($traineeGroup, $course, $targetDate));
            });
        }
    }
}
