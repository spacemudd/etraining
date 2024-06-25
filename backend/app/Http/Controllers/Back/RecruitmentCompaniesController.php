<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\RecruitmentCompany;
use Inertia\Inertia;
use Illuminate\Http\Request;

class RecruitmentCompaniesController extends Controller
{
    public function index()
    {
        $recruitmentCompanies = RecruitmentCompany::with('createdBy')->paginate(20);
    
        return Inertia::render('Back/RecruitmentCompanies/Index', [
            'recruitmentCompanies' => $recruitmentCompanies,
        ]);
    }

    public function create(){
        return Inertia::render('Back/RecruitmentCompanies/Create');
    }

    public function store(Request $request){

        //validate
        $validatedRecruitment = $request->validate([
            'recruitment_name_ar' => 'required|string|max:255',
            'recruitment_name_en' => 'nullable|string|max:255'
        ]);

        //create
        $recruitmentCompany = RecruitmentCompany::create([
            'name' => $validatedRecruitment['recruitment_name_ar'],
            'name_en' => $validatedRecruitment['recruitment_name_en'],
            'created_by_id' => auth()->user()->id,
        ]);

        //redirect
        return redirect()->route('back.settings.recruitment-companies.index');
    }

    public function destroy($id)
{
    
    $recruitmentCompany = RecruitmentCompany::findOrFail($id);

    $recruitmentCompany->delete();

    return redirect()->route('back.settings.recruitment-companies.index');
}
}




