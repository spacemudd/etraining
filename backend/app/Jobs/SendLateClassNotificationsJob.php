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

        $alreadySent = ["noony7771@gmail.com","Jojo.f505@hotmail.com","mnala8706@gmail.com","Abonassertk@gmail.com","rahaf5ahmed@gmail.com","wejdan.tulq@gmail.com","zaf06307@gmail.com","h1413ai@gmail.com","Lauly11@hotmail.com","Abeerth73@gmail.com","n.7oppy@gmail.com","wadad857@gmail.com","sara9alenezi@gmail.com","hand1100@icloud.com","Mm-_-94@hotmail.com","ats1403h@gmail.com","meesh22880@gmail.com","Njoola14161234@gmail.com","mashaeil1678@gmail.com","abeeralaenazi@gmail.com","ziadanzi2002@gmail.com","muntt1985@gmail.com","Fatimah77_@hotmail.com","hajrfran@gmail.com","najdn331@gmail.com","Norah1418nnaa@gmail.com","raghadmq15@gmail.com","fara0555477517@gmail.com","hya.supie2030@gmail.com","slmaiald@gmail.com","ashwwag.n@gmail.com","nouft53@gmail.com","Mznah.sh@gmail.com","alanazisha21@gmail.com","dal0029@outlook.sa","amalalenazi2@gmail.com","Sarah4433a@gmail.com","Wala-a-2018@hotmail.com","alanoudsaad101@gmail.com","Re2015a@hotmail.com","Shaikhah42@hotmail.com","rahafalqahtani112@gmail.com","ooxp@hotmail.com","Zahraduraie@gmail.com","reemx8x@icloud.com","youssef@icloud.com","asom88almutairi@gmail.com","hsah195555@gmail.com","Batoooly@live.com","may3599@icloud.com","bofaten1@gmail.com","abdullah3314@hotmail.com","Raamaa236@gmail.com","a.alwthere0@gmail.com","noody_2013f@hotmail.com","hasuhanan@gmail.com","aaml35609@gmail.com","m.s2444@hotmail.com","jalnzy992@gmail.com","Raneemgh24@outlook.sa","ryoooof510@gmail.com","rawan-alenzi@hotmail.com","shoooos550@hotmail.com","norah6089@icloud.com","taiiif.90@gmail.com","asma.116699@gmail.com","rehamalbarak1420@gmail.com","aashwaq147@gmail.com","m.s8617@hotmail.com","Lamosh.22@icloud.com","mahazed1444@gmail.com","Om.fr789@gmail.com","alenazy1985@icloud.com","Mem773161@amail.com","man3sh@hotmail.com","Tole.en13@hotmail.com","hnna5733@gmai.com","nourah.ahmmed@gmail.com","Ahad7747@gmail.com","jihan188881@gmail.com","shomwokh199@gmail.com","Norahbadar1@gmail.com","Adamaltaweel@gmail.com","Manalmoh1422@gmail.com","Soosoo-mh@hotmail.com","soso0.8@hotmail.com","fatmaa63@icloud.com","nat81416@gmail.com","Sarahalshmry55@gmail.com","milan.aleid.2001s@gmail.com","aasszz1110@hotmail.com","mremalnazy176@gmail.com","ahoooos550@hotmail.com","hayabndar0@gmail.com","besh12124@yahoo.com","rawanalanzi9999@gmail.com","alia2.shb@gmail.com","ilovezoba@gmail.com","Aisha1989129@gmail.com","noo_d@icloud.com","AYOOSA.ABDULLAH@ICLOUD.COM"];

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
