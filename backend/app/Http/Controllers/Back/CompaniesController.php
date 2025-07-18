<?php

namespace App\Http\Controllers\Back;

use App\Exports\CompaniesExport;
use App\Http\Controllers\Controller;
use App\Models\Back\Audit;
use App\Models\Back\Center;
use App\Models\Back\Company;
use App\Models\Back\Instructor;
use App\Models\Back\RecruitmentCompany;
use App\Models\Back\Region;
use App\Models\Back\Trainee;
use App\Models\User;
use App\Notifications\DeleteCompanyNotification;
use App\Notifications\NewCompanyNotification;
use App\Services\CompaniesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Excel;


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
            'centers' => Center::orderBy('name')->get(),
            'recruitmentCompanies' => RecruitmentCompany::orderBy('name')->get(),
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
        'nature_of_work' => 'nullable|string|max:255',
        'salesperson_email' => 'nullable|email',
        'salesperson_name' => 'nullable',
        'region_id' => 'nullable|exists:regions,id',
        'center_id' => 'nullable|exists:centers,id',
        'recruitment_company_id' => 'nullable|exists:recruitment_companies,id',
    ]);


    $company = Company::create($validated);

    app()->make(CompaniesService::class)->notifyUsersAboutNewCompany($company);

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
                'center',
                'resignations',
                'company_mails',
                'recruitmentCompany',
            ])
            ->withCount([
                'trainees' => function($model) {
                    $model->where('posted_at', '=', null)->withTrashed();
                },
                'aliases',
            ])
            ->with('region')
            ->with('center');

        if (auth()->user()->can('view-deleted-companies')) {
            $company = $company->withTrashed();
        }

        $company = $company->findOrFail($id);

        Gate::authorize('view-company', $company);

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

        // Get all trainee counts
        $total_trainees_count = Trainee::where('company_id', $company->id)->withTrashed()->count();
        $active_trainees_count = Trainee::where('company_id', $company->id)->count();
        $posted_trainees_count = Trainee::where('company_id', $company->id)->whereNotNull('posted_at')->withTrashed()->count();
        $trashed_trainees_count = Trainee::where('company_id', $company->id)->onlyTrashed()->count();
        $trashed_not_posted_count = Trainee::where('company_id', $company->id)->onlyTrashed()->whereNull('posted_at')->count();

        return Inertia::render('Back/Companies/Show', [
            'company' => array_merge($company->toArray(), [
                'total_trainees_count' => $total_trainees_count,
                'trainees_count' => $active_trainees_count,
                'posted_trainees_count' => $posted_trainees_count,
                'trashed_not_posted_count' => $trashed_not_posted_count,
            ]),
            'invoices' => $invoices,
            'instructors' => Instructor::get(),
            'trainees_trashed_count' => $trashed_trainees_count,
            'regions' => Region::orderBy('name')->get(),
            'centers' => Center::orderBy('name')->get(),
            'recruitmentCompanies' => RecruitmentCompany::orderBy('name')->get(),
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
        $company = Company::with('recruitmentCompany')->findOrFail($id);

        return Inertia::render('Back/Companies/Edit', [
            'company' => $company,
            'regions' => Region::orderBy('name')->get(),
            'centers' => Center::orderBy('name')->get(),
            'recruitmentCompanies' =>RecruitmentCompany::orderBy('name')->get(),
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
            'nature_of_work' => 'nullable|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
            'center_id' => 'nullable|exists:centers,id',
            'recruitment_company_id' => 'nullable|exists:recruitment_companies,id',
        ]);



        $company = Company::findOrFail($id);
        $company->update($request->except(['_token']));


        // Get regions and centers for redirection
        $region = Region::orderBy('name')->get();
        $center = Center::orderBy('name')->get();
        $recruitmentCompanies =  RecruitmentCompany::orderBy('name')->get();


        return redirect()->route('back.companies.show', ['company' => $company, 'region' => $region, 'center' => $center ,'recruitmentCompanies' => $recruitmentCompanies ])->with(
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

        app()->make(CompaniesService::class)->notifyUsersAboutDeletedCompany(Company::withTrashed()->find($id));

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


    public function exportArchived(){
        return Excel::download(new \App\Exports\ExportArchivedCompanies(),'archived-companies.xlsx');
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
        $company = Company::onlyTrashed()->findOrFail($id);
        $company->restore();
        app()->make(CompaniesService::class)->notifyUsersAboutNewCompany($company);
        return redirect()->route('back.companies.show', $id);
    }




}
