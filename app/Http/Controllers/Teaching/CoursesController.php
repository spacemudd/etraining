<?php

namespace App\Http\Controllers\Teaching;

use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CoursesController extends Controller
{
    public function show($course_id)
    {
        return Inertia::render('Teaching/Courses/Show', [
            'course' => Course::findOrFail($course_id),
        ]);
    }
}
