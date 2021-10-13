<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ManagementRolesController extends Controller
{
    public function index()
    {
        return Inertia::render('ManagementRoles/Index');
    }
}
