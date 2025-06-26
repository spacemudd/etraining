<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Jobs\TraineesReportJob;
use App\Models\Back\Trainee;
use App\Models\EducationalLevel;
use App\Models\JobTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TraineesReportController extends Controller
{
    public function index()
    {
        $this->authorize('view-backoffice-reports');
        
        // Get educational levels
        $educationalLevels = EducationalLevel::orderBy('order')->get(['id', 'name_ar', 'name_en']);
        
        // Get unique deleted marks (cached for performance)
        $deletedMarks = Cache::remember('trainee_deleted_marks', 3600, function () {
            return Trainee::withTrashed()
                ->whereNotNull('deleted_remark')
                ->distinct()
                ->pluck('deleted_remark')
                ->filter()
                ->values()
                ->toArray();
        });

        return Inertia::render('Back/Reports/Trainees/Index', [
            'educational_levels' => $educationalLevels,
            'deleted_marks' => $deletedMarks,
        ]);
    }

    public function generate(Request $request)
    {
        $this->authorize('view-backoffice-reports');
        
        $request->validate([
            'age_under' => 'nullable|integer|min:1|max:100',
            'has_invoices' => ['nullable', Rule::in(['yes', 'no'])],
            'assigned_to_company' => ['nullable', Rule::in(['yes', 'no'])],
            'status' => ['nullable', Rule::in(['0', '1', '2'])],
            'phone_is_owned' => ['nullable', Rule::in(['true', 'false'])],
            'educational_level_id' => 'nullable|exists:educational_levels,id',
            'deleted_mark' => 'nullable|string|max:255',
        ]);

        // Create job tracker
        $tracker = JobTracker::create([
            'user_id' => auth()->id(),
            'team_id' => auth()->user()->current_team_id,
            'queued_at' => now(),
            'metadata' => [
                'age_under' => $request->age_under,
                'has_invoices' => $request->has_invoices,
                'assigned_to_company' => $request->assigned_to_company,
                'status' => $request->status,
                'phone_is_owned' => $request->phone_is_owned,
                'educational_level_id' => $request->educational_level_id,
                'deleted_mark' => $request->deleted_mark,
            ],
        ]);

        // Dispatch job
        TraineesReportJob::dispatch($tracker);

        return response()->json($tracker);
    }
} 