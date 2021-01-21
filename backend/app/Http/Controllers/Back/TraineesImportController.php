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

        $rows = Excel::import(new TraineesCsvImport(), $request->file('excel_file')->getRealPath());
    }
}
