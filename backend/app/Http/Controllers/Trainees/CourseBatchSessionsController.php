<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoomMeetingsController;
use App\Models\Back\CourseBatchSession;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseBatchSessionsController extends Controller
{
    public $attendanceService;

    public function __construct(AttendanceService $service)
    {
        $this->attendanceService = $service;
    }
    /**
     * Display the specified resource.
     *
     * @param $course_id
     * @param $course_batch_id
     * @param $course_batch_session_id
     * @return \Inertia\Response
     */
    public function show($course_id, $course_batch_id, $course_batch_session_id)
    {
        $session = CourseBatchSession::with(['course', 'course_batch'])->findOrFail($course_batch_session_id);

        if (! $session->zoom_meeting_id) {
            dd(1);
            abort(403);
        }

        $this->attendanceService->markAttendance($session, auth()->user()->trainee);

        Inertia::setRootView('zoom');

        return Inertia::render('Trainees/CourseBatchSessions/Show', [
            'course_batch_session' => $session,
        ]);
    }
}
