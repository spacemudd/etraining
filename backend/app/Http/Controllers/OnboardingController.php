<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class OnboardingController extends Controller
{
    public function index()
    {
        return view('onboarding.index');
    }

    public function instructor()
    {
        return view('onboarding.instructor');
    }

    public function trainee()
    {
        return view('onboarding.trainee');
    }
}
