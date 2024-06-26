<?php

namespace App\Jobs;

use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReport;
use App\Models\Back\CourseBatchSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MakeAttendanceReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $course_batch_session_id;

    /**
     * @var string AttendanceReport
     */
    public $report_id;

    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @param string $course_batch_session_id
     * @param $report_id
     */
    public function __construct(string $course_batch_session_id, $report_id)
    {
        $this->course_batch_session_id = $course_batch_session_id;
        $this->report_id = $report_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $report = AttendanceReport::find($this->report_id);

        $session = CourseBatchSession::with('course')
            ->with(['attendances' => function($q) {
                $q->with(['trainee' => function($q) {
                    $q->with('company');
                }]);
            }])
            ->with(['course_batch' => function($q) {
                $q->with(['trainee_group' => function($q) {
                    $q->with(['trainees' => function($q) {
                        $q->with('company');
                    }]);
                }]);
            }])->findOrFail($this->course_batch_session_id);

        $usersWhoDidntAttended = [];

        foreach ($session->course_batch->trainee_group->trainees as $trainee) {

            $hasAttended = AttendanceReportRecord::where('course_batch_session_id', $session->id)
                ->where('trainee_id', $trainee->id)
                ->first();

            if (!$hasAttended) {
                $usersWhoDidntAttended[] = $trainee;
            }
        }

        foreach ($usersWhoDidntAttended as $trainee) {
            AttendanceReportRecord::create([
                'attendance_report_id' => $this->report_id,
                'course_id' => $session->course_id,
                'course_batch_id' => $session->course_batch_id,
                'course_batch_session_id' => $session->id,
                'instructor_id' => $session->course->instructor_id,
                'trainee_id' => $trainee->id,
                'trainee_user_id' => optional($trainee->user)->id,
                'session_starts_at' => $session->starts_at,
                'session_ends_at' => $session->ends_at,
                'attended_at' => null,
                'status' => AttendanceReportRecord::STATUS_ABSENT,
                'last_login_at' => optional($trainee->user)->last_login_at,
                'team_id' => $report->team_id,
            ]);
        }

        $report->is_ready_for_review = true;
        $report->save();
    }
}
