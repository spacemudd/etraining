<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DisableWebsiteController extends Controller
{
    public function index()
    {
        $this->authorize('disable-website');
        return Inertia::render('Back/Settings/DisableWebsite', [
            'website_disabled' => auth()->user()->currentTeam->website_disabled,
            'website_disabled_notice' => auth()->user()->currentTeam->website_disabled_notice,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'website_disabled' => 'required|boolean',
            'website_disabled_notice' => 'required|string|max:500',
        ]);

        $team = Team::findOrFail(auth()->user()->current_team_id);
        $team->website_disabled = $request->website_disabled;
        $team->website_disabled_notice = $request->website_disabled_notice;
        $team->save();

        return redirect()->route('back.settings');
    }

    public function showDisabledPage()
    {
        if (!auth()->user()->team->website_disabled) {
            return redirect()->route('dashboard');
        }

        return view('disabled_website', [
            'message' => auth()->user()->team->website_disabled_notice,
        ]);
    }
}
