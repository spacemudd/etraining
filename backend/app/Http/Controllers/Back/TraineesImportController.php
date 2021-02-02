<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Imports\TraineesCsvImport;
use App\Models\Back\Company;
use App\Models\Back\TraineeGroup;
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
            'trainee_group_name' => 'required_without:trainee_group_id',
            'trainee_group_id' => 'required_without:trainee_group_name',
            'company_id' => 'required|exists:companies,id',
        ]);

        $path = request()->file('excel_file')->store('tmp');
        $filepath = storage_path('app').'/'.$path;

        if ($request->trainee_group_name === $request->trainee_group_id) {
            $trainee_group_id = null;
        } else {
            $trainee_group_id = str_before($request->trainee_group_id, '-group');
        }

        Excel::import(new TraineesCsvImport($request->company_id, $request->trainee_group_name, $trainee_group_id), $filepath);

        return response()->json([
            'success' => true,
        ]);
    }
}
