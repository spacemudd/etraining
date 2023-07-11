<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\Resignation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CompanyResignationsController extends Controller
{
    /**
     *
     *
     * @param $compay_id
     * @return \Inertia\Response
     */
    public function create($compay_id)
    {
        return Inertia::render('Back/CompanyResignations/Create', [
            'company' => $company = Company::with('trainees')->findOrFail($compay_id),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'trainees.*.id' => 'required|exists:trainees,id',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();

        $resignation = Resignation::create([
            'date' => $request->date,
            'company_id' => $request->company_id,
        ]);

        foreach ($request->trainees as $trainee) {
            $resignation->trainees()->attach($trainee['id'], [
                'id' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
                'team_id' => $resignation->team_id,
            ]);
        }

        DB::commit();

        return redirect()->route('back.companies.show', $resignation->company_id);
    }
}
