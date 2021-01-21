<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Imports\TraineesCsvImport;
use App\Models\Back\Company;
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
        return Inertia::render('Trainees/Import/Index', [
            'companies' => Company::get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'excel_file' => 'required',
            'company_id' => 'required|exists:companies,id',
        ]);

        $path = request()->file('excel_file')->store('tmp');
        $filepath = storage_path('app').'/'.$path;

        $rows = Excel::import(new TraineesCsvImport($request->company_id), $filepath);

        return response()->json([
            'success' => true,
        ]);
    }
}
