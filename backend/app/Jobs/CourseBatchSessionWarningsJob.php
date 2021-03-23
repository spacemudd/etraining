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

class CourseBatchSessionWarningsJob implements ShouldQueue
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
        $attendances = $this->courseBatchSession->attendances;
        foreach ($attendances as $attendance) {
            if ($attendance->status === CourseBatchSessionAttendance::STATUS_ABSENT && !$attendance->attended) {
                Log::info('Sending to: '.$attendance->trainee->id);
                $attendance->trainee->notify(new TraineeMissedClassNotification($this->courseBatchSession));
            }
        }
        Log::debug('Finished sending late notifications to trainees');
    }
}
