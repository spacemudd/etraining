<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use Illuminate\Http\Request;

class CourseBatchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $course_id
     * @return \Illuminate\Http\Response
     */
    public function index($course_id)
    {
        return CourseBatch::orderBy('starts_at', 'desc')->where('course_id', $course_id)
            ->with(['trainee_group' => function($q) {
                $q->withCount('trainees');
            }])
            ->with(['course_batch_sessions' => function($q) {
                $q->with('attendance_report')->orderBy('starts_at', 'desc');
            }])->get();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $course_id)
    {
        $request->validate([
            'trainee_group_id' => 'required|exists:trainee_groups,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date',
            'location_at' => 'required|string',
        ]);

        $course = Course::findOrFail($course_id);
        $course->batches()->save(new CourseBatch($request->except('_token')));

        return redirect()->route('back.courses.show', ['course' => $course_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function destroy($coursE_id, $id)
    {
        CourseBatch::find($id)->delete();
        return response()->json([
            'success' => true,
        ]);
    }
}
