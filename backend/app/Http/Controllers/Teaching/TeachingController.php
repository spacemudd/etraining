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
        $ownedCourses = Course::responsibleToTeach()->pluck('id');

        $sessions = CourseBatchSession::whereIn('course_id', $ownedCourses)->with(['course_batch' => function($q) {
            $q->with(['course' => function($q) {
                $q->responsibleToTeach()->with('instructor');
            }]);
        }])->has('course_batch.course')
            ->oldest()
            ->paginate(15);

        return Inertia::render('Teaching/Dashboard', [
            'user' => auth()->user(),
            'sessions' => $sessions,
        ]);
    }
}
