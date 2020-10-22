<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
