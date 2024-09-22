<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class UserGuidesController extends Controller
{
    public function index()
    {
        $is_trainee = auth()->user()->trainee;
        return Inertia::render('UserGuides/Index', [
            'is_trainee' => $is_trainee,
        ]);
    }
}
