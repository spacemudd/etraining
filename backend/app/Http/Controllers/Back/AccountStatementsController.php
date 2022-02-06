<?php

namespace App\Http\Controllers\Back;

use App\Exports\TraineeAccountSummary;
use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class AccountStatementsController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Finance/AccountStatements', [
            'companies' => Company::select('id', 'name_ar')->orderBy('name_ar')->get(),
            'trainees' => Trainee::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function excel(Request $request)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'trainee_id' => 'nullable|exists:trainees,id',
        ]);

        if ($request->trainee_id) {
            return Excel::download(new TraineeAccountSummary($request->trainee_id), 'account-statement.xlsx');
        }

        //if ($request->trainee_id) {
        //    return Excel::download(new TraineeAccountSummary($request->trainee_id), 'account-statement.xlsx');
        //}
    }
}
