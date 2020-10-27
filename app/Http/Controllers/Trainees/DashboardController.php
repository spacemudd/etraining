<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Models\Back\CourseBatchSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $sessions = CourseBatchSession::with(['course_batch' => function($q) {
            $q->with(['course' => function($q) {
                $q->with('instructor');
            }]);
        }])->paginate(15);

        return Inertia::render('Trainees/Dashboard', [
            'user' => auth()->user(),
            'sessions' => $sessions,
        ]);
    }
}
