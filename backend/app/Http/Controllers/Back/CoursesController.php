<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use Illuminate\Http\Request;
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
        return Inertia::render('Back/Courses/Index', [
            'courses' => Course::with('instructor')->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Back/Courses/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'instructor_id' => 'nullable|exists:instructors,id',
            'company_id' => 'nullable|exists:companies,id',
            'description' => 'nullable|string|max:255',
            'classroom_count' => 'nullable|numeric',
            'approval_code' => 'nullable|string|max:255',
            'days_duration' => 'nullable|numeric',
            'hours_duration' => 'nullable|numeric',
            'training_package' => 'nullable',
        ]);

        $course = Course::create($request->except('_token'));

        if ($file = $request->file('training_package')) {
            $course->uploadToFolder($file, 'training-package');
        }

        return redirect()->route('back.courses.show', $course->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Inertia::render('Back/Courses/Show', [
            'course' => Course::with('batches')->with('instructor')->findOrFail($id),
        ]);
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
        $request->validate([
            'name_en' => 'nullable|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|exists:instructors,id',
            'company_id' => 'nullable|exists:companies,id',
            'description' => 'nullable|string|max:255',
            'classroom_count' => 'nullable|numeric',
            'approval_code' => 'nullable|string|max:255',
            'days_duration' => 'nullable|numeric',
            'hours_duration' => 'nullable|numeric',
            'training_package' => 'nullable',
        ]);

        $course = Course::where('id', $id)->first();

        return $course->update(
            [
                "name_en" => $request->course['name_en'],
                "name_ar" => $request->course['name_ar'],
                "approval_code" => $request->course['approval_code'],
                "days_duration" => $request->course['days_duration'],
                "hours_duration" => $request->course['hours_duration'],
                "description" => $request->course['description']
            ]
        );
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

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $course_id
     * @return
     */
    public function storeTrainingPackage(Request $request, $course_id)
    {
        $request->validate([
            'training_package' => 'required_without_all:file',
            'file' => 'required_without_all:training_package',
        ]);

        $course = Course::findOrFail($course_id);
        if ($request->training_package) $file = $request->file('training_package');
        if ($request->file) $file = $request->file('file');
        return $course->uploadToFolder($file, 'training-package');
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $course_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteTrainingPackage(Request $request, $course_id)
    {
        $course = Course::findOrFail($course_id);
        $course->getMedia('training-package')->each->forceDelete();
        return response()->redirectToRoute('back.courses.show', $course->id);
    }

    /**
     * Approve a course to be viewed by everyone.
     *
     * @param $course_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($course_id)
    {
        $course = Course::findOrFail($course_id);
        $course->status = Course::STATUS_APPROVED;
        $course->save();
        return response()->redirectToRoute('back.courses.show', $course->id);
    }
}
