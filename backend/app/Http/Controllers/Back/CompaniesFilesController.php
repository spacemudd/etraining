<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Media;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Str;

class CompaniesFilesController extends Controller
{
    public function index($company_id)
    {
        $company = Company::withTrashed()->with('logo_files')
            ->findOrFail($company_id);

        return Inertia::render('Back/Companies/Files/Index',[
            'company' => $company,
        ]);
    }

    public function store($company_id)
    {
        $company = Company::withTrashed()->findOrFail($company_id);
        $company->addMediaFromRequest('logo_files')
            ->usingFileName(request()->file('logo_files')->hashName())
            ->toMediaCollection('logo_files');

        return redirect()->route('back.companies.files.index', $company->id);
    }

    public function show($company_id, $file)
    {
        $media = Media::findOrFail($file);
        if ($media->disk === 's3') {
            return redirect()->to($media->getTemporaryUrl(now()->addMinutes(5), '', [
                //'ResponseContentType' => 'application/octet-stream',
            ]));
        } else {
            return response()->file($media->getPath());
        }
    }

    public function destroy($company_id, $file)
    {
        $media = Media::findOrFail($file);
        $media->delete();
        return redirect()->route('back.companies.files.index', $company_id);
    }
}
