<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\ComplaintsSettings;
use App\Models\Back\SurveyLink;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComplaintsSettingsController extends Controller
{
    /**
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('edit-complaint-settings');

        return Inertia::render('Back/Settings/ComplaintsSettings', [
            'complaints_settings' => ComplaintsSettings::firstOrCreate(['team_id' => auth()->user()->currentTeam()->first()->id], [
                'team_id' => auth()->user()->currentTeam()->first()->id,
                'enabled' => false,
                'emails' => null,
            ])
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'option_emails' => 'required|string|max:255',
            'option_enabled' => 'required|boolean',
        ]);

        ComplaintsSettings::where('team_id', auth()->user()->currentTeam()->first()->id)
            ->update([
                'enabled' => $request->option_enabled,
                'emails' => $request->option_emails,
            ]);

        return redirect()->route('back.settings');
    }
}
