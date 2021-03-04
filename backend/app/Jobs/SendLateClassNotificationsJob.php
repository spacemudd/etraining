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

        //return 1;

        $alreadySent = [
            "noony7771@gmail.com",
            "Jojo.f505@hotmail.com",
            "mnala8706@gmail.com",
            "Abonassertk@gmail.com",
            "Lauly11@hotmail.com",
            "Abeerth73@gmail.com",
            "n.7oppy@gmail.com",
            "n.7oppy@gmail.com",
            "wadad857@gmail.com",
            "sara9alenezi@gmail.com",
            "hand1100@icloud.com",
            "Realmu99@gmail.com",
            "Mm-_-94@hotmail.com",
            "ats1403h@gmail.com",
            "meesh22880@gmail.com",
            "Njoola14161234@gmail.com",
            "mashaeil1678@gmail.com",
            "abeeralaenazi@gmail.com",
            "F20036@icloud.com",
            "agla.glaa@windowslive.com",
            "ziadanzi2002@gmail.com",
            "muntt1985@gmail.com",
            "Fatimah77_@hotmail.com",
            "Fatima.Mohammad.sh@hotmail.com",
            "hajrfran@gmail.com",
            "najdn331@gmail.com",
            "Norah1418nnaa@gmail.com",
            "memebaqir1418@icloud.com",
            "adlal8311@gmail.com",
            "raghadmq15@gmail.com",
            "hya.supie2030@gmail.com",
            "ashwwag.n@gmail.com",
            "alanazisha21@gmail.com",
            "eill35az@gmail.com",
        ];

        foreach ($usersWhoDidntAttended as $punchIn) {
            if (in_array($punchIn->email, $alreadySent)) {
                continue;
            }


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

        foreach ($usersWhoWhereLate as $punchInAttendance) {
            $punchIn = $punchInAttendance->trainee;
            if (in_array($punchIn->email, $alreadySent)) {
                continue;
            }
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
