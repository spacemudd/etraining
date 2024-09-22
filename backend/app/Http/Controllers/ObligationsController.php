<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ObligationsController extends Controller
{
    public function index()
    {
        return Inertia::render('Obligations/Index');
    }
}
