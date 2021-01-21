<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Imports\TraineesCsvImport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class TraineesImportController extends Controller
{
    /**
     * Show import page.
     *
     */
    public function index()
    {
        return Inertia::render('Trainees/Import/Index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'excel_file' => 'required',
        ]);

        $tmpfname = request()->file('excel_file')->getRealPath();
        rename($tmpfname, $tmpfname .= '.tmp');

        $rows = Excel::import(new TraineesCsvImport(), $tmpfname);

        return response()->json([
            'success' => true,
        ]);
    }
}
