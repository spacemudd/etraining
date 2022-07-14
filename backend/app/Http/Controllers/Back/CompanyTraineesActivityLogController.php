<?php

namespace App\Http\Controllers\Back;

use App\Exports\Back\ComapnyTraineeActivityLogExport;
use App\Exports\CompanyTraineeActivityLogExport;
use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\TraineeCompanyMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyTraineesActivityLogController extends Controller
{
    public function index($company_id)
    {
        $company = Company::findOrFail($company_id);

        $activityLog = QueryBuilder::for(TraineeCompanyMovement::class)
            ->where('company_id', $company_id)
            ->defaultSort('created_at')
            ->allowedSorts(['in_date', 'out_date'])
            ->allowedFilters(['created_at', 'trainee_name'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Companies/ActivityLog', [
            'company' => $company,
            'activityLog' => $activityLog,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addSearchRows([
                'trainee_name' => __('words.trainee'),
            ]);
        });
    }

    public function excel(Request $request)
    {
        $request->validate([
            'from_date' => [
                'required',
                'date',
                'before:to_date'
            ],
            'to_date' => [
                'required',
                'date',
                'after:from_date'
            ],
            'trainee_name' => 'nullable|string|max:255',
            'company' => 'required|exists:companies,id',
        ]);

        $filename = now()->format('Y-m-d-h-i').'-trainees-activity-log.xlsx';
        return Excel::download(new CompanyTraineeActivityLogExport($request, $request->company_id), $filename);
    }
}
