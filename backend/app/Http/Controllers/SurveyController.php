<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SurveyController extends Controller
{
    public function index()
    {
        $traineeSurvey = 'https://forms.gle/zH1GTLBeJuEuL2d28';
        $instructorsSurvey = 'https://forms.gle/8N6CZgjTMnVkxw5z7';

        if (auth()->user()->trainee) {
            $surveyLink = $traineeSurvey;
        } else {
            $surveyLink = $instructorsSurvey;
        }

        return Inertia::render('Survey/Index', [
            'surveyLink' => $surveyLink,
        ]);
    }
}
