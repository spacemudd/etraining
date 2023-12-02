<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the trainee profile page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Trainees/Profile/Index', [
            'user' => auth()->user(),
            'trainee' => auth()->user()->trainee,
            'pending_withdraws' => auth()->user()->trainee->pending_withdraws,
        ]);
    }
}
