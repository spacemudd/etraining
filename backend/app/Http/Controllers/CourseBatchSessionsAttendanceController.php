<?php

namespace App\Http\Controllers;

use App\Exports\Back\CourseBatchSessionAttendanceExport;
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
            ->with(['course_batch' => function($q) {
                $q->with(['trainee_group' => function($q) {
                    $q->with(['trainees' => function($q) {
                        $q->addSelect(['has_attended' => CourseBatchSessionAttendance::select('attended')
                            ->whereColumn('trainee_id', 'trainees.id')
                            ->take(1)
                        ]);
                    }]);
                }]);
            }])->findOrFail($course_batch_session_id);

        return Inertia::render('Teaching/CourseBatchSessions/Attendance/Index', [
            'course_batch_session' => $courseBatchSession,
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

        throw_if($courseBatchSession->committed_attendances_at, 'Attendances were already committed');

        DB::beginTransaction();
        foreach ($request->trainees as $trainee) {
            $status = CourseBatchSessionAttendance::STATUS_PRESENT;
            if ($trainee['status'] === 'present') {
                $status = CourseBatchSessionAttendance::STATUS_PRESENT;
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
                $att = CourseBatchSessionAttendance::make([
                    'course_batch_session_id' => $courseBatchSession->id,
                    'course_batch_id' => $courseBatchSession->course_batch->id,
                    'course_id' => $courseBatchSession->course->id,
                    'trainee_id' => $trainee['id'],
                    'trainee_user_id' => Trainee::findOrFail($trainee['id'])->user_id,
                    'session_starts_at' => $courseBatchSession->starts_at,
                    'session_ends_at' => $courseBatchSession->ends_at,
                    'attended' => false,
                    'absence_reason' => $trainee['absence_reason'],
                    'status' => $status,
                ]);
                $att->team_id = $courseBatchSession->team_id;
                $att->save();
            }
        }

        $courseBatchSession->committed_attendances_at = now();
        $courseBatchSession->save();
        DB::commit();

        CourseBatchSessionWarningsJob::dispatch($courseBatchSession);

        return response()->redirectToRoute('teaching.courses.show', $courseBatchSession->course_id);
    }

    public function attendingExcel($course_batch_session_id)
    {
        return Excel::download(new AttendanceSheetExport($course_batch_session_id),'Attendance Sheet.xlsx');
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
}
