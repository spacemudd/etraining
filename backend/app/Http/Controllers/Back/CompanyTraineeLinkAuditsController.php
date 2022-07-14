<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\CompanyTraineeLinkAudit;
use App\Models\Back\TraineeCompanyMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyTraineeLinkAuditsController extends Controller
{
    public function index($company_id, Request $request)
    {
        if ($request->from_date && $request->to_date) {
            $from_date = Carbon::parse($request->from_date)->startOfDay();
            $to_date = Carbon::parse($request->to_date)->endOfDay();
        } else {
            $from_date = now()->startOfMonth()->startOfDay();
            $to_date = now()->endOfMonth()->endOfDay();
        }

        $company = Company::findOrFail($company_id);

        $logs = QueryBuilder::for(CompanyTraineeLinkAudit::class)
            ->with('trainee')
            ->where('company_id', $company_id)
            ->groupBy('trainee_id');
            if ($request->from_date && $request->to_date) {
                $logs = $logs->whereBetween('created_at', [$from_date, $to_date]);
            }
            $logs = $logs->defaultSort('created_at')
            ->allowedFilters(['created_at', 'trainee.name'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Companies/Trainees/CompanyTraineeLinkAudit/Index', [
            'company' => $company,
            'activityLog' => $logs,
            'date_from' => $from_date->format('Y-m-d'),
            'date_to' => $to_date->format('Y-m-d'),
        ])->table(function ($table) {
            $table->disableGlobalSearch();
            $table->addSearchRows([
                'trainee.name' => __('words.trainee'),
            ]);
        });
    }
}
