<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Models\Back\Company;
use App\Exports\Back\CandidateExport;

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
        return Excel::download(new CandidateExport(), 'candidates.xlsx');
    }
}
