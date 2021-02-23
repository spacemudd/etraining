<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoomMeetingsController;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\TraineeGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;
use MacsiDigital\Zoom\Support\Entry;
use MacsiDigital\Zoom\User;
use Zoom;

class CourseBatchSessionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $course_id
     * @param $course_batch_id
     * @param $course_batch_session_id
     * @return \Illuminate\Http\Response
     */
    public function index($course_id, $course_batch_id)
    {
        return CourseBatch::findOrFail($course_batch_id)->course_batch_sessions;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $course_id
     * @param $course_batch_id
     * @return void
     */
    public function store(Request $request, $course_id, $course_batch_id)
    {
        $request->validate([
            'starts_at' => 'required|date',
            'ends_at' => 'required|date',
            'course_batch_id' => 'required|exists:course_batches,id',
        ]);

        $batch = CourseBatch::findOrFail($request->course_batch_id);
        $session = CourseBatchSession::create([
            'course_id' => $batch->course_id,
            'course_batch_id' => $batch->id,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ]);

        if ($request->wantsJson()) {
            return response()->json($session->toArray());
        }

        return redirect()->route('back.courses.show', $session->course_id);
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

        $zoomSettings = $session->course->instructor->zoom_account;
        $zoom = new Entry($zoomSettings->ZOOM_CLIENT_KEY, $zoomSettings->ZOOM_CLIENT_SECRET);
        $user = new User($zoom);

        if (! $session->zoom_meeting_id) {
	        // https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingcreate
            $meeting = $user->find('me')->meetings()->create([
                'topic' => $session->course->course_name,
                'type' => ZoomMeetingsController::ZOOM_SCHEDULED_MEETING,
                'start_time' => $session->starts_at->toIso8601ZuluString(),
                'password' => '123123',
                'timezone' => 'Asia/Riyadh',
                'settings' => [
                    'participant_video' => false,
                    'mute_upon_entry' => true,
                    'host_video' => false,
                    'show_share_button' => false,
                    'watermark' => false,
                    'use_pmi' => false,
                    'approval_type' => 2, // 0-automatic, 1-manually, 2-not required
                    'registration_type' => 2, // 1-Attendees register once and can attend any of the occurrences., 2-Attendees need to register for each occurrence to attend., 3-Attendees register once and can choose one or more occurrences to attend.
                    'audio' => 'voip', // voip, telephony, both
                    'auto_recording' => 'none', // none, local, cloud
                    'waiting_room' => false,
                    'global_dial_in_countries' => [],
                    'contact_name' => null,
                    'contact_email' => null
                ],
            ]);
            $session->zoom_meeting_id = $meeting->id;
            $session->start_url = $meeting->start_url;
            $session->join_url = $meeting->join_url;
            $session->save();
        }

       Inertia::setRootView('zoom');
        return Inertia::render('Teaching/CourseBatchSessions/Show', [
            'course_batch_session' => $session,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $course_id
     * @param $course_batch_id
     * @param $course_batch_session_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($course_id, $course_batch_id, $course_batch_session_id)
    {
        return CourseBatchSession::find($course_batch_session_id)->delete();
    }
}
