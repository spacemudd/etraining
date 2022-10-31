<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use Illuminate\Http\Request;

class SiteSearchController extends Controller
{
    /**
     * Search the site globally.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|max:200',
        ]);

        $trainees = Trainee::where('name', 'LIKE', '%'.$request->search.'%')
            ->orWhere('email', 'LIKE', '%'.$request->search.'%')
            ->orWhere('phone', 'LIKE', '%'.$request->search.'%')
            ->orWhere('identity_number', 'LIKE', '%'.$request->search.'%')
            ->with('company')
            ->withTrashed()
            ->take(30)
            ->get();

        if ($request->trainees) return $trainees;

        $companies = Company::where('name_ar', 'LIKE', '%'.$request->search.'%')->take(30)->get();

        if ($request->only_companies) {
            return $companies;
        }

        return $trainees->merge($companies);
    }
}
