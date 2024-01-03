<?php

namespace App\Http\Controllers\Back;

use App\Exports\CompaniesExport;
use App\Http\Controllers\Controller;
use App\Models\Back\Audit;
use App\Models\Back\Company;
use App\Models\Back\Instructor;
use App\Models\Back\Region;
use App\Models\Back\Trainee;
use App\Models\User;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('view-companies');

        if (request()->expectsJson()) {
            return Company::get();
        }

        return Inertia::render('Back/Companies/Index', [
            'companies' => Company::paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create-companies');

        return Inertia::render('Back/Companies/Create',[
            'regions' => Region::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create-companies');

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'cr_number' => 'nullable|max:255',
            'contact_number' => 'nullable|string|max:255',
            'company_rep' => 'nullable|string|max:255',
            'company_rep_mobile' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'monthly_subscription_per_trainee' => 'nullable|numeric|min:0|max:100000',
            'shelf_number' => 'nullable|string|max:255',
            'salesperson_email' => 'nullable|email',
            'region_id' => 'nullable|exists:regions,id',
        ]);

        $company = Company::create($validated);

        return redirect()->route('back.companies.show', ['company' => $company])->with(
            'success',
            __('words.created-successfully')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Inertia\Response
     */
    public function show($id)
    {
        $company = Company::query()
            ->with([
                'trainees' => function($model) {
                    $model->where('posted_at', '=', null)->withTrashed();
                },
                'contracts' => function ($model) {
                    $model->with([
                        'instructors' => function ($q) {
                            $q->withCount('trainees');
                        },
                    ]);
                },
                'region',
                'resignations',
                'company_mails',
            ])
            ->withCount([
                'trainees' => function($model) {
                    $model->where('posted_at', '=', null)->withTrashed();
                },
            ])
            ->with('region');

        if (auth()->user()->can('view-deleted-companies')) {
            $company = $company->withTrashed();
        }

        $company = $company->findOrFail($id);

//        Gate::authorize('view-company', $company);

        $invoices = $company->invoices()
            ->select('*')
            ->selectRaw('COUNT(id) as trainee_count')
            ->selectRaw('SUM(sub_total) as sub_total')
            ->selectRaw('SUM(tax) as tax')
            ->selectRaw('SUM(grand_total) as grand_total')
            ->groupByRaw('from_date, to_date, created_by_id, DATE(created_at)')
            ->with(['created_by'])
            ->latest()
            ->get();

        return Inertia::render('Back/Companies/Show', [
            'company' => $company,
            'invoices' => $invoices,
            'instructors' => Instructor::get(),
            'trainees_trashed_count' => Trainee::where('company_id', $company->id)->onlyTrashed()->count(),
            'regions' => Region::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        return Inertia::render('Back/Companies/Edit', [
            'company' => Company::findOrFail($id),
            'regions' => Region::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'cr_number' => 'nullable|max:255',
            'contact_number' => 'nullable|string|max:255',
            'company_rep' => 'nullable|string|max:255',
            'company_rep_mobile' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'monthly_subscription_per_trainee' => 'nullable|numeric|min:0|max:100000',
            'shelf_number' => 'nullable|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
        ]);

        $company = Company::findOrFail($id);
        $region = Region::orderBy('name')->get();
        $company->update($request->except('_token'));
        return redirect()->route('back.companies.show', ['company' => $company, 'region' => $region])->with(
            'success',
            __('words.updated-successfully')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Company::findOrFail($id)->delete();
        return redirect()->route('back.companies.index');
    }

    public function postTrainees($id)
    {
        DB::beginTransaction();
        $company = Company::findOrFail($id);
        $company->trainees()->onlyTrashed()->update(['posted_at' => now()]);
        DB::commit();
        return redirect()->route('back.companies.show', $id);
    }

    public function export()
    {
        Audit::create([
            'event' => 'companies.export.excel',
            'auditable_id' => auth()->user()->id,
            'auditable_type' => User::class,
            'new_values' => [],
        ]);
        return Excel::download(new CompaniesExport, now()->format('Y-m-d').'-companies.xlsx');
    }

    public function markAsPtcNet($id)
    {
        $company = Company::findOrFail($id);
        if ($company->is_ptc_net) {
            $company->update(['is_ptc_net' => null]);
        } else {
            $company->update(['is_ptc_net' => now()]);
        }

        return redirect()->route('back.companies.show', $id);
    }

    public function deleted()
    {
        $this->authorize('view-deleted-companies');

        if (request()->expectsJson()) {
            return Company::onlyTrashed()->get();
        }

        return Inertia::render('Back/Companies/Index', [
            'companies' => Company::onlyTrashed()->paginate(20),
        ]);
    }

    public function restore($id)
    {
        $this->authorize('restore-deleted-companies');
        $company = Company::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('back.companies.show', $id);
    }
}
