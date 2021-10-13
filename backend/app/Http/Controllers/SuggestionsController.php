<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SuggestionsController extends Controller
{
    public function index()
    {
        return Inertia::render('Suggestions/Index');
    }
}
