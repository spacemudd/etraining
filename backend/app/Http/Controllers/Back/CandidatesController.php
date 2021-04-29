<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Models\Back\Company;
use App\Exports\Back\CandidateExport;
use App\Models\Back\ExportTraineesToExcelJobTracker;
use App\Jobs\ExportCandidatesToExcelJob;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;



class CandidatesController extends Controller
{
    //
    public function index() {
        return Inertia::render('Back/Candidates/Index', [
            'candidates' => Trainee::candidates()->latest()->paginate(20),
            'candidates_count' => Trainee::candidates()->count(),
        ]);
    }

    public function excel()
    {

        $excelJob = new ExportTraineesToExcelJobTracker();
        $excelJob->queued_at = now();
        $excelJob->user_id = auth()->user()->id;
        $excelJob->team_id = auth()->user()->team_id;
        $excelJob->save();

        dispatch(new ExportCandidatesToExcelJob($excelJob));

        return $excelJob;
    }
}
