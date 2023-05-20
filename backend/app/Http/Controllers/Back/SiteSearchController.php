<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class SiteSearchController extends Controller
{
    /**
     * 10 characters: to show 10 neighbouring characters around the searched word
     * @var int
     */
    const BUFFER = 10;

    /**
     * Helper function to generate the model namespace
     * @return string
     */
    private function modelNamespacePrefix()
    {
        return app()->getNamespace() . 'Models\Back\\';
    }

    /**
     * Search the site globally.
     *
     * @param $search_string
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

        $companies = Company::where('name_ar', 'LIKE', '%'.$request->search.'%')
            ->take(30);

        if (auth()->user()->can('view-deleted-companies')) {
            $companies = $companies->withTrashed();
        }

        $companies = $companies->get();

        $related = $trainees->merge($companies);

        return $related;
    }
}
