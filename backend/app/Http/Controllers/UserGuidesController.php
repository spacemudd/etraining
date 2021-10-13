<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class UserGuidesController extends Controller
{
    public function index()
    {
        return Inertia::render('UserGuides/Index');
    }
}
