<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReportRecordWarning;
use App\Models\Back\Course;
use App\Models\Question;
use App\Models\Quiz;
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

    public function timeline($course_id)
    {
        $course = Course::attending()->with('instructor')->findOrFail($course_id);
        return Inertia::render('Trainees/Courses/Timeline', [
            'course' => $course,
        ]);
    }

    public function grades($course_id)
    {
        $course = Course::query()
            ->with([
                'instructor' => function($model){
                    $model->withTrashed();
                },
                'questions' => function($model){
                    $model->withTrashed();
                },
                'quizzes' => function($model){
                    $model->withTrashed();
                },
//                'quizzes' => function($model){
//                    $model->with([
//                        'questions' => function($model){
//                            $model->withTrashed();
//                        }
//                    ]);
//                },
            ])
            ->findOrFail($course_id);

        return Inertia::render('Trainees/Courses/Grades', [
            'course' => $course,
            'questions' => Question::get(),
            'quizzes' => Quiz::get(),
        ]);
    }

    public function messages($course_id)
    {
        $course = Course::attending()->with('instructor')->findOrFail($course_id);
        return Inertia::render('Trainees/Courses/Messages', [
            'course' => $course,
        ]);
    }

    public function resources($course_id)
    {
        $course = Course::attending()->with('instructor')->findOrFail($course_id);
        return Inertia::render('Trainees/Courses/Resources', [
            'course' => $course,
        ]);
    }
}
