<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PDF;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Trainees/Courses/Index', [
            'courses' => Course::attending()->with('instructor')->latest()->paginate(10),
        ]);
    }

    public function show($course_id)
    {


        return Inertia::render('Trainees/Courses/Show', [
            'course' => Course::attending()->findOrFail($course_id),
        ]);

    }

    public function generateCertificate($course_id) {
       $course = Course::attending()->findOrFail($course_id);

       $trainee_name = auth()->user()->trainee->name;

       $pdf = PDF::loadView("pdf.trainees.certificate", ['course_name'=>$course->name_en, 'trainee_name'=>$trainee_name]);

        return $pdf->download();
    }
}
