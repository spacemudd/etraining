<?php

namespace App\Console\Commands;

use App\Models\Back\CourseBatchSession;
use App\Models\Back\Trainee;
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
        $sessions = CourseBatchSession::whereBetween('starts_at', [
            now()->startOfDay(),
            now()->addDay()->endOfDay()
        ])->get();

        foreach($sessions as $session) {
            Trainee::where('trainee_group_id', $session->course_batch->trainee_group_id)->chunk(20, function($trainees) use ($session) {
                Notification::send($trainees, new TraineeAlertUpcomingSessionNotification($session));
            });
        }
    }
}
