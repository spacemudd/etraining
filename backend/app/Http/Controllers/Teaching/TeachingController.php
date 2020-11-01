<?php

namespace App\Http\Controllers\Teaching;

use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use App\Models\Back\CourseBatchSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeachingController extends Controller
{
    public function dashboard()
    {
        $sessions = CourseBatchSession::with(['course_batch' => function($q) {
            $q->with(['course' => function($q) {
                $q->with('instructor');
            }]);
        }])->paginate(15);

        return Inertia::render('Teaching/Dashboard', [
            'user' => auth()->user(),
            'sessions' => $sessions,
        ]);
    }
}
