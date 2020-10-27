<?php

namespace App\Http\Controllers;

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

        //$meeting = Zoom::user()->find('me')->meetings;
        $meeting = Zoom::user()->find('me')->meetings()->create([
            'topic' => 'New Meeting',
            'type' => self::ZOOM_INSTANT_MEETING,
            'start_time' => now()->toIso8601ZuluString(),
            'password' => '123123',
        ]);

        return $meeting;
    }

    public function configs()
    {
        $meeting = Zoom::user()->find('me')->meetings()->create([
            'topic' => 'New Meeting',
            'type' => self::ZOOM_INSTANT_MEETING,
            'start_time' => now()->toIso8601ZuluString(),
            'password' => '123123',
        ]);

        return response()->json([
            'apiKey' => config('zoom.api_key'),
            'meetingNumber' => $meeting->id,
            'leaveUrl' => url('/dashboard'),
            'userName' => auth()->user()->email,
            'role' => self::ZOOM_HOST_ROLE,
            'password' => '123123',
        ]);
    }
}
