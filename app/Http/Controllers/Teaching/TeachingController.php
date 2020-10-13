<?php

namespace App\Http\Controllers\Teaching;

use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeachingController extends Controller
{
    public function dashboard()
    {
        $courses = Course::get();
        return Inertia::render('Teaching/Dashboard/Index', [
            'courses' => $courses,
        ]);
    }
}
