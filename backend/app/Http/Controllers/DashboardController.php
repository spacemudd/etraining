<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Teaching\TeachingController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        if (Str::contains(auth()->user()->roles()->first()->name, 'instructors')) {
            if (!auth()->user()->instructor) {
                auth()->logout();
                return url('/');
            }
            return app()->make(TeachingController::class)->dashboard();
        }

        if (Str::contains(auth()->user()->roles()->first()->name, 'trainees')) {
            return app()->make(Trainees\DashboardController::class)->dashboard();
        }

        return Inertia::render('Dashboard', [
            'companies_count' => \App\Models\Back\Company::count(),
            'trainees_count' => \App\Models\Back\Trainee::count(),
            'trainees_candidates_count' => \App\Models\Back\Trainee::candidates()->count(),
            'trainees_approved_count' => \App\Models\Back\Trainee::approved()->count(),
            'trainees_incomplete_count' => \App\Models\Back\Trainee::incomplete()->count(),
            'instructors_count' => \App\Models\Back\Instructor::count(),
            'courses_count' => \App\Models\Back\Course::count(),
        ]);
    }
}
