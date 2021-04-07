<?php

namespace App\Http\Controllers;

use App\Jobs\CourseBatchSessionWarningsJob;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use App\Models\Back\Trainee;
use App\Exports\AttendanceSheetExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Excel;

class CourseBatchSessionsAttendanceController extends Controller
{
    /**
     * Display the attendances of the course batch session's.
     *
     * @param $course_batch_session_id
     * @return \Inertia\Response
     */
    public function index($course_batch_session_id): \Inertia\Response
    {
        $courseBatchSession = CourseBatchSession::with('course')
            ->with(['course_batch' => function($q) use ($course_batch_session_id) {
                $q->with(['trainee_group' => function($q) use ($course_batch_session_id) {
                    $q->with(['trainees' => function($q) use ($course_batch_session_id) {
                        $q->with(['attendances' => function($q) use ($course_batch_session_id) {
                            $q->where('course_batch_session_id', $course_batch_session_id);
                        }])->paginate(10);
                    }]);
                }]);
            }])->findOrFail($course_batch_session_id);

        $attendances = CourseBatchSessionAttendance::where('course_batch_session_id', $course_batch_session_id)
            ->with('trainee')
            ->paginate(10);

        //$courseBatchSession->course_batch->trainee_group
        //    ->setRelation('trainees',
        //        $courseBatchSession->course_batch->trainee_group->trainees()->with(['attendances' => function($q) use ($course_batch_session_id) {
        //            $q->where('course_batch_session_id', $course_batch_session_id);
        //        }])->paginate(100)
        //    );

        return Inertia::render('Teaching/CourseBatchSessions/Attendance/Index', [
            'course_batch_session' => $courseBatchSession,
            'attendances' => $attendances,
        ]);
    }

    /**
     * Save the attendance for a trainee.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_batch_session_id' => 'required|exists:course_batch_sessions,id',
        ]);

        $courseBatchSession = CourseBatchSession::with(['course', 'course_batch'])->findOrFail($request->course_batch_session_id);

        // throw_if($courseBatchSession->committed_attendances_at, 'Attendances were already committed');

        DB::beginTransaction();
        foreach ($request->trainees as $trainee) {
            $status = CourseBatchSessionAttendance::STATUS_PRESENT;
            if ($trainee['status'] === 'present') {
                $status = CourseBatchSessionAttendance::STATUS_PRESENT;
            }

            if ($trainee['status'] === 'present_late') {
                $status = CourseBatchSessionAttendance::STATUS_PRESENT_LATE_TO_COURSE;
            }

            if ($trainee['status'] === 'absent') {
                $status = CourseBatchSessionAttendance::STATUS_ABSENT;
            }

            if ($trainee['status'] === 'absent_forgiven') {
                $status = CourseBatchSessionAttendance::STATUS_ABSENT_FORGIVEN;
            }

            $attendance = CourseBatchSessionAttendance::where('trainee_id', $trainee['id'])
                ->where('course_batch_session_id', $courseBatchSession->id)
                ->first();

            if ($attendance) {
                $attendance->attended = $attendance->attended;
                $attendance->absence_reason = $trainee['absence_reason'];
                $attendance->status = $status;
                $attendance->save();
            } else {
                $traineeObject = Trainee::findOrFail($trainee['id']);
                $att = CourseBatchSessionAttendance::make([
                    'course_batch_session_id' => $courseBatchSession->id,
                    'course_batch_id' => $courseBatchSession->course_batch->id,
                    'course_id' => $courseBatchSession->course->id,
                    'trainee_id' => $trainee['id'],
                    'trainee_user_id' => $traineeObject->user_id,
                    'session_starts_at' => $courseBatchSession->starts_at,
                    'session_ends_at' => $courseBatchSession->ends_at,
                    'attended' => false,
                    'absence_reason' => $trainee['absence_reason'],
                    'status' => $status,
                    'last_login_at' => optional($traineeObject->user)->last_login_at,
                ]);
                $att->team_id = $courseBatchSession->team_id;
                $att->save();
            }
        }

        $courseBatchSession->committed_attendances_at = now();
        $courseBatchSession->save();
        DB::commit();

        return response()->redirectToRoute('teaching.course-batch-sessions.attendance.confirm', $courseBatchSession->id);
    }

    public function attendingExcel($course_batch_session_id)
    {
        return Excel::download(new AttendanceSheetExport($course_batch_session_id),'Attendance Sheet.xlsx');
    }

    public function update(Request $request, $course_batch_session_id, $attendance_id)
    {
        $request->validate([
            'status' => 'required|string',
            'absence_reason' => 'nullable|string',
        ]);

        $attendance = CourseBatchSessionAttendance::with('trainee')->findOrFail($attendance_id);

        switch ($request->status) {
            case 'present':
                $attendance->status = CourseBatchSessionAttendance::STATUS_PRESENT;
                $attendance->absence_reason = null;
                break;
            case 'present_late':
                $attendance->status = CourseBatchSessionAttendance::STATUS_PRESENT_LATE_TO_COURSE;
                $attendance->absence_reason = null;
                break;
            case 'absent_forgiven':
                $attendance->status = CourseBatchSessionAttendance::STATUS_ABSENT_FORGIVEN;
                $attendance->absence_reason = $request->absence_reason;
                break;
            case 'absent':
                $attendance->status = CourseBatchSessionAttendance::STATUS_ABSENT;
                $attendance->absence_reason = null;
                break;
        }

        $attendance->save();

        return $attendance;
    }

    public function updateTraineeAttendance(Request $request, $session_id)
    {
        $request->validate([
            'trainee_id' => 'required',
            'status' => 'required|string',
        ]);

        $trainee = Trainee::findOrFail($request->trainee_id);
        $attendance = CourseBatchSessionAttendance::where('course_batch_session_id', $session_id)
            ->where('trainee_id', $trainee->id)
            ->first();

        if ($request->status === 'present') {
            $attendance->status = CourseBatchSessionAttendance::STATUS_PRESENT;
        } elseif ($request->status === 'absent') {
            $attendance->status = CourseBatchSessionAttendance::STATUS_ABSENT;
        } elseif ($request->status === 'absent_forgiven') {
            $attendance->status = CourseBatchSessionAttendance::STATUS_ABSENT_FORGIVEN;
        }

        $attendance->save();

        return $attendance;
    }

    /**
     * Confirm sending our warnings.
     *
     * @param $course_batch_session_id
     * @return \Inertia\Response
     */
    public function confirm($course_batch_session_id)
    {
        $session = CourseBatchSession::with('course')->findOrFail($course_batch_session_id);
        $sendingWarningsToQuery = $session->attendances()
            ->with('trainee')
            ->where('status', CourseBatchSessionAttendance::STATUS_ABSENT);
            //->where('attended', false);

        $sendingLateWarningsQuery = $session->attendances()
            ->with('trainee')
            ->where('status', CourseBatchSessionAttendance::STATUS_PRESENT_LATE_TO_COURSE);

        return Inertia::render('Teaching/CourseBatchSessions/Attendance/Confirm', [
            'course_batch_session' => $session,
            'sending_absent_warnings_to_count' => $sendingWarningsToQuery->count(),
            'sending_absent_warnings_to_list' => $sendingWarningsToQuery->get(),
            'sending_late_warnings_to_count' => $sendingLateWarningsQuery->count(),
            'sending_late_warnings_to_list' => $sendingLateWarningsQuery->get(),
        ]);
    }

    /**
     * Approve the attendance sheet.
     *
     */
    public function approve($course_batch_session_id)
    {
        $courseBatchSession = CourseBatchSession::findOrFail($course_batch_session_id);
        CourseBatchSessionWarningsJob::dispatch($courseBatchSession);
        return response()->redirectToRoute('teaching.courses.show', $courseBatchSession->course_id);
    }
}
