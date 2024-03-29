<?php

namespace App\Http\Controllers\Back;

use App\Exports\CompanyAccountSummary;
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
        $this->authorize('view-backoffice-reports');
        return Inertia::render('Back/Finance/AccountStatements', [
            'companies' => Company::select('id', 'name_ar')->withTrashed()->orderBy('name_ar')->toBase()->get(),
            'trainees' => Trainee::select('id', 'name')->withTrashed()->orderBy('name')->toBase()->get(),
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

        if ($request->company_id) {
            return Excel::download(new CompanyAccountSummary($request->company_id), 'account-statement.xlsx');
        }
    }
}
