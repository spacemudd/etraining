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
        $googleSheetId = AppSetting::where('name', 'google_sheet_id')->value('value');
        $googleSheetRange = AppSetting::where('name', 'google_sheet_range')->value('value');
        $googleSheetAlertEmail = AppSetting::where('name', 'google_sheet_alert_email')->value('value');
        $jsonKeyUploaded = AppSetting::first()?->getFirstMedia('google-service-json') !== null;

        return Inertia::render('Back/Settings/AppSettings/Index', [
            'gosi_monthly_requests' => $gosiMonthlyRequests,
            'google_sheet_id' => $googleSheetId,
            'google_sheet_range' => $googleSheetRange,
            'google_sheet_alert_email' => $googleSheetAlertEmail,
            'json_key_uploaded' => $jsonKeyUploaded,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'gosi_monthly_requests' => 'required|numeric|min:0',
            'google_sheet_id' => 'nullable|string',
            'google_sheet_range' => 'nullable|string',
            'google_sheet_alert_email' => 'nullable|string',
        ]);

        AppSetting::updateOrCreate(['name' => 'gosi_monthly_requests'], ['value' => $request->input('gosi_monthly_requests')]);
        AppSetting::updateOrCreate(['name' => 'google_sheet_id'], ['value' => $request->input('google_sheet_id')]);
        AppSetting::updateOrCreate(['name' => 'google_sheet_range'], ['value' => $request->input('google_sheet_range')]);
        AppSetting::updateOrCreate(['name' => 'google_sheet_alert_email'], ['value' => $request->input('google_sheet_alert_email')]);

        cache()->forget('gosi_monthly_requests_limit');

        return redirect()->route('back.settings.app.index')->with('success', 'Settings updated.');
    }

    public function deleteJsonKey()
    {
        $setting = AppSetting::first();
        if ($setting) {
            $setting->clearMediaCollection('google-service-json');
        }

        return back()->with('success', 'تم حذف المفتاح.');
    }
}
