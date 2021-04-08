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
        Log::debug('[CourseBatchSessionWarningsJob] For course batch session ID: '.$this->courseBatchSession->id);
        foreach ($attendances as $attendance) {
            if ($attendance->attendance_status === CourseBatchSessionAttendance::STATUS_ABSENT) {
                Log::debug('[CourseBatchSessionWarningsJob] Sending absent notification: '.$attendance->id.' - User: '.$attendance->trainee->email);
                $attendance->trainee->notify(new TraineeMissedClassNotification($this->courseBatchSession));
            }

            if ($attendance->attendance_status === CourseBatchSessionAttendance::STATUS_PRESENT_LATE_TO_COURSE) {
                Log::debug('[CourseBatchSessionWarningsJob] Sending present_late notification: '.$attendance->id.' - User: '.$attendance->trainee->email);
                $attendance->trainee->notify(new TraineeLateToClassNotification($this->courseBatchSession));
            }
        }
        Log::debug('[CourseBatchSessionWarningsJob] Finished for course batch session ID: '.$this->courseBatchSession->id);
    }
}
