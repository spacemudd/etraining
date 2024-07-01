<?php

namespace App\Http\Controllers;

use App\Models\Back\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;


class LogoFilesController extends Controller
{
    public function index(){

        return Inertia::render('Back/Companies/CompaniesLogo/Index', [
<<<<<<< HEAD
            'companies' => Company::with('logo_files')->paginate(20),
=======
            'companies' => Company::with('logo_files')->paginate(300),
>>>>>>> companies-paginator
        ]);
    }


}
