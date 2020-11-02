<?php

namespace App\Http\Controllers;

use App\Models\Back\CourseBatchSession;
use Illuminate\Http\Request;
use Zoom;

class ZoomMeetingsController extends Controller
{
    const ZOOM_INSTANT_MEETING = 1;

    const ZOOM_HOST_ROLE = 1;
    const ZOOM_ATTENDEE_ROLE = 0;

    public function store(Request $request)
    {
        $request->validate([
            'course_batch_session_id' => 'required',
        ]);

        $meeting = Zoom::user()->find('me')->meetings()->create([
            'topic' => 'New Meeting',
            'type' => self::ZOOM_INSTANT_MEETING,
            'start_time' => now()->toIso8601ZuluString(),
            'password' => '123123',
        ]);

        $session = CourseBatchSession::find($request->course_batch_session_id);
        $session->zoom_meeting_id = $meeting->id;
        $session->save();

        return $meeting;
    }

    public function configs(Request $request)
    {
        $request->validate([
            'course_batch_session_id' => 'required',
        ]);

        $session = CourseBatchSession::find($request->course_batch_session_id);
        $meeting = Zoom::meeting()->find($session->zoom_meeting_id);

        \Log::info([
            'zoom' => json_encode((array) $meeting),
            'msg' => 'Generating Zoom configs',
            'meeting_id_from_zoom' => $meeting->id,
            'session_zoom_meeting_id' => $session->zoom_meeting_id,
        ]);

        return response()->json([
            'apiKey' => config('zoom.api_key'),
            'meetingNumber' => $meeting->id,
            'leaveUrl' => url('/dashboard'),
            'userName' => auth()->user()->email,
            'role' => auth()->user()->isTrainee() ? self::ZOOM_ATTENDEE_ROLE : self::ZOOM_HOST_ROLE,
            'password' => '123123',
        ]);
    }
}
