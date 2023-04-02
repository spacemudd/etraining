<?php

namespace App\Console\Commands;

use App\Mail\CourseDelayedEmail;
use App\Mail\CourseDelayedMail;
use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDelayMessageToSpecificInstructorsTrainees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:send-delay {--instructor_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends an email to instructor trainees';

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
        if ($instructor_id = $this->option('instructor_id')) {
            Trainee::where('instructor_id', $instructor_id)
                ->chunk(300, function($trainees) {
                    $this->info('Sending to '.$trainees->count());
                    $emails = $trainees->pluck('email');
                    $emails[] = 'shafiqalshaar@gmail.com';
                    $emails[] = 'leena@ptc-ksa.net';
                    Mail::bcc($emails)->locale('ar')
                        ->queue(new CourseDelayedMail());
                });
        }

        $this->info('Done!');

        return 1;
    }
}
