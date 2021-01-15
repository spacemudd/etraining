<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RolesController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Roles/Index', [
            'roles' => Role::with('permissions')->get(),
        ]);
    }
}
