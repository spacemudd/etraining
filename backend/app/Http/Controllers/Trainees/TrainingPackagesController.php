<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainingPackagesController extends Controller
{
    /**
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $instructor = optional(auth()->user()->trainee)->instructor;
        $courses = Course::where('instructor_id', $instructor->id)->get();

        return Inertia::render('Trainees/TrainingPackages/Index', [
            'courses' => $courses,
        ]);
    }
}
