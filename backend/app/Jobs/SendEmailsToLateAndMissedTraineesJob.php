<?php

namespace App\Jobs;

use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use App\Notifications\TraineeLateToClassNotification;
use App\Notifications\TraineeMissedClassNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SendEmailsToLateAndMissedTraineesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $courseBatchSession;

    public $timeout = 3600;

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

        $usersWhoWhereLate = CourseBatchSessionAttendance::where('course_batch_session_id', $this->courseBatchSession->id)
            ->where('attended', false)
            ->get();

        Log::info(collect($usersWhoDidntAttended)->pluck('email'));
        Log::info(collect($usersWhoWhereLate)->pluck('trainee.email'));

        Log::debug('Beginning to send late notifications to trainees ('.count($usersWhoDidntAttended).')');
        Log::debug('Beginning to send late notifications to trainees ('.$usersWhoWhereLate->count().')');

        foreach ($usersWhoDidntAttended as $punchIn) {
            //if (in_array($punchIn->email, $alreadySent)) {
            //    continue;
            //}

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

            if (Str::startsWith($punchIn->phone, '5')) {
                $punchIn->phone = Str::replaceFirst('5', '9665', $punchIn->phone);
                $punchIn->save();

                if ($punchIn->user) {
                    $user = $punchIn->user;
                    $user->phone = $punchIn->phone;
                    $user->save();
                }
            }

            try {
                $punchIn->notify(new TraineeMissedClassNotification($this->courseBatchSession));
            } catch (\Exception $er) {
                Log::error("Couldn't send to: ".$punchIn->email);
                app('sentry')->captureException($er);
            }
        }

        foreach ($usersWhoWhereLate as $punchInAttendance) {
            $punchIn = $punchInAttendance->trainee;
            //if (in_array($punchIn->email, $alreadySent)) {
            //    continue;
            //}
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
                Log::error("Couldn't send to: ".$punchIn->email);
                app('sentry')->captureException($er);
            }
        }

        Log::debug('Finished sending late notifications to trainees');
    }
}
