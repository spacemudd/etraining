<?php

namespace App\Jobs;

use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReportRecordWarning;
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

                $warning = new AttendanceReportRecordWarning();
                $warning->team_id = $attendance->team_id;
                $warning->attendance_report_id = $this->report->id;
                $warning->attendance_report_record_id = $attendance->id;
                $warning->trainee_id = $attendance->trainee_id;
                $warning->save();

                try {
                    $attendance->trainee->notify(new TraineeMissedClassNotification($this->courseBatchSession));
                } catch (\Exception $exception) {
                    \Log::error('Error for trainee ID: '.$attendance->trainee->id.' - '.$exception->getMessage());
                }
            }

            if ($attendance->status === AttendanceReportRecord::STATUS_LATE_TO_CLASS) {
                Log::debug('[CourseBatchSessionWarningsJob] Sending present_late notification: '.$attendance->id.' - User: '.$attendance->trainee->email);
                try {
                    $attendance->trainee->notify(new TraineeLateToClassNotification($this->courseBatchSession));
                } catch (\Exception $exception) {
                    \Log::error('Error for trainee ID: '.$attendance->trainee->id.' - '.$exception->getMessage());
                }
            }
        }
        Log::debug('[CourseBatchSessionWarningsJob] Finished for report ID: '.$this->report->id);
    }
}
