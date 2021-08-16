<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\SurveyLink;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SurveyLinksController extends Controller
{
    /**
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('edit-survey-links');

        return Inertia::render('Back/Settings/SurveyLinks', [
            'instructor_survey_link' => SurveyLink::where('type', 'instructor')->first()->url ?? '',
            'trainee_survey_link' => SurveyLink::where('type', 'trainee')->first()->url ?? '',
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('edit-survey-links');

        $request->validate([
            'type' => 'required|string|max:100',
            'url' => 'nullable|string',
        ]);


        if ($request->type === 'instructor') {
            SurveyLink::where('type', 'instructor')->delete();

            if ($request->url) {
                $sl = new SurveyLink();
                $sl->type = 'instructor';
                $sl->url = $request->url;
                $sl->created_by_id = auth()->user()->id;
                $sl->save();
            }
        }

        if ($request->type === 'trainee') {
            SurveyLink::where('type', 'trainee')->delete();

            if ($request->url) {
                $sl = new SurveyLink();
                $sl->type = 'trainee';
                $sl->url = $request->url;
                $sl->created_by_id = auth()->user()->id;
                $sl->save();
            }
        }

        return redirect()->route('back.settings.survey-links.index');
    }

    public function landed()
    {
        if (auth()->user()->trainee) {
            if ($target = optional(SurveyLink::where('type', 'trainee')->first())->url) {
                return Inertia::render('Back/Survey/Index', [
                    'url' => $target,
                ]);
            }
        }

        if (auth()->user()->instructor) {
            if ($target = optional(SurveyLink::where('type', 'instructor')->first())->url) {
                return Inertia::render('Back/Survey/Index', [
                    'url' => $target,
                ]);
            }
        }

        return redirect()->route('dashboard');
    }
}
