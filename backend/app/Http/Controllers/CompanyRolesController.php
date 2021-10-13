<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyRolesController extends Controller
{
    public function index()
    {
        return Inertia::render('CompanyRoles/Index');
    }
}
