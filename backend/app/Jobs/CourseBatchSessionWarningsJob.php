<?php

namespace App\Jobs;

use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReportRecordWarning;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use App\Models\Back\MissedCourseNotice;
use App\Models\InboxMessage;
use App\Models\User;
use App\Notifications\ManageMissedClassNotification;
use App\Notifications\TraineeLateToClassNotification;
use App\Notifications\TraineeMissedClassNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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

                // Skip warning if trainee was created less than or equal to 2 days before the session
                $sessionDate = $this->courseBatchSession->starts_at;
                $traineeCreatedDate = $attendance->trainee->created_at;
                $daysDifference = $traineeCreatedDate->diffInDays($sessionDate);
                
                if ($daysDifference <= 2) {
                    Log::debug('[CourseBatchSessionWarningsJob] Skipping warning - trainee created '.$daysDifference.' days before session: '.$attendance->trainee->email);
                    continue;
                }

                // Skip warning if trainee is currently on leave
                $sessionDateOnly = $this->courseBatchSession->starts_at->toDateString();
                $hasActiveLeave = $attendance->trainee->leaves()
                    ->where('status', '!=', 'cancelled')
                    ->where('from_date', '<=', $sessionDateOnly)
                    ->where('to_date', '>=', $sessionDateOnly)
                    ->exists();

                if ($hasActiveLeave) {
                    Log::debug('[CourseBatchSessionWarningsJob] Skipping warning - trainee is on leave: '.$attendance->trainee->email);
                    continue;
                }

                $warning = new AttendanceReportRecordWarning();
                $warning->team_id = $attendance->team_id;
                $warning->attendance_report_id = $this->report->id;
                $warning->attendance_report_record_id = $attendance->id;
                $warning->trainee_id = $attendance->trainee_id;
                $warning->save();

                // TODO: Move this to another service class
                $startDate = Carbon::createFromFormat('Y-m-d', '2023-06-01')->startOfDay();
                $endDate = now();
                if ($attendance->trainee->warnings()->whereBetween('created_at', [$startDate, $endDate])->count() >= 3) {
                    //User::where('email', 'trainee.affairs@ptc-ksa.net')
                    //    ->first()
                    //    ->notify(new ManageMissedClassNotification($attendance->trainee));

//                    User::permission('manage-missed-course-notices')
//                        ->whereBetween('created_at', [$startDate, $endDate])
//                        ->get()
//                        ->each(function($user) use ($attendance) {
//                            $user->notify(new ManageMissedClassNotification($attendance->trainee));
//                        });
                }

                try {
                    $attendance->trainee->notify(new TraineeMissedClassNotification($this->courseBatchSession));

                    $dayName = $this->courseBatchSession->starts_at->locale('ar')->getTranslatedDayName();
                    $dayDate = $this->courseBatchSession->starts_at->toDateString();
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

                // Skip warning if trainee was created less than or equal to 2 days before the session
                $sessionDate = $this->courseBatchSession->starts_at;
                $traineeCreatedDate = $attendance->trainee->created_at;
                $daysDifference = $traineeCreatedDate->diffInDays($sessionDate);
                
                if ($daysDifference <= 2) {
                    Log::debug('[CourseBatchSessionWarningsJob] Skipping late warning - trainee created '.$daysDifference.' days before session: '.$attendance->trainee->email);
                    continue;
                }

                // Skip warning if trainee is currently on leave
                $sessionDateOnly = $this->courseBatchSession->starts_at->toDateString();
                $hasActiveLeave = $attendance->trainee->leaves()
                    ->where('status', '!=', 'cancelled')
                    ->where('from_date', '<=', $sessionDateOnly)
                    ->where('to_date', '>=', $sessionDateOnly)
                    ->exists();

                if ($hasActiveLeave) {
                    Log::debug('[CourseBatchSessionWarningsJob] Skipping late warning - trainee is on leave: '.$attendance->trainee->email);
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
