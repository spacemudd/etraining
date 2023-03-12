<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompaniesNotificationController extends Controller
{
    /**
     * @param $company_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index($company_id)
    {
        $company = Company::findOrFail($company_id);
        return Inertia::render('Back/Companies/Notification/Index', compact('company'));
    }

    /**
     * @param $company_id
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function store($company_id, Request $request)
    {
        $request->validate([
            'body' => 'required|string|max:400',
        ]);

        return redirect()->route('back.companies.show', $company_id)
            ->with('success', 'Notification sent successfully!');
    }
}
