<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainingScheduleController extends Controller
{
    public function index()
    {
        return Inertia::render('TrainingSchedule/Index');
    }
}
