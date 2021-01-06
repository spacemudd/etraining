<?php

namespace App\Http\Controllers;

use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use App\Models\Back\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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
            ->with(['attendances' => function($q) {
                $q->with('trainee');
            }])
            ->with(['course_batch' => function($q) {
                $q->with(['trainee_group' => function($q) {
                    $q->with('trainees');
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
            'trainees.*.id' => 'required|exists:trainees,id',
            'trainees.*.physical_attendance' => 'required|boolean',
        ]);

        $courseBatchSession = CourseBatchSession::with(['course', 'course_batch'])->findOrFail($request->course_batch_session_id);

        DB::beginTransaction();
        foreach ($request->trainees as $trainee) {
            CourseBatchSessionAttendance::create([
                'course_batch_session_id' => $courseBatchSession->id,
                'course_batch_id' => $courseBatchSession->course_batch->id,
                'course_id' => $courseBatchSession->course->id,
                'trainee_id' => $trainee['id'],
                'trainee_user_id' => Trainee::findOrFail($trainee['id'])->user_id,
                'session_starts_at' => $courseBatchSession->starts_at,
                'session_ends_at' => $courseBatchSession->ends_at,
                'physical_attendance' => $trainee['physical_attendance'],
            ]);
        }
        DB::commit();

        return response()->redirectToRoute('teaching.courses.show', $courseBatchSession->course_id);
    }
}
