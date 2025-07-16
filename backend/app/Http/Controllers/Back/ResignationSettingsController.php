<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResignationSettingsController extends Controller
{
    public function index()
    {
        $defaultCcEmails = AppSetting::where('name', 'resignation_default_cc_emails')->value('value') ?? '';
        $defaultBccEmails = AppSetting::where('name', 'resignation_default_bcc_emails')->value('value') ?? '';

        return Inertia::render('Back/Settings/ResignationSettings/Index', [
            'default_cc_emails' => $defaultCcEmails,
            'default_bcc_emails' => $defaultBccEmails,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'default_cc_emails' => 'nullable|string|max:1000',
            'default_bcc_emails' => 'nullable|string|max:1000',
        ]);

        AppSetting::updateOrCreate(
            ['name' => 'resignation_default_cc_emails'],
            ['value' => $request->input('default_cc_emails', '')]
        );

        AppSetting::updateOrCreate(
            ['name' => 'resignation_default_bcc_emails'],
            ['value' => $request->input('default_bcc_emails', '')]
        );

        return redirect()->route('back.settings.resignation.index')->with('success', 'إعدادات البريد الإلكتروني للاستقالات تم تحديثها بنجاح.');
    }
} 