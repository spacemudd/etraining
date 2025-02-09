<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Audit;
use App\Models\Back\Company;
use App\Models\Back\CompanyContract;
use App\Models\Back\Instructor;
use App\Models\Numbering;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\MediaLibrary\Support\MediaStream;
use App\Exports\Back\CompanyTraineeExport;
use Maatwebsite\Excel\Facades\Excel;


class CompaniesContractsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index($company_id)
    {
        $this->authorize('view-company-contracts');

        $contracts = CompanyContract::where('company_id', $company_id)
                ->with(['instructors' => function($q) use ($company_id) {
                    $q->with(['trainees_contract' => function($q) use ($company_id) {
                        $q->where('company_id', $company_id)
                            ->with(['trainee_group', 'company']);
                    }])
                    ->withCount('trainees_contract AS trainees_count');
                }])
                ->withCount('attachments')
                ->latest()
                ->get();

        return response()->json($contracts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $company_id
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($company_id)
    {
        $this->authorize('create-company-contracts');

        return Inertia::render('Back/CompaniesContracts/Create', [
            'company' => Company::findOrFail($company_id),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $this->authorize('create-company-contracts');

        $request->validate([
            'reference_number' => 'nullable|string|max:255|unique:company_contracts,reference_number',
            'contract_starts_at' => 'required|date',
            'contract_ends_at' => 'nullable|date',
            'contract_period_in_months' => 'nullable',
            'auto_renewal' => 'nullable|boolean',
            'trainees_count' => 'nullable',
            'trainee_salary' => 'nullable',
            'instructor_cost' => 'nullable',
            'company_reimbursement' => 'nullable',
            'notes' => 'nullable',
            'files.*' => 'required',
        ]);

        $company = Company::findOrFail($request->company_id);
        DB::beginTransaction();
        $contract = CompanyContract::make($request->except('_token'));
        if (!$request->reference_number) {
            $contract->reference_number = Numbering::getNewNumber(now()->format('dmY').'-');
        }
        $contract->team_id = $company->team_id;
        $contract->company_id = $company->id;
        $contract->created_by_id = optional(auth()->user())->id;
        $contract->save();
        DB::commit();

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
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($company_id, $id)
    {
        $this->authorize('view-company-contracts');
        return Inertia::render('Back/CompaniesContracts/Show', [
            'contract' => CompanyContract::with('company')->findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $company_id
     * @param $contract_id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($company_id, $contract_id)
    {
        $this->authorize('create-company-contracts');

        return Inertia::render('Back/CompaniesContracts/Edit', [
            'contract' => CompanyContract::with('company')->findOrFail($contract_id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $company_id
     * @param $contract_id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function update(Request $request, $company_id, $contract_id)
    {
        $this->authorize('create-company-contracts');

        $request->validate([
            'reference_number' => 'nullable|string|max:255|unique:company_contracts,reference_number,'.$contract_id,
            'contract_starts_at' => 'required|date',
            'contract_ends_at' => 'nullable|date',
            'contract_period_in_months' => 'nullable',
            'auto_renewal' => 'nullable|boolean',
            'trainees_count' => 'nullable',
            'trainee_salary' => 'nullable',
            'instructor_cost' => 'nullable',
            'company_reimbursement' => 'nullable',
            'notes' => 'nullable',
        ]);

        \DB::beginTransaction();
        $contract = CompanyContract::findOrFail($contract_id);

        if (!$request->reference_number || $request->reference_number==='') {
            $contract->reference_number = Numbering::getNewNumber(now()->format('dmY').'-');
            $contract->save();
        }

        $contract->update($request->except('_token'));
        \DB::commit();

        return $contract;
        //return redirect()->route('back.companies.contracts.show', [
        //    'company_id' => $company_id,
        //    'contract' => $contract_id,
        //]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $company_id
     * @param $contract_id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($company_id, $contract_id)
    {
        $this->authorize('delete-company-contracts');
        CompanyContract::findOrFail($contract_id)->delete();
        return redirect()->route('back.companies.show', $company_id);
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

        if ($files->first()->disk === 's3') {
            return redirect()->to($files->first()->getTemporaryUrl(now()->addMinutes(5), '', [
                //'ResponseContentType' => 'application/octet-stream',
            ]));
        } else {
            return response()->file($files->first()->getPath());
        }
    }

    /**
     * Attaches instructors to a company's contract.
     *
     * @param \Illuminate\Http\Request $request
     * @return Instructor[]
     */
    public function attachInstructor(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'company_contract_id' => 'required|exists:company_contracts,id',
        ]);

        $contract = CompanyContract::findOrFail($request->company_contract_id);
        $contract->instructors()->attach([$request->instructor_id]);

        $contract->load('instructors');

        return $contract;
    }

    /**
     * Detaches instructors to a company's contract.
     *
     * @param \Illuminate\Http\Request $request
     * @return Instructor[]
     */
    public function detachInstructor(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'company_contract_id' => 'required|exists:company_contracts,id',
        ]);

        $contract = CompanyContract::findOrFail($request->company_contract_id);
        $contract->instructors()->detach([$request->instructor_id]);

        $contract->load('instructors');

        return $contract;
    }

    public function storeAttachments($company_id, $contract_id, Request $request)
    {
        $contract = CompanyContract::where('id', $contract_id)
            ->where('company_id', $company_id)
            ->firstOrFail();

        // Check and save the files.
        $files = $request->file('files');
        if ($files) {
            foreach($files as $file) {
                $contract->addContractCopyAttachment($file);
            }
        }
        return redirect()->route('back.companies.contracts.show', [
            'company_id' => $company_id,
            'contract' => $contract_id,
        ]);
    }

    public function deleteAttachments($company_id, $contract_id)
    {
        $contract = CompanyContract::where('id', $contract_id)
            ->where('company_id', $company_id)
            ->firstOrFail();

        $files = $contract->clearMediaCollection('contract_copy');

        return redirect()->route('back.companies.contracts.show', [
            'company_id' => $contract->company_id,
            'contract' => $contract->id,
        ]);
    }

    /**
     * Export as Excel file.
     *
     * @param $company_id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function excel($company_id)
    {
        $company = Company::withTrashed()->findOrFail($company_id);
        Audit::create([
            'event' => 'companies.trainees.export.excel',
            'auditable_id' => auth()->user()->id,
            'auditable_type' => User::class,
            'new_values' => [],
        ]);
        return Excel::download(
            new CompanyTraineeExport($company_id),
            $company->name_ar.' - '.__('words.trainees').'.xlsx')
            ;
    }
}
