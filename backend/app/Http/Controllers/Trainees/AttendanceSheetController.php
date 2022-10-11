da<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Models\Back\AttendanceReportRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceSheetController extends Controller
{
    public function index()
    {
        return Inertia::render('Trainees/AttendanceSheet/Index', [
            'records' => AttendanceReportRecord::where('trainee_id', auth()->user()->trainee->id)
                ->with(['course_batch_session' => function($q) {
                    $q->with('course');
                }])
                ->orderBy('session_starts_at', 'desc')
                ->get(),
        ]);
    }
}
