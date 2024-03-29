<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReportRecordWarning;
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
        // TODO: Refactor.
        // The attending() problem is that when the user is not assigned to an instructor,
        // they can view all courses that have instructor_id = null.
        if (optional(auth()->user()->trainee)->instructor_id) {
            $courses = Course::attending()
                ->with('instructor')
                ->latest()
                ->paginate(10);

            $withAttendances = $courses->each(function ($c) {
                $c->append('my_attendance');
            });
        } else {
            $courses = [];
            $withAttendances = [];
        }

        return Inertia::render('Trainees/Courses/Index', [
            'courses' => $courses,
            'withAttendances' => $withAttendances,
        ]);
    }

    public function show($course_id)
    {
        // TODO: Refactor.
        // The attending() problem is that when the user is not assigned to an instructor,
        // they can view all courses that have instructor_id = null.
        if (optional(auth()->user()->trainee)->instructor_id) {
            $course = Course::attending()->with('instructor')->findOrFail($course_id);
        } else {
            $course = null;
        }

        return Inertia::render('Trainees/Courses/Show', [
            'course' => $course,
        ]);
    }

    public function generateCertificate($course_id)
    {
       $course = Course::attending()->findOrFail($course_id);

       $trainee_name = auth()->user()->trainee->name;

       $pdf = PDF::loadView("pdf.trainees.certificate", ['course_name'=>$course->name_en, 'trainee_name'=>$trainee_name]);

        return $pdf->download();
    }
}
