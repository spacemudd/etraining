<?php
/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Services;

use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReport;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeAbsent;

class AttendanceService
{
    public function markAttendance(CourseBatchSession $course_batch_session, Trainee $trainee)
    {
        // Find if the user has logged in the first 15 minutes of starting the session.
        $attendance = now()->isBetween(
            $course_batch_session->starts_at->subMinutes(5),
            $course_batch_session->starts_at->addMinutes(15)
        );

        $alreadyPunched = AttendanceReportRecord::where('trainee_id', $trainee->id)
            ->where('course_batch_session_id', $course_batch_session->id)
            ->first();

        if (! $alreadyPunched) {
            $course_batch_session->attendance_snapshots()->save(new AttendanceReportRecord([
                'attendance_report_id' => AttendanceReport::where('course_batch_session_id', $course_batch_session->id)
                    ->first()
                    ->id,
                'course_id' => $course_batch_session->course_id,
                'course_batch_id' => $course_batch_session->course_batch_id,
                'instructor_id' => $course_batch_session->course->instructor_id,
                'trainee_id' => $trainee->id,
                'trainee_user_id' => $trainee->user->id,
                'session_starts_at' => $course_batch_session->starts_at,
                'session_ends_at' => $course_batch_session->ends_at,
                'attended_at' => now(),
                'status' => $attendance ? AttendanceReportRecord::STATUS_PRESENT : AttendanceReportRecord::STATUS_LATE_TO_CLASS,
                'last_login_at' => optional($trainee->user)->last_login_at,
            ]));
        }

        return $alreadyPunched;
    }

    /**
     *
     * @param \App\Models\Back\CourseBatchSession $courseBatchSession
     * @return array
     */
    public function getMissingTraineesForCourseBatchSession(CourseBatchSession $courseBatchSession)
    {
        $session = CourseBatchSession::with('course')
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
            }])->findOrFail($courseBatchSession->id);

        $usersWhoDidntAttended = [];

        foreach ($session->course_batch->trainee_group->trainees as $trainee) {
            $hasAttended = CourseBatchSessionAttendance::where('course_batch_session_id', $session->id)
                ->where('trainee_id', $trainee->id)
                ->first();
            if (!$hasAttended) {
                $usersWhoDidntAttended[] = $trainee;
            }
        }

        return $usersWhoDidntAttended;
    }
}
