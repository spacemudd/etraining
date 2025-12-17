<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoomMeetingsController;
use App\Models\Back\AttendanceReport;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        DB::beginTransaction();
        try {
            $batch = CourseBatch::findOrFail($request->course_batch_id);
            $session = CourseBatchSession::create([
                'course_id' => $batch->course_id,
                'course_batch_id' => $batch->id,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
            ]);
            $report = new AttendanceReport();
            $report->course_batch_session_id = $session->id;
            $report->status = AttendanceReport::STATUS_DRAFT_REPORT;
            $report->submitted_by = null;
            $report->save();

            // إعادة ربط المدربين بالشعبة تلقائياً
            if ($batch->trainee_group_id) {
                $traineeGroupId = $batch->trainee_group_id;
                
                // البحث عن المدرب الأكثر شيوعاً في هذه الشعبة
                $instructorCounts = Trainee::where('trainee_group_id', $traineeGroupId)
                    ->whereNotNull('instructor_id')
                    ->selectRaw('instructor_id, COUNT(*) as count')
                    ->groupBy('instructor_id')
                    ->orderByDesc('count')
                    ->first();

                if ($instructorCounts && isset($instructorCounts->instructor_id)) {
                    $instructorId = $instructorCounts->instructor_id;
                    
                    // Get trainees before update for logging
                    $trainees = Trainee::where('trainee_group_id', $traineeGroupId)
                        ->get(['id', 'name', 'instructor_id']);
                    
                    // ربط جميع متدربي هذه الشعبة بالمدرب
                    Trainee::where('trainee_group_id', $traineeGroupId)
                        ->update(['instructor_id' => $instructorId]);
                    
                    // Log assignment
                    $traineeGroup = TraineeGroup::find($traineeGroupId);
                    foreach ($trainees as $trainee) {
                        Log::info('INSTRUCTOR_ID_CHANGED: CourseBatchSessionsController::store - Auto-linking instructor to group on session creation', [
                            'trainee_id' => $trainee->id,
                            'trainee_name' => $trainee->name,
                            'old_instructor_id' => $trainee->instructor_id,
                            'new_instructor_id' => $instructorId,
                            'trainee_group_id' => $traineeGroupId,
                            'trainee_group_name' => $traineeGroup->name ?? null,
                            'course_batch_id' => $batch->id,
                            'course_batch_session_id' => $session->id,
                            'reason' => 'إنشاء جدولة جديدة - إعادة ربط المدرب بالشعبة تلقائياً',
                            'location' => __FILE__ . ':' . __LINE__,
                            'method' => 'CourseBatchSessionsController::store',
                            'user_id' => auth()->id(),
                            'user_name' => auth()->user()->name ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if ($request->wantsJson()) {
            return response()->json($session->toArray());
        }

        return redirect()->route('back.courses.show', $session->course_id);
    }

    /**
     * Start the Zoom session.
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
        $zoom = new Entry($zoomSettings->account_id, $zoomSettings->client_id, $zoomSettings->client_secret);
        $user = new User($zoom);

        if (! $session->zoom_meeting_id) {
            // if the user is a trainee, give them an error.
            throw_if(auth()->user()->trainee, "The user is a trainee that's making a Zoom session before the instructor");

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
            $session->instructor_started_at = now();
            $session->save();
        }

       Inertia::setRootView('zoom');
        return Inertia::render('Teaching/CourseBatchSessions/Show', [
            'course_batch_session' => $session,
            'account_id' => $zoomSettings->account_id,
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
