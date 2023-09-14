<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;

class CompaniesChasersController extends Controller
{
    /**
     *
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('manage-chasers');

        $chasers = User::role(auth()->user()->current_team_id.'_chasers')->get();

        return Inertia::render('Back/CompaniesChasers/Index', [
            'chasers' => $chasers,
        ]);
    }
}
