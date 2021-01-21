<?php

namespace App\Http\Controllers\Teaching;

use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Teaching/Courses/Index', [
            'id' => auth()->user()->instructor->id,
            //'courses' => Course::responsibleToTeach()->with('instructor')->latest()->paginate(10),
            'courses' => Course::with('instructor')->latest()->paginate(10),
        ]);
    }


    public function show($course_id)
    {
        return Inertia::render('Teaching/Courses/Show', [
            //'course' => Course::responsibleToTeach()->findOrFail($course_id),
            'course' => Course::findOrFail($course_id),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Teaching/Courses/Create');
    }

    /**
     * Store a new course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'classroom_count' => 'nullable|numeric',
            'approval_code' => 'nullable|string|max:255',
            'days_duration' => 'nullable|numeric',
            'hours_duration' => 'nullable|numeric',
            'training_package' => 'nullable',
        ]);

        $course = Course::create($request->only([
            'name_en',
            'name_ar',
            'description',
            'classroom_count',
            'approval_code',
            'days_duration',
            'hours_duration',
            'training_package'
        ]));
        $course->status = Course::STATUS_PENDING;
        $course->instructor_id = auth()->user()->instructor->id;
        $course->save();

        if ($file = $request->file('training_package')) {
            $course->uploadToFolder($file, 'training-package');
        }

        Log::info('Created a new program by instructor: '.auth()->user()->email);

        return redirect()->route('teaching.courses.show', $course->id);
    }

    /**
     * Download the training package.
     *
     * @param $course_id
     */
    public function trainingPackage($course_id)
    {

    }
}
