<?php

namespace App\Console\Commands;

use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use Illuminate\Console\Command;

class MigrateAttendances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:migrate-attendances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $attendances = CourseBatchSessionAttendance::count();

        $bar = $this->output->createProgressBar($attendances);
        $bar->start();

        \DB::beginTransaction();

        CourseBatchSession::with(['attendances', 'course', 'trainee'])->chunk(10, function($sessions) use (&$bar) {
            foreach ($sessions as $session) {
                AttendanceReport::unguard();
                $report = AttendanceReport::firstOrCreate([
                    'course_batch_session_id' => $session->id,
                ], [
                    'team_id' => $session->team_id,
                    'course_batch_session_id' => $session->id,
                    'is_ready_for_review' => true,
                    'status' => $session->sent_warnings_at ? AttendanceReport::STATUS_SUBMITTED_REPORT : AttendanceReport::STATUS_DRAFT_REPORT,
                ]);

                foreach ($session->attendances->chunk(100) as $oldAttendances) {
                    foreach ($oldAttendances as $oldAttendance) {
                        if (!optional($session->course)->instructor_id) continue;

                        if ($oldAttendance->trainee->created_at->isAfter($session->starts_at)) continue;

                        if ($oldAttendance->trainee->deleted_at) {
                            if ($oldAttendance->trainee->deleted_at->isBefore($session->starts_at)) continue;
                        }

                        $newAttendance = new AttendanceReportRecord();
                        $newAttendance->team_id = $oldAttendance->team_id;
                        $newAttendance->attendance_report_id = $report->id;
                        $newAttendance->course_id = $oldAttendance->course_id;
                        $newAttendance->course_batch_id = $oldAttendance->course_batch_id;
                        $newAttendance->course_batch_session_id = $session->id;
                        $newAttendance->session_starts_at = $session->starts_at;
                        $newAttendance->session_ends_at = $session->ends_at;
                        $newAttendance->instructor_id = $session->course->instructor_id;
                        $newAttendance->trainee_id = $oldAttendance->trainee_id;
                        $newAttendance->trainee_user_id = $oldAttendance->trainee_user_id;
                        $newAttendance->attended_at = $oldAttendance->attended_at ?: $this->confirmTraineeAttendedAt($oldAttendance, $session);
                        $newAttendance->status = $this->getAttendanceStatus($oldAttendance, $session);
                        $newAttendance->absence_reason = $oldAttendance->absence_reason;
                        $newAttendance->last_login_at = $this->getLastLogin($oldAttendance, $session);
                        $newAttendance->created_at = $oldAttendance->created_at;
                        $newAttendance->updated_at = $oldAttendance->updated_at;
                        $newAttendance->save();
                    }
                    $bar->advance(100);
                }

                // Another round to fill the missing gaps.
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
                    }])->findOrFail($session->id);
                $usersWhoDidntAttended = [];
                if ($users->course_batch->trainee_group) {
                    foreach ($users->course_batch->trainee_group->trainees as $trainee) {
                        $hasAttended = AttendanceReportRecord::where('course_batch_session_id', $session->id)
                            ->where('trainee_id', $trainee->id)
                            ->first();
                        if (!$hasAttended) {
                            $usersWhoDidntAttended[] = $trainee;
                        }
                    }

                    foreach ($usersWhoDidntAttended as $didntAttend) {
                        if ($didntAttend->created_at->isAfter($session->created_at)) continue;
                        if (!optional($session->course)->instructor_id) continue;
                        $newAttendance = new AttendanceReportRecord();
                        $newAttendance->team_id = $didntAttend->team_id;
                        $newAttendance->attendance_report_id = $report->id;
                        $newAttendance->course_id = $users->course_id;
                        $newAttendance->course_batch_id = $users->course_batch_id;
                        $newAttendance->course_batch_session_id = $users->id;
                        $newAttendance->session_starts_at = $session->starts_at;
                        $newAttendance->session_ends_at = $session->ends_at;
                        $newAttendance->instructor_id = $session->course->instructor_id;
                        $newAttendance->trainee_id = $didntAttend->id;
                        $newAttendance->trainee_user_id = $didntAttend->trainee_user_id;
                        $newAttendance->attended_at = null;
                        $newAttendance->status = AttendanceReportRecord::STATUS_ABSENT;
                        $newAttendance->absence_reason = null;
                        $newAttendance->last_login_at = $didntAttend->last_login_at;
                        $newAttendance->save();
                    }
                }

            }

        });

        \DB::commit();

        return 1;
    }

    public function confirmTraineeAttendedAt($oldAttendance, $session)
    {
        if ($oldAttendance->created_at->isBetween($session->starts_at, $session->ends_at)) {
            return $oldAttendance->created_at;
        } else {
            return null;
        }
    }

    public function getAttendanceStatus($oldAttendance, $session)
    {
        // If absent
        if ((int) $oldAttendance->status === CourseBatchSessionAttendance::STATUS_ABSENT) {
            return AttendanceReportRecord::STATUS_ABSENT;
        }

        // If excuse
        if ((int) $oldAttendance->status === CourseBatchSessionAttendance::STATUS_ABSENT_FORGIVEN) {
            return AttendanceReportRecord::STATUS_ABSENT_WITH_EXCUSE;
        }

        $late = null;
        $present = null;

        // If late
        if ($oldAttendance->created_at->isBetween($session->starts_at->addMinutes(15), $session->ends_at)) {
            $late = AttendanceReportRecord::STATUS_LATE_TO_CLASS;
        }

        // If present
        if ($oldAttendance->created_at->isBetween($session->starts_at->subMinutes(10), $session->starts_at->addMinutes(15))) {
            $present = AttendanceReportRecord::STATUS_PRESENT;
        }

        if ($present) {
            return $present;
        }

        if ($late) {
            return $late;
        }

        if ($oldAttendance->attendance_status === 'absent') {
            return AttendanceReportRecord::STATUS_ABSENT;
        }

        if ($oldAttendance->attendance_status === 'present_late') {
            return AttendanceReportRecord::STATUS_LATE_TO_CLASS;
        }

        if ($oldAttendance->attendance_status === 'present_late') {
            return AttendanceReportRecord::STATUS_LATE_TO_CLASS;
        }

        if ($oldAttendance->attendance_status === 'present') {
            return AttendanceReportRecord::STATUS_PRESENT;
        }

        return null;
    }

    public function getLastLogin($oldAttendance, $session)
    {
        if (optional($oldAttendance->last_login_at)->isAfter($session->ends_at)) {
            return null;
        } else {
            return $oldAttendance->last_login_at;
        }
    }
}
