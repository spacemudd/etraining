<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequirementsController extends Controller
{
    public function index()
    {
        return view('requirements');
    }
}
