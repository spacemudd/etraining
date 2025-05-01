<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppSettingsController extends Controller
{
    public function index()
    {
        $gosiMonthlyRequests = AppSetting::where('name', 'gosi_monthly_requests')->value('value');

        return Inertia::render('Back/Settings/AppSettings/Index', [
            'gosi_monthly_requests' => $gosiMonthlyRequests,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'gosi_monthly_requests' => 'required|numeric|min:0',
        ]);

        AppSetting::updateOrCreate(
            ['name' => 'gosi_monthly_requests'],
            ['value' => $request->input('gosi_monthly_requests')]
        );

        cache()->forget('gosi_monthly_requests_limit');

        return redirect()->route('back.settings.app.index')->with('success', 'Settings updated.');
    }
}
