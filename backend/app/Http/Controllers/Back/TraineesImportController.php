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

        $path = request()->file('excel_file')->store('tmp');
        $filepath = storage_path('app').'/'.$path.'.tmp';

        $rows = Excel::import(new TraineesCsvImport(), $filepath);

        return response()->json([
            'success' => true,
        ]);
    }
}
