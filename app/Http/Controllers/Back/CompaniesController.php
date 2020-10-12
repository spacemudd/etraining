<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\Instructor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view-companies');
        return Inertia::render('Back/Companies/Index', [
            'companies' => Company::paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create-companies');

        return Inertia::render('Back/Companies/Create');
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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'cr_number' => 'nullable|max:255',
            'contact_number' => 'nullable|string|max:255',
            'company_rep' => 'nullable|string|max:255',
            'company_rep_mobile' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $company = Company::create($request->except('_token'));

        return redirect()->route('back.companies.show', ['company' => $company])->with('success', __('words.created-successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        return Inertia::render('Back/Companies/Show', [
            'company' => Company::findOrFail($id),
            'instructors' => Instructor::get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        return Inertia::render('Back/Companies/Edit', [
            'company' => Company::findOrFail($id),
        ]);
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
        $company = Company::findOrFail($id);
        $company->update($request->except('_token'));
        return redirect()->route('back.companies.show', ['company' => $company])->with('success', __('words.updated-successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Company::findOrFail($id)->delete();
        return redirect()->route('back.companies.index');
    }
}
