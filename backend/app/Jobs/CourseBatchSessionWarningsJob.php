<?php

namespace App\Jobs;

use App\Mail\WarningMail;
use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReportRecordWarning;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use App\Models\Back\MissedCourseNotice;
use App\Models\InboxMessage;
use App\Notifications\TraineeLateToClassNotification;
use App\Notifications\TraineeMissedClassNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mail;

class CourseBatchSessionWarningsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $report;

    public $courseBatchSession;

    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Back\AttendanceReport $report
     */
    public function __construct(AttendanceReport $report)
    {
        $this->report = $report;
        $this->courseBatchSession = $report->course_batch_session;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $attendances = $this->report->attendances;
        Log::debug('[CourseBatchSessionWarningsJob] For report ID: '.$this->report->id. ' with attendances: '.$attendances->count());
        foreach ($attendances as $attendance) {
            if (!$attendance->trainee) continue;

            if ($attendance->status === AttendanceReportRecord::STATUS_ABSENT) {
                Log::debug('[CourseBatchSessionWarningsJob] Sending absent notification: '.$attendance->id.' - User: '.$attendance->trainee->email);

                $alreadyIssued = AttendanceReportRecordWarning::where('team_id', $attendance->team_id)
                    ->where('attendance_report_id', $this->report->id)
                    ->where('attendance_report_record_id', $attendance->id)
                    ->where('trainee_id', $attendance->trainee_id)
                    ->exists();

                if ($alreadyIssued) {
                    continue;
                }

                $warning = new AttendanceReportRecordWarning();
                $warning->team_id = $attendance->team_id;
                $warning->attendance_report_id = $this->report->id;
                $warning->attendance_report_record_id = $attendance->id;
                $warning->trainee_id = $attendance->trainee_id;
                $warning->save();
                if ($attendance->trainee->warnings()->count() >= 3) {
                    Mail::to(['sara@ptc-ksa.com', 'trainee.affairs@ptc-ksa.com'])
                        ->queue(new WarningMail($attendance, auth()->user()->email));
                }
                try {
                    $attendance->trainee->notify(new TraineeMissedClassNotification($this->courseBatchSession));

                    $dayName = $this->courseBatchSession->starts_at->locale('ar')->getTranslatedDayName();
                    $dayDate = $this->courseBatchSession->starts_at->now()->toDateString();
                    if ($attendance->trainee_user_id) {
                        $message = new InboxMessage();
                        $message->team_id = $this->courseBatchSession->team_id;
                        $message->body = 'نظرا لتغيبكم اليوم '.$dayName.' الموافق لـ '.$dayDate.'وعدم حضوركم للبرنامج التدريبي وبناء عليه تم انذاركم.';
                        $message->to_id = $attendance->trainee_user_id;
                        $message->is_system_message = true;
                        $message->save();
                    }
                } catch (\Exception $exception) {
                    \Log::error('Error for trainee ID: '.$attendance->trainee->id.' - '.$exception->getMessage());
                }
            }

            if ($attendance->status === AttendanceReportRecord::STATUS_LATE_TO_CLASS) {
                Log::debug('[CourseBatchSessionWarningsJob] Sending present_late notification: '.$attendance->id.' - User: '.$attendance->trainee->email);

                $alreadySent = MissedCourseNotice::where('team_id', $attendance->team_id)
                    ->where('course_batch_session_id', $this->courseBatchSession->id)
                    ->where('trainee_id', $attendance->trainee_id)
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                try {
                    $attendance->trainee->notify(new TraineeLateToClassNotification($this->courseBatchSession));
                    MissedCourseNotice::create([
                        'team_id' => $attendance->team_id,
                        'course_batch_session_id' => $this->courseBatchSession->id,
                        'trainee_id' => $attendance->trainee_id,
                    ]);
                    $dayName = $this->courseBatchSession->starts_at->locale('ar')->getTranslatedDayName();
                    $dayDate = $this->courseBatchSession->starts_at->now()->toDateString();
                    if ($attendance->trainee_user_id) {
                        $message = new InboxMessage();
                        $message->team_id = $this->courseBatchSession->team_id;
                        $message->body = 'نظرا لتأخركم اليوم '.$dayName.' الموافق لـ '.$dayDate.' للبرنامج التدريبي وبناء عليه تم انذاركم.';
                        $message->to_id = $attendance->trainee_user_id;
                        $message->is_system_message = true;
                        $message->save();
                    }
                } catch (\Exception $exception) {
                    \Log::error('Error for trainee ID: '.$attendance->trainee->id.' - '.$exception->getMessage());
                }
            }
        }
        Log::debug('[CourseBatchSessionWarningsJob] Finished for report ID: '.$this->report->id);
    }
}
