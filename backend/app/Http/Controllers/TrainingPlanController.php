<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainingPlanController extends Controller
{
    public function index()
    {
        return Inertia::render('TrainingPlan/Index');
    }
}
