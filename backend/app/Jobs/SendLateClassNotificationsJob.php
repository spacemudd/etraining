<?php

namespace App\Jobs;

use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use App\Notifications\TraineeLateToClassNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SendLateClassNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $courseBatchSession;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Back\CourseBatchSession $session
     */
    public function __construct(CourseBatchSession $session)
    {
        $this->courseBatchSession = $session;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $users = CourseBatchSession::with('course')
            ->with(['attendances' => function($q) {
                $q->with(['trainee' => function($q) {
                    $q->with('company');
                }]);
            }])
            ->with(['course_batch' => function($q) {
                $q->with(['trainee_group' => function($q) {
                    $q->with(['trainees' => function($q) {
                        $q->withTrashed()->with('company');
                    }]);
                }]);
            }])->findOrFail($this->courseBatchSession->id);

        $usersWhoDidntAttended = [];

        foreach ($users->course_batch->trainee_group->trainees as $trainee) {
            $hasAttended = CourseBatchSessionAttendance::where('course_batch_session_id', $this->courseBatchSession->id)
                ->where('trainee_id', $trainee->id)
                ->first();
            if (!$hasAttended) {
                $usersWhoDidntAttended[] = $trainee;
            }
        }
        
        Log::debug('Beginning to send late notifications to trainees ('.count($usersWhoDidntAttended).')');
        dd(count($usersWhoDidntAttended));
        foreach ($usersWhoDidntAttended as $punchIn) {
            Log::debug('Sending warning to user: '.$punchIn->email);

            if (Str::startsWith($punchIn->phone, '05')) {
                $punchIn->phone = Str::replaceFirst('05', '9665', $punchIn->phone);
                $punchIn->save();

                if ($punchIn->user) {
                    $user = $punchIn->user;
                    $user->phone = $punchIn->phone;
                    $user->save();
                }
            }

            try {
                $punchIn->notify(new TraineeLateToClassNotification($this->courseBatchSession));
            } catch (\Exception $er) {
                Log::error('Couldnt send to: '.$punchIn->email);
                app('sentry')->captureException($er);
            }
        }
        Log::debug('Finished sending late notifications to trainees');
    }
}
