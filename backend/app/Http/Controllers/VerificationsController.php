<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class VerificationsController extends Controller
{
    public function index()
    {
        return view('auth/verify-code');
    }
}
