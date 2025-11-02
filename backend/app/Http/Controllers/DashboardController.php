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
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->to('/login');
        }

        // Get the first role safely
        $firstRole = $user->roles()->first();
        
        if ($firstRole) {
            $roleName = $firstRole->name ?? '';
            
            if (Str::contains($roleName, 'instructors')) {
                if (!$user->instructor) {
                    auth('web')->logout();
                    return redirect()->to('/');
                }
                return app()->make(TeachingController::class)->dashboard();
            }

            if (Str::contains($roleName, 'trainees')) {
                return app()->make(Trainees\DashboardController::class)->dashboard();
            }
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
