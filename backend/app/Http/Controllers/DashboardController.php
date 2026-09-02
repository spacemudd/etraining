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

        // Prefer staff dashboard when the user also has non-trainee / non-instructor roles.
        $roles = $user->roles;
        $hasStaffRole = $roles->contains(static function ($role) {
            $roleName = $role->name ?? '';

            return ! Str::contains($roleName, 'instructors')
                && ! Str::contains($roleName, 'trainees');
        });

        if (! $hasStaffRole) {
            $instructorRole = $roles->first(static function ($role) {
                return Str::contains($role->name ?? '', 'instructors');
            });

            if ($instructorRole) {
                if (!$user->instructor) {
                    auth('web')->logout();
                    return redirect()->to('/');
                }
                return app()->make(TeachingController::class)->dashboard();
            }

            $traineeRole = $roles->first(static function ($role) {
                return Str::contains($role->name ?? '', 'trainees');
            });

            if ($traineeRole) {
                return app()->make(Trainees\DashboardController::class)->dashboard();
            }
        }

        // Check if user has limited view permission (identity only)
        $hasLimitedView = $user->can('view-trainee-identity-only');

        return Inertia::render('Dashboard', [
            'companies_count' => \App\Models\Back\Company::count(),
            'trainees_count' => \App\Models\Back\Trainee::count(),
            'trainees_candidates_count' => \App\Models\Back\Trainee::candidates()->count(),
            'trainees_approved_count' => \App\Models\Back\Trainee::approved()->count(),
            'trainees_incomplete_count' => \App\Models\Back\Trainee::incomplete()->count(),
            'instructors_count' => \App\Models\Back\Instructor::count(),
            'courses_count' => \App\Models\Back\Course::count(),
            'is_limited_view' => $hasLimitedView,
        ]);
    }
}
