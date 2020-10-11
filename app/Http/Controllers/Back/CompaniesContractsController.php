<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\CompanyContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\MediaLibrary\Support\MediaStream;

class CompaniesContractsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($company_id)
    {
        if (request()->wantsJson()) {
            return CompanyContract::where('company_id', $company_id)
                ->withCount('attachments')
                ->latest()
                ->get();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $company_id
     * @return \Inertia\Response
     */
    public function create($company_id)
    {
        return Inertia::render('Back/CompaniesContracts/Create', [
            'company' => Company::findOrFail($company_id),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'reference_number' => 'nullable|string|max:255',
            'contract_starts_at' => 'required|date',
            'contract_ends_at' => 'nullable|date',
            'contract_period_in_months' => 'nullable',
            'auto_renewal' => 'nullable|boolean',
            'trainees_count' => 'nullable',
            'trainee_salary' => 'nullable',
            'instructor_cost' => 'nullable',
            'company_reimbursement' => 'nullable',
            'notes' => 'nullable',
            'files[]' => 'nullable',
        ]);

        $company = Company::findOrFail($request->company_id);
        $contract = CompanyContract::make($request->except('_token'));
        $contract->team_id = $company->team_id;
        $contract->company_id = $company->id;
        $contract->created_by_id = optional(auth()->user())->id;
        $contract->save();

        // Check and save the files.
        $files = $request->file('files');
        if ($files) {
            foreach($files as $file) {
                $contract->addContractCopyAttachment($file);
            }
        }

        return redirect()->route('back.companies.show', $company);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Downloads attachments to the contract.
     *
     * @param $company_id
     * @param $contract_id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function attachments($company_id, $contract_id)
    {
        $contract = CompanyContract::where('id', $contract_id)
            ->where('company_id', $company_id)
            ->firstOrFail();

        $files = $contract->getMedia('contract_copy');

        if ($files->count() > 1) {
            return MediaStream::create(Str::slug($contract->reference_number.'-'.now()->format('d-m-Y').'-scans.zip'))->addMedia($files);
        }

        return redirect()->to($files->first()->getTemporaryUrl(now()->addMinutes(5), '', [
            'ResponseContentType' => 'application/octet-stream',
        ]));
    }
}
