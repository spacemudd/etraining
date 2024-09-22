<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainingPlanController extends Controller
{
    public function index()
    {
        return Inertia::render('Trainees/TrainingPlan/Index');
    }
}
