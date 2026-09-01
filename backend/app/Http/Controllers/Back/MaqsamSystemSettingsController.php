<?php

declare(strict_types=1);

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaqsamSystemSettingsController extends Controller
{
    public function index()
    {
        $baseUrl = AppSetting::where('name', 'maqsam_system_base_url')->value('value') ?? '';
        $accessKey = AppSetting::where('name', 'maqsam_system_access_key')->value('value') ?? '';
        $accessToken = AppSetting::where('name', 'maqsam_system_access_token')->value('value') ?? '';

        return Inertia::render('Back/Settings/MaqsamSystem/Index', [
            'base_url' => $baseUrl,
            'access_key' => $accessKey,
            'access_token' => $accessToken,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'base_url' => 'nullable|string|max:500',
            'access_key' => 'nullable|string|max:2000',
            'access_token' => 'nullable|string|max:2000',
        ]);

        AppSetting::updateOrCreate(
            ['name' => 'maqsam_system_base_url'],
            ['value' => $request->input('base_url', '')]
        );

        AppSetting::updateOrCreate(
            ['name' => 'maqsam_system_access_key'],
            ['value' => $request->input('access_key', '')]
        );

        AppSetting::updateOrCreate(
            ['name' => 'maqsam_system_access_token'],
            ['value' => $request->input('access_token', '')]
        );

        return redirect()->route('back.settings.maqsam-system.index')
            ->with('success', __('words.maqsam-system-settings-saved'));
    }
}
